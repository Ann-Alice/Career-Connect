<?php
// Test upload endpoint with proper initialization
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Log all errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'test_upload_errors.log');

error_log("=== Test Upload Started ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . json_encode($_POST));
error_log("FILES Data: " . json_encode($_FILES));

try {
    // Define required constants
    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }
    if (!defined('SITE_ROOT')) {
        define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS . 'eris');
    }
    if (!defined('LIB_PATH')) {
        define('LIB_PATH', SITE_ROOT . DS . 'include');
    }

    // Include required files
    require_once('include/config.php');
    require_once('include/database.php');
    
    if (!isset($mydb) || !$mydb) {
        throw new Exception('Database connection failed');
    }
    
    error_log("Database connection successful");
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    }
    
    // Check token
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    if (empty($token)) {
        throw new Exception('No token provided');
    }
    error_log("Token received: " . $token);
    
    // Validate token
    $sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '" . $mydb->escape_string($token) . "' AND EXPIRY_DATE > NOW()";
    $mydb->setQuery($sql);
    $invitation = $mydb->loadSingleResult();
    
    if (!$invitation) {
        throw new Exception('Invalid or expired token');
    }
    error_log("Token validated successfully");
    
    // Check upload directory
    $upload_dir = 'uploads/interviews/' . $invitation->REGISTRATIONID;
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    if (!is_writable($upload_dir)) {
        throw new Exception('Upload directory is not writable');
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
    
    // Move uploaded file
    $filename = $_FILES['video']['name'];
    $filepath = $upload_dir . '/' . $filename;
    error_log("Moving file from " . $_FILES['video']['tmp_name'] . " to " . $filepath);
    
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $filepath)) {
        $error = error_get_last();
        throw new Exception('Failed to move uploaded file: ' . ($error ? $error['message'] : 'Unknown error'));
    }
    
    // Save to database
    $conversationTurn = isset($_POST['conversationTurn']) ? (int)$_POST['conversationTurn'] : 0;
    $questionType = isset($_POST['questionType']) ? $_POST['questionType'] : 'initial_answer';
    $currentEssentialQuestion = isset($_POST['currentEssentialQuestion']) ? (int)$_POST['currentEssentialQuestion'] : 0;
    $duration = isset($_POST['duration']) ? (float)$_POST['duration'] : 0;
    
    $sql = "INSERT INTO tblinterviewrecordings 
            (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE) 
            VALUES 
            ('" . $mydb->escape_string($invitation->REGISTRATIONID) . "', 
             " . $currentEssentialQuestion . ", 
             '" . $mydb->escape_string($filepath) . "', 
             " . $duration . ", 
             NOW(), 
             " . $conversationTurn . ", 
             '" . $mydb->escape_string($questionType) . "')";
    
    error_log("Database SQL: " . $sql);
    $mydb->setQuery($sql);
    
    if (!$mydb->executeQuery()) {
        // Delete the file if database insert fails
        unlink($filepath);
        throw new Exception('Failed to save recording info to database');
    }
    
    error_log("Upload completed successfully");
    
    // Clean output buffer and return success
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output: " . $output);
    }
    
    echo json_encode([
        'success' => true, 
        'filepath' => $filepath,
        'message' => 'Upload completed successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Error in test upload: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Clean output buffer and return error
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before error: " . $output);
    }
    
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'details' => 'Check test_upload_errors.log for more information'
    ]);
}

error_log("=== Test Upload Ended ===");
?> 