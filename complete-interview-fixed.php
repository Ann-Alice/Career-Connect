<?php
// Simplified complete-interview.php with enhanced error handling
require_once('include/initialize.php');

// Enhanced error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
set_time_limit(300); // 5 minutes timeout
ini_set('memory_limit', '256M');

// Custom error handler to catch all errors
function customErrorHandler($severity, $message, $file, $line) {
    error_log("PHP Error: [$severity] $message in $file on line $line");
    if ($severity === E_ERROR || $severity === E_PARSE || $severity === E_CORE_ERROR || $severity === E_COMPILE_ERROR) {
        http_response_code(500);
        die(json_encode(['success' => false, 'error' => 'Server error occurred']));
    }
}
set_error_handler('customErrorHandler');

// Custom exception handler
function customExceptionHandler($exception) {
    error_log("Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Server exception: ' . $exception->getMessage()]));
}
set_exception_handler('customExceptionHandler');

try {
    error_log("Complete interview script started");
    
    // Set content type header
    header('Content-Type: application/json');
    
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Invalid request method');
    }
    
    // Get and validate input data
    $input = file_get_contents('php://input');
    if (empty($input)) {
        error_log("Empty input data received");
        throw new Exception('No data received');
    }
    
    error_log("Received input data length: " . strlen($input));
    
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg() . " - Input: " . substr($input, 0, 500));
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Extract and validate required parameters
    $token = isset($data['token']) ? trim($data['token']) : '';
    $registrationId = isset($data['registrationId']) ? trim($data['registrationId']) : '';
    
    if (empty($token) || empty($registrationId)) {
        error_log("Missing required parameters - token: " . (empty($token) ? 'empty' : 'present') . ", registrationId: " . (empty($registrationId) ? 'empty' : 'present'));
        throw new Exception('Missing required parameters');
    }
    
    // Validate registration ID is numeric
    if (!is_numeric($registrationId)) {
        error_log("Invalid registrationId format: " . $registrationId);
        throw new Exception('Invalid registration ID format');
    }
    
    error_log("Processing completion for token: $token, registrationId: $registrationId");
    
    // Check database connection
    if (!isset($mydb)) {
        error_log("Database connection not available");
        throw new Exception('Database connection failed');
    }
    
    // Validate token and registration
    $token_escaped = $mydb->escape_string($token);
    $registrationId_escaped = $mydb->escape_string($registrationId);
    
    $sql = "SELECT i.*, r.APPLICANTID, r.JOBID 
            FROM tblinterviewinvitations i
            JOIN tbljobregistration r ON i.REGISTRATIONID = r.REGISTRATIONID
            WHERE i.TOKEN = '{$token_escaped}' 
            AND i.REGISTRATIONID = '{$registrationId_escaped}' 
            AND i.EXPIRY_DATE > NOW()";
    
    $mydb->setQuery($sql);
    $invitation = $mydb->loadSingleResult();
    
    if (!$invitation) {
        error_log("Invalid or expired token for token: " . $token . ", registrationId: " . $registrationId);
        throw new Exception('Invalid or expired interview invitation');
    }
    
    error_log("Valid invitation found for applicantId: " . $invitation->APPLICANTID);
    
    // Check if interview is already completed
    $sql = "SELECT INTERVIEW_STATUS FROM tbljobregistration 
            WHERE REGISTRATIONID = '{$registrationId_escaped}'";
    $mydb->setQuery($sql);
    $current_status = $mydb->loadSingleResult();
    
    if ($current_status && $current_status->INTERVIEW_STATUS === 'Completed') {
        error_log("Interview already completed for registrationId: " . $registrationId);
        throw new Exception('Interview has already been completed');
    }
    
    // Extract optional data with safe defaults
    $chatMessages = isset($data['chatMessages']) && is_array($data['chatMessages']) ? $data['chatMessages'] : [];
    $aiResponses = isset($data['aiResponses']) && is_array($data['aiResponses']) ? $data['aiResponses'] : [];
    $totalQuestions = isset($data['totalQuestions']) ? (int)$data['totalQuestions'] : 0;
    $completedQuestions = isset($data['completedQuestions']) ? (int)$data['completedQuestions'] : 0;
    $candidateFeedback = isset($data['candidateFeedback']) ? $data['candidateFeedback'] : null;
    $selectedVoice = isset($data['selectedVoice']) ? $data['selectedVoice'] : 'female';
    
    error_log("Processing data - chatMessages: " . count($chatMessages) . ", aiResponses: " . count($aiResponses) . ", totalQuestions: $totalQuestions");
    
    // Begin transaction if supported
    $transaction_started = false;
    try {
        $mydb->setQuery("START TRANSACTION");
        $mydb->executeQuery();
        $transaction_started = true;
        error_log("Transaction started");
    } catch (Exception $e) {
        error_log("Could not start transaction: " . $e->getMessage());
    }
    
    try {
        // Update interview status first (most important)
        $sql = "UPDATE tbljobregistration 
                SET INTERVIEW_STATUS = 'Completed', 
                    INTERVIEW_COMPLETED_AT = NOW() 
                WHERE REGISTRATIONID = '{$registrationId_escaped}'";
        
        error_log("Executing status update query: " . $sql);
        $mydb->setQuery($sql);
        $updateResult = $mydb->executeQuery();
        
        if (!$updateResult) {
            throw new Exception('Failed to update interview status');
        }
        
        $affected_rows = $mydb->affected_rows();
        error_log("Status update successful - affected rows: " . $affected_rows);
        
        // Save interview results with safe JSON encoding
        try {
            $interviewResults = [
                'overall_score' => min(100, max(0, isset($data['totalQuestionsAnswered']) ? 
                    (int)$data['totalQuestionsAnswered'] / max(1, $totalQuestions) * 100 : 0)),
                'total_questions_answered' => isset($data['totalQuestionsAnswered']) ? (int)$data['totalQuestionsAnswered'] : 0,
                'interview_duration' => isset($data['interviewDuration']) ? (float)$data['interviewDuration'] : 0,
                'selected_voice' => $selectedVoice,
                'completed_at' => date('Y-m-d H:i:s'),
                'questions_data' => [
                    'total' => $totalQuestions,
                    'completed' => $completedQuestions
                ]
            ];
            
            // Add candidate feedback if provided (sanitized)
            if ($candidateFeedback && is_array($candidateFeedback)) {
                $sanitized_feedback = [];
                foreach ($candidateFeedback as $key => $value) {
                    if (is_string($value) || is_numeric($value)) {
                        $sanitized_feedback[$key] = is_string($value) ? substr($value, 0, 1000) : $value;
                    }
                }
                $interviewResults['candidate_feedback'] = $sanitized_feedback;
            }
            
            $resultsJson = json_encode($interviewResults, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            if ($resultsJson === false) {
                error_log("JSON encoding failed: " . json_last_error_msg());
                // Create minimal backup version
                $resultsJson = json_encode([
                    'overall_score' => $interviewResults['overall_score'],
                    'completed_at' => $interviewResults['completed_at'],
                    'status' => 'completed'
                ]);
            }
            
            if ($resultsJson) {
                $resultsJson_escaped = $mydb->escape_string($resultsJson);
                $sql = "UPDATE tbljobregistration 
                        SET INTERVIEW_RESULTS = '{$resultsJson_escaped}' 
                        WHERE REGISTRATIONID = '{$registrationId_escaped}'";
                
                error_log("Saving interview results - JSON length: " . strlen($resultsJson));
                $mydb->setQuery($sql);
                $mydb->executeQuery();
                error_log("Interview results saved successfully");
            }
            
        } catch (Exception $e) {
            error_log("Error saving interview results: " . $e->getMessage());
            // Continue without failing the whole process
        }
        
        // Commit transaction if started
        if ($transaction_started) {
            $mydb->setQuery("COMMIT");
            $mydb->executeQuery();
            error_log("Transaction committed successfully");
        }
        
        // Return success response
        error_log("Interview completion successful for registrationId: " . $registrationId);
        echo json_encode(['success' => true, 'message' => 'Interview completed successfully']);
        
    } catch (Exception $e) {
        // Rollback transaction if started
        if ($transaction_started) {
            try {
                $mydb->setQuery("ROLLBACK");
                $mydb->executeQuery();
                error_log("Transaction rolled back due to error");
            } catch (Exception $rollback_error) {
                error_log("Failed to rollback transaction: " . $rollback_error->getMessage());
            }
        }
        throw $e; // Re-throw to outer catch block
    }
    
} catch (Exception $e) {
    error_log("Exception in interview completion: " . $e->getMessage() . " - File: " . $e->getFile() . " - Line: " . $e->getLine());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Error $e) {
    error_log("Fatal error in interview completion: " . $e->getMessage() . " - File: " . $e->getFile() . " - Line: " . $e->getLine());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
}
?>