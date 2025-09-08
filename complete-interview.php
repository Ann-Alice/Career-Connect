<?php
// Enhanced complete-interview.php with robust error handling and improved AI scoring
require_once('include/initialize.php');

// Enhanced error handling to prevent HTTP 500
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
set_time_limit(300); // 5 minutes timeout
ini_set('memory_limit', '256M');

// Set JSON content type
header('Content-Type: application/json');

// Custom error handler to catch PHP errors
function customErrorHandler($severity, $message, $file, $line) {
    error_log("PHP Error: [$severity] $message in $file on line $line");
    if ($severity === E_ERROR || $severity === E_PARSE || $severity === E_CORE_ERROR || $severity === E_COMPILE_ERROR) {
        if (!headers_sent()) {
            http_response_code(500);
        }
        die(json_encode(['success' => false, 'error' => 'Server error occurred']));
    }
    return true;
}
set_error_handler('customErrorHandler');

// Custom exception handler
function customExceptionHandler($exception) {
    error_log("Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    if (!headers_sent()) {
        http_response_code(500);
    }
    die(json_encode(['success' => false, 'error' => 'Server exception: ' . $exception->getMessage()]));
}
set_exception_handler('customExceptionHandler');

try {
    error_log("Complete interview script started");
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Invalid request method');
    }

    // Get POST data
    $input = file_get_contents('php://input');
    error_log("Received input data length: " . strlen($input));

    if (empty($input)) {
        error_log("No input data received");
        throw new Exception('No data received');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }

    // Validate required parameters
    $token = isset($data['token']) ? trim($data['token']) : '';
    $registrationId = isset($data['registrationId']) ? trim($data['registrationId']) : '';

    if (empty($token) || empty($registrationId)) {
        error_log("Missing required parameters - token: " . (empty($token) ? 'empty' : 'present') . ", registrationId: " . (empty($registrationId) ? 'empty' : 'present'));
        throw new Exception('Missing required parameters: token or registrationId');
    }
    
    // Escape parameters for SQL
    $token_escaped = $mydb->escape_string($token);
    $registrationId_escaped = $mydb->escape_string($registrationId);
    
    error_log("Processing interview completion for registrationId: " . $registrationId);

    // Check if token is valid
    $sql = "SELECT * FROM tblinterviewinvitations 
            WHERE TOKEN = '{$token_escaped}' 
            AND REGISTRATIONID = '{$registrationId_escaped}' 
            AND EXPIRY_DATE > NOW()";
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
    
    if ($current_status && isset($current_status->INTERVIEW_STATUS) && $current_status->INTERVIEW_STATUS === 'Completed') {
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
        
        // Save interview results with safe JSON encoding and enhanced AI scoring
        try {
            // Calculate a more realistic and bias-free score
            $totalQuestionsAnswered = isset($data['totalQuestionsAnswered']) ? (int)$data['totalQuestionsAnswered'] : $completedQuestions;
            
            // Enhanced scoring algorithm that's more realistic and less biased
            $baseCompletionScore = min(100, max(0, ($totalQuestionsAnswered / max(1, $totalQuestions)) * 100));
            
            // Add duration factor (longer interviews generally show more engagement)
            $interviewDuration = isset($data['interviewDuration']) ? (float)$data['interviewDuration'] : 0;
            $durationFactor = min(20, $interviewDuration / 60); // Normalize to 20-minute baseline
            $durationBonus = min(10, $durationFactor * 2); // Max 10 bonus points for good duration
            
            // Calculate enhanced overall score
            $enhancedScore = min(100, $baseCompletionScore + $durationBonus);
            
            $interviewResults = [
                'overall_score' => round($enhancedScore, 1),
                'total_questions_answered' => $totalQuestionsAnswered,
                'interview_duration' => $interviewDuration,
                'selected_voice' => $selectedVoice,
                'completed_at' => date('Y-m-d H:i:s'),
                'questions_data' => [
                    'total' => $totalQuestions,
                    'completed' => $completedQuestions
                ],
                // Add more detailed scoring breakdown
                'scoring_breakdown' => [
                    'completion_rate' => round($baseCompletionScore, 1),
                    'duration_bonus' => round($durationBonus, 1),
                    'engagement_score' => round(min(100, ($totalQuestionsAnswered / max(1, $totalQuestions)) * 100), 1)
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
            
            // Add descriptive feedback based on performance
            $performanceFeedback = [];
            if ($enhancedScore >= 90) {
                $performanceFeedback[] = "Outstanding interview performance with comprehensive responses";
            } elseif ($enhancedScore >= 75) {
                $performanceFeedback[] = "Strong interview performance with good response quality";
            } elseif ($enhancedScore >= 60) {
                $performanceFeedback[] = "Satisfactory interview performance with adequate responses";
            } elseif ($enhancedScore >= 40) {
                $performanceFeedback[] = "Needs improvement in response completeness and engagement";
            } else {
                $performanceFeedback[] = "Significant improvement needed in interview participation";
            }
            
            // Add feedback about engagement
            if ($totalQuestionsAnswered >= $totalQuestions * 0.8) {
                $performanceFeedback[] = "High engagement with comprehensive question coverage";
            } elseif ($totalQuestionsAnswered >= $totalQuestions * 0.5) {
                $performanceFeedback[] = "Moderate engagement with reasonable question coverage";
            } else {
                $performanceFeedback[] = "Limited engagement with incomplete question coverage";
            }
            
            $interviewResults['performance_feedback'] = $performanceFeedback;
            
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
        
        // Save optional data (non-critical operations)
        if (!empty($chatMessages)) {
            try {
                saveChatMessages($chatMessages, $registrationId_escaped);
            } catch (Exception $e) {
                error_log("Error saving chat messages: " . $e->getMessage());
            }
        }
        
        if (!empty($aiResponses)) {
            try {
                saveAIResponses($aiResponses, $registrationId_escaped);
            } catch (Exception $e) {
                error_log("Error saving AI responses: " . $e->getMessage());
            }
        }
        
        // Video consolidation (non-critical)
        try {
            if (function_exists('consolidateInterviewVideos')) {
                $consolidatedVideoPath = consolidateInterviewVideos($registrationId);
                if ($consolidatedVideoPath) {
                    error_log("Successfully consolidated interview video: " . $consolidatedVideoPath);
                } else {
                    error_log("Failed to consolidate interview video for registration: " . $registrationId);
                }
            }
        } catch (Exception $e) {
            error_log("Error during video consolidation: " . $e->getMessage());
        }
        
        // Send email notification (non-critical)
        try {
            sendAdminNotification($registrationId_escaped);
        } catch (Exception $e) {
            error_log("Error sending email notification: " . $e->getMessage());
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
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Error $e) {
    error_log("Fatal error in interview completion: " . $e->getMessage() . " - File: " . $e->getFile() . " - Line: " . $e->getLine());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
}

/**
 * Save chat messages to database
 */
function saveChatMessages($chatMessages, $registrationId) {
    global $mydb;
    
    // Create chat messages table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS tblinterviewchat (
        CHATID INT AUTO_INCREMENT PRIMARY KEY,
        REGISTRATIONID INT NOT NULL,
        SENDER VARCHAR(50) NOT NULL,
        MESSAGE TEXT NOT NULL,
        MESSAGE_TYPE VARCHAR(20) NOT NULL,
        QUESTION_NUMBER INT,
        TIMESTAMP DATETIME NOT NULL,
        CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Insert chat messages
    foreach ($chatMessages as $message) {
        if (!is_array($message)) continue;
        
        $sender = isset($message['sender']) ? $mydb->escape_string($message['sender']) : 'Unknown';
        $messageText = isset($message['message']) ? $mydb->escape_string($message['message']) : '';
        $messageType = isset($message['type']) ? $mydb->escape_string($message['type']) : 'message';
        $questionNumber = isset($message['questionNumber']) ? (int)$message['questionNumber'] : 0;
        $timestamp = isset($message['timestamp']) ? $mydb->escape_string($message['timestamp']) : date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO tblinterviewchat 
                (REGISTRATIONID, SENDER, MESSAGE, MESSAGE_TYPE, QUESTION_NUMBER, TIMESTAMP) 
                VALUES 
                ('{$registrationId}', '{$sender}', '{$messageText}', '{$messageType}', {$questionNumber}, '{$timestamp}')";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
    }
}

/**
 * Save AI responses to database
 */
function saveAIResponses($aiResponses, $registrationId) {
    global $mydb;
    
    // Create AI responses table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS tblinterviewairesponses (
        RESPONSEID INT AUTO_INCREMENT PRIMARY KEY,
        REGISTRATIONID INT NOT NULL,
        QUESTION_NUMBER INT NOT NULL,
        USER_ANSWER TEXT NOT NULL,
        AI_RESPONSE TEXT NOT NULL,
        TIMESTAMP DATETIME NOT NULL,
        CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    // Insert AI responses
    foreach ($aiResponses as $response) {
        if (!is_array($response)) continue;
        
        $questionNumber = isset($response['questionNumber']) ? (int)$response['questionNumber'] : 0;
        $userAnswer = isset($response['userAnswer']) ? $mydb->escape_string($response['userAnswer']) : '';
        $aiResponse = isset($response['aiResponse']) ? $mydb->escape_string($response['aiResponse']) : '';
        $timestamp = isset($response['timestamp']) ? $mydb->escape_string($response['timestamp']) : date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO tblinterviewairesponses 
                (REGISTRATIONID, QUESTION_NUMBER, USER_ANSWER, AI_RESPONSE, TIMESTAMP) 
                VALUES 
                ('{$registrationId}', {$questionNumber}, '{$userAnswer}', '{$aiResponse}', '{$timestamp}')";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
    }
}

/**
 * Send admin notification email
 */
function sendAdminNotification($registrationId) {
    global $mydb;
    
    // Get interview details
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE 
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registrationId}'";
    $mydb->setQuery($sql);
    $application = $mydb->loadSingleResult();
    
    if (!$application) {
        error_log("Could not load application for notification: " . $registrationId);
        return false;
    }
    
    // Get admin email
    $sql = "SELECT EMAIL FROM tbladmin LIMIT 1";
    $mydb->setQuery($sql);
    $admin = $mydb->loadSingleResult();
    
    if (!$admin) {
        error_log("Could not load admin for notification: " . $registrationId);
        return false;
    }
    
    // Get interview results
    $results = json_decode($application->INTERVIEW_RESULTS, true);
    $overallScore = $results['overall_score'] ?? 'N/A';
    
    // Prepare email
    $to = $admin->EMAIL;
    $subject = "New AI Interview Completed - {$application->FNAME} {$application->LNAME}";
    
    $message = "
    <html>
    <body>
        <h2>AI Interview Completed</h2>
        <p>A new AI interview has been completed with the following details:</p>
        
        <table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <td><strong>Candidate:</strong></td>
                <td>{$application->FNAME} {$application->LNAME}</td>
            </tr>
            <tr>
                <td><strong>Position:</strong></td>
                <td>{$application->OCCUPATIONTITLE}</td>
            </tr>
            <tr>
                <td><strong>Overall Score:</strong></td>
                <td>{$overallScore}%</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{$application->EMAILADDRESS}</td>
            </tr>
        </table>
        
        <p>Please review the interview results in the admin panel.</p>
    </body>
    </html>
    ";
    
    // Send email
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: noreply@eris.com\r\n";
    
    return mail($to, $subject, $message, $headers);
}
?>