<?php
// Simplified upload script that always returns proper JSON
ini_set('display_errors', 0); // Disable error display to prevent output corruption
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Log all errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'simple_upload_working_errors.log');

error_log("=== Simple Upload Working Started ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . json_encode($_POST));
error_log("FILES Data: " . json_encode($_FILES));

try {
    // Simple database connection
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'erisdb';
    $port = 4306;
    
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    if (!$conn) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }
    error_log("Database connection successful");
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    }
    
    // Check token (skip for test uploads)
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    $isTest = isset($_POST['test']) && $_POST['test'] === 'true';
    
    if (!$isTest && empty($token)) {
        throw new Exception('No token provided');
    }
    error_log("Token received: " . $token . ", Test mode: " . ($isTest ? 'true' : 'false'));
    
    $registrationId = 'test';
    
    if (!$isTest) {
        // Validate token
        $token = mysqli_real_escape_string($conn, $token);
        $sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '$token' AND EXPIRY_DATE > NOW()";
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($conn));
        }
        
        $invitation = mysqli_fetch_object($result);
        if (!$invitation) {
            throw new Exception('Invalid or expired token');
        }
        error_log("Token validated successfully");
        $registrationId = $invitation->REGISTRATIONID;
    }
    
    // Enhanced file handling with proper directory structure
    $base_upload_dir = 'uploads/interviews';
    $registration_upload_dir = $base_upload_dir . '/' . $registrationId;
    
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
    
    // Check if video file was uploaded
    if (!isset($_FILES['video'])) {
        throw new Exception('No video file uploaded');
    }
    
    if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        $errorMessage = isset($uploadErrors[$_FILES['video']['error']]) ? 
            $uploadErrors[$_FILES['video']['error']] : 'Unknown upload error';
        throw new Exception('Upload error: ' . $errorMessage);
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
    $unique_filename = "recording_{$registrationId}_{$timestamp}.{$file_extension}";
    $filepath = $registration_upload_dir . '/' . $unique_filename;
    error_log("Moving file from " . $_FILES['video']['tmp_name'] . " to " . $filepath);
    
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $filepath)) {
        $error = error_get_last();
        throw new Exception('Failed to move uploaded file: ' . ($error ? $error['message'] : 'Unknown error'));
    }
    
    // Save to database (skip for test uploads)
    if (!$isTest) {
        $conversationTurn = isset($_POST['conversationTurn']) ? (int)$_POST['conversationTurn'] : 0;
        $questionType = isset($_POST['questionType']) ? $_POST['questionType'] : 'initial_answer';
        $currentEssentialQuestion = isset($_POST['currentEssentialQuestion']) ? (int)$_POST['currentEssentialQuestion'] : 0;
        $duration = isset($_POST['duration']) ? (float)$_POST['duration'] : 0;
        $transcript = isset($_POST['transcript']) ? $_POST['transcript'] : '';
        
        $registrationId_escaped = mysqli_real_escape_string($conn, $registrationId);
        $filepath_escaped = mysqli_real_escape_string($conn, $filepath);
        $questionType_escaped = mysqli_real_escape_string($conn, $questionType);
        $transcript_escaped = mysqli_real_escape_string($conn, $transcript);
        
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
                VALUES 
                ('$registrationId_escaped', $currentEssentialQuestion, '$filepath_escaped', $duration, NOW(), $conversationTurn, '$questionType_escaped', '$transcript_escaped')";
        
        error_log("Database SQL: " . $sql);
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            $error = mysqli_error($conn);
            // Delete the file if database insert fails
            unlink($filepath);
            throw new Exception('Failed to save recording info to database: ' . $error);
        }
    }
    
    error_log("Upload completed successfully");
    
    // Clean output buffer and return success
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output: " . $output);
    }
    
    $response = [
        'success' => true, 
        'filepath' => $filepath,
        'message' => 'Upload completed successfully',
        'conversationTurn' => $conversationTurn,
        'questionType' => $questionType,
        'duration' => $duration
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error in simple upload working: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Clean output buffer and return error
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before error: " . $output);
    }
    
    $response = [
        'success' => false, 
        'error' => $e->getMessage(),
        'details' => 'Check simple_upload_working_errors.log for more information'
    ];
    
    echo json_encode($response);
}

mysqli_close($conn);
error_log("=== Simple Upload Working Ended ===");
?> 