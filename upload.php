<?php
/**
 * Upload Handler for Video Recordings
 * Stores video files on server and saves file paths to database
 */

// Set JSON content type
header('Content-Type: application/json');

try {
    // Database connection - adjust path as needed
    require_once('include/initialize.php');
    
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
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
    
    $registration_id = 'test';
    
    // Validate token and get registration info (skip for test uploads)
    if (!$is_test && !empty($token)) {
        $sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = ? AND EXPIRY_DATE > NOW()";
        $mydb->setQuery($sql);
        $mydb->bind_param('s', $token);
        $invitation = $mydb->loadSingleResult();
        
        if (!$invitation) {
            throw new Exception('Invalid or expired authentication token');
        }
        
        $registration_id = $invitation->REGISTRATIONID;
    }
    
    // Create upload directory structure
    $base_upload_dir = 'uploads';
    $registration_upload_dir = $base_upload_dir . '/' . $registration_id;
    
    // Create base directory if it doesn't exist
    if (!is_dir($base_upload_dir)) {
        if (!mkdir($base_upload_dir, 0755, true)) {
            throw new Exception('Failed to create base upload directory');
        }
    }
    
    // Create registration directory if it doesn't exist
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
    $allowed_extensions = ['webm', 'mp4'];
    if (!in_array($file_extension, $allowed_extensions)) {
        $file_extension = 'webm'; // Default to webm for browser recordings
    }
    
    // Generate unique filename using uniqid()
    $unique_filename = uniqid('recording_', true) . '.' . $file_extension;
    $file_path = $registration_upload_dir . '/' . $unique_filename;
    
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
    
    // Prepare recording metadata
    $conversation_turn = isset($_POST['conversationTurn']) ? (int)$_POST['conversationTurn'] : 1;
    $question_type = isset($_POST['questionType']) ? $_POST['questionType'] : 'initial_answer';
    $current_question = isset($_POST['currentEssentialQuestion']) ? (int)$_POST['currentEssentialQuestion'] : 0;
    $duration = isset($_POST['duration']) ? (float)$_POST['duration'] : 0;
    $transcript = isset($_POST['transcript']) ? $_POST['transcript'] : '';
    
    // Save recording info to database (skip for test uploads)
    if (!$is_test) {
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
                VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
        
        $mydb->setQuery($sql);
        $mydb->bind_param('sisidss', 
            $registration_id, 
            $current_question, 
            $file_path, 
            $duration, 
            $conversation_turn, 
            $question_type, 
            $transcript
        );
        
        if (!$mydb->executeQuery()) {
            // Delete the uploaded file if database insert fails
            unlink($file_path);
            throw new Exception('Failed to save recording info to database: ' . $mydb->getLastError());
        }
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Recording uploaded and saved successfully',
        'file_path' => $file_path,
        'file_size' => $file_size,
        'filename' => $unique_filename,
        'registration_id' => $registration_id
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>