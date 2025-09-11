<?php
/**
 * Enhanced Video Recording Upload Handler
 * Properly saves recordings as files on server with path in database
 * Ensures proper file storage and database consistency
 */

// Disable error display to prevent JSON corruption
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Start output buffering to capture any unexpected output
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'upload_recording_errors.log');

error_log("=== Enhanced Recording Upload Started ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . json_encode(array_keys($_POST)));
error_log("FILES Data: " . json_encode(array_keys($_FILES)));

try {
    // Database connection
    require_once('include/initialize.php');
    
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    }
    
    // Check if video file was uploaded
    if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE', 
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $error_code = $_FILES['video']['error'] ?? UPLOAD_ERR_NO_FILE;
        $error_message = $upload_errors[$error_code] ?? 'Unknown upload error';
        throw new Exception('Upload error: ' . $error_message);
    }
    
    // Get and validate token
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $is_test = isset($_POST['test']) && $_POST['test'] === 'true';
    
    if (!$is_test && empty($token)) {
        throw new Exception('No authentication token provided');
    }
    
    $registration_id = 'test';
    
    // Validate token and get registration info (skip for test uploads)
    if (!$is_test) {
        $sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = ? AND EXPIRY_DATE > NOW()";
        $invitation = $mydb->loadSingleResultPrepared($sql, [$token], 's');
        
        if (!$invitation) {
            throw new Exception('Invalid or expired authentication token');
        }
        
        $registration_id = $invitation->REGISTRATIONID;
        error_log("Token validated for registration ID: " . $registration_id);
    }
    
    // Create upload directory structure
    $base_upload_dir = 'uploads/interviews';
    $registration_upload_dir = $base_upload_dir . '/' . $registration_id;
    
    if (!is_dir($base_upload_dir)) {
        if (!mkdir($base_upload_dir, 0755, true)) {
            throw new Exception('Failed to create base upload directory');
        }
    }
    
    if (!is_dir($registration_upload_dir)) {
        if (!mkdir($registration_upload_dir, 0755, true)) {
            throw new Exception('Failed to create registration upload directory');
        }
    }
    
    // Validate upload directory permissions
    if (!is_writable($registration_upload_dir)) {
        throw new Exception('Upload directory is not writable: ' . $registration_upload_dir);
    }
    
    // Generate unique filename with proper extension
    $original_filename = $_FILES['video']['name'];
    $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    
    // Validate file extension
    $allowed_extensions = ['webm', 'mp4', 'avi', 'mov'];
    if (!in_array($file_extension, $allowed_extensions)) {
        $file_extension = 'webm'; // Default to webm for browser recordings
    }
    
    $timestamp = time();
    $unique_filename = "recording_{$registration_id}_{$timestamp}.{$file_extension}";
    $file_path = $registration_upload_dir . '/' . $unique_filename;
    
    error_log("Moving uploaded file to: " . $file_path);
    
    // Move uploaded file to final location
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $file_path)) {
        $error = error_get_last();
        throw new Exception('Failed to move uploaded file: ' . ($error['message'] ?? 'Unknown error'));
    }
    
    // Verify file was moved and has content
    if (!file_exists($file_path) || filesize($file_path) === 0) {
        throw new Exception('File upload failed - file is empty or missing');
    }
    
    $file_size = filesize($file_path);
    error_log("File uploaded successfully: {$file_size} bytes");
    
    // Prepare recording metadata
    $conversation_turn = isset($_POST['conversationTurn']) ? (int)$_POST['conversationTurn'] : 1;
    $question_type = isset($_POST['questionType']) ? $_POST['questionType'] : 'essential_answer';
    $current_question = isset($_POST['currentEssentialQuestion']) ? (int)$_POST['currentEssentialQuestion'] : 0;
    $duration = isset($_POST['duration']) ? (float)$_POST['duration'] : 0;
    $transcript = isset($_POST['transcript']) ? $_POST['transcript'] : '';
    
    // Save recording info to database (skip for test uploads)
    if (!$is_test) {
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
                VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
        
        $params = [
            $registration_id,
            $current_question,
            $file_path,
            $duration,
            $conversation_turn,
            $question_type,
            $transcript
        ];
        $types = 'sisidiss';
        
        // Prepare and execute the statement
        $stmt = $mydb->prepareStatement($sql, $params, $types);
        if (!$stmt) {
            // Delete the uploaded file if database insert fails
            unlink($file_path);
            throw new Exception('Failed to prepare recording info statement: ' . $mydb->error_msg);
        }
        
        if (!$mydb->executePreparedStatement($stmt)) {
            // Delete the uploaded file if database insert fails
            unlink($file_path);
            throw new Exception('Failed to save recording info to database: ' . $mydb->error_msg);
        }
        
        mysqli_stmt_close($stmt);
        error_log("Recording metadata saved to database successfully");
    }
    
    // Clean output buffer and return success response
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output captured: " . $output);
    }
    
    $response = [
        'success' => true,
        'message' => 'Recording uploaded and saved successfully',
        'file_path' => $file_path,
        'file_size' => $file_size,
        'filename' => $unique_filename,
        'registration_id' => $registration_id,
        'conversation_turn' => $conversation_turn,
        'question_type' => $question_type,
        'duration' => $duration
    ];
    
    echo json_encode($response);
    error_log("Upload completed successfully for registration: " . $registration_id);
    
} catch (Exception $e) {
    error_log("Upload error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Clean output buffer and return error response
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before error: " . $output);
    }
    
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
}

error_log("=== Enhanced Recording Upload Ended ===");
?>