<?php
// Simple upload script without complex initialization
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Log all errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'simple_upload_errors.log');

error_log("=== Simple Upload Started ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . json_encode($_POST));
error_log("FILES Data: " . json_encode($_FILES));

try {
    // Simple database connection without complex initialization
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
    
    // Check token
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    if (empty($token)) {
        throw new Exception('No token provided');
    }
    error_log("Token received: " . $token);
    
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
    
    $registrationId = mysqli_real_escape_string($conn, $invitation->REGISTRATIONID);
    $filepath = mysqli_real_escape_string($conn, $filepath);
    $questionType = mysqli_real_escape_string($conn, $questionType);
    
    $sql = "INSERT INTO tblinterviewrecordings 
            (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE) 
            VALUES 
            ('$registrationId', $currentEssentialQuestion, '$filepath', $duration, NOW(), $conversationTurn, '$questionType')";
    
    error_log("Database SQL: " . $sql);
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        $error = mysqli_error($conn);
        // Delete the file if database insert fails
        unlink($filepath);
        
        // Check if it's a table doesn't exist error
        if (strpos($error, "doesn't exist") !== false) {
            throw new Exception('Database table missing. Please run setup-interview-tables.php first: ' . $error);
        } else {
            throw new Exception('Failed to save recording info to database: ' . $error);
        }
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
    error_log("Error in simple upload: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Clean output buffer and return error
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before error: " . $output);
    }
    
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'details' => 'Check simple_upload_errors.log for more information'
    ]);
}

mysqli_close($conn);
error_log("=== Simple Upload Ended ===");
?> 