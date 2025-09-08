<?php
// Save consolidated interview video to database
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', 'consolidated_video_errors.log');

error_log("=== Save Consolidated Video Started ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . json_encode($_POST));
error_log("FILES Data: " . json_encode($_FILES));

try {
    // Database connection
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
    
    $registrationId = $invitation->REGISTRATIONID;
    
    // Check upload directory for consolidated videos
    $consolidated_dir = 'uploads/interviews/consolidated';
    if (!file_exists($consolidated_dir)) {
        if (!mkdir($consolidated_dir, 0777, true)) {
            throw new Exception('Failed to create consolidated upload directory');
        }
    }
    
    if (!is_writable($consolidated_dir)) {
        throw new Exception('Consolidated upload directory is not writable');
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
    $filepath = $consolidated_dir . '/' . $filename;
    error_log("Moving consolidated video from " . $_FILES['video']['tmp_name'] . " to " . $filepath);
    
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $filepath)) {
        $error = error_get_last();
        throw new Exception('Failed to move uploaded consolidated video: ' . ($error ? $error['message'] : 'Unknown error'));
    }
    
    // Get additional data
    $duration = isset($_POST['duration']) ? (float)$_POST['duration'] : 0;
    $videoType = isset($_POST['videoType']) ? $_POST['videoType'] : 'consolidated';
    
    // Escape data for SQL
    $registrationId = mysqli_real_escape_string($conn, $registrationId);
    $filename = mysqli_real_escape_string($conn, $filename);
    $videoType = mysqli_real_escape_string($conn, $videoType);
    
    // Check if consolidated video already exists for this registration
    $checkSql = "SELECT VIDEOID FROM tblinterviewvideos WHERE REGISTRATIONID = '$registrationId'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        // Update existing record
        $sql = "UPDATE tblinterviewvideos 
                SET VIDEO_PATH = '$filename', CREATED_AT = NOW()
                WHERE REGISTRATIONID = '$registrationId'";
        error_log("Updating existing consolidated video record");
    } else {
        // Insert new record
        $sql = "INSERT INTO tblinterviewvideos (REGISTRATIONID, VIDEO_PATH, CREATED_AT) 
                VALUES ('$registrationId', '$filename', NOW())";
        error_log("Creating new consolidated video record");
    }
    
    error_log("Database SQL: " . $sql);
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        $error = mysqli_error($conn);
        // Delete the file if database insert/update fails
        unlink($filepath);
        throw new Exception('Failed to save consolidated video info to database: ' . $error);
    }
    
    error_log("Consolidated video upload completed successfully");
    
    // Clean output buffer and return success
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output: " . $output);
    }
    
    $response = [
        'success' => true, 
        'filepath' => $filepath,
        'filename' => $filename,
        'message' => 'Consolidated video uploaded successfully',
        'duration' => $duration,
        'type' => $videoType,
        'registrationId' => $registrationId
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error in save consolidated video: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Clean output buffer and return error
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before error: " . $output);
    }
    
    $response = [
        'success' => false, 
        'error' => $e->getMessage(),
        'details' => 'Check consolidated_video_errors.log for more information'
    ];
    
    echo json_encode($response);
}

if (isset($conn)) {
    mysqli_close($conn);
}
error_log("=== Save Consolidated Video Ended ===");
?>