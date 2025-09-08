<?php
// Disable error display to prevent output before JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Set maximum upload size
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
ini_set('max_execution_time', 300);

// Start output buffering to catch any unexpected output
ob_start();

// Set JSON content type header
header('Content-Type: application/json');

// Test JSON output capability
if (isset($_GET['test'])) {
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output in test: " . $output);
    }
    die(json_encode(['success' => true, 'message' => 'JSON test successful']));
}

// Define required constants before including files
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS . 'eris');
}
if (!defined('LIB_PATH')) {
    define('LIB_PATH', SITE_ROOT . DS . 'include');
}

require_once('include/initialize.php');

// Test database connection
try {
    if (!isset($mydb) || !$mydb) {
        error_log("Database connection not available");
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("Unexpected output before JSON: " . $output);
        }
        die(json_encode(['success' => false, 'error' => 'Database connection not available']));
    }
    error_log("Database connection successful");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before JSON: " . $output);
    }
    die(json_encode(['success' => false, 'error' => 'Database connection error']));
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before JSON: " . $output);
    }
    die(json_encode(['success' => false, 'error' => 'Invalid request method']));
}

// Validate token
$token = isset($_POST['token']) ? $_POST['token'] : '';
error_log("Token received: " . $token);

if (empty($token)) {
    error_log("Empty token received");
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before JSON: " . $output);
    }
    die(json_encode(['success' => false, 'error' => 'Invalid token']));
}

// Check if token is valid
$sql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '{$token}' AND EXPIRY_DATE > NOW()";
error_log("SQL Query: " . $sql);
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();

if (!$invitation) {
    error_log("Token validation failed - no invitation found or token expired");
    // Let's also check if the token exists but is expired
    $checkSql = "SELECT * FROM tblinterviewinvitations WHERE TOKEN = '{$token}'";
    $mydb->setQuery($checkSql);
    $checkInvitation = $mydb->loadSingleResult();
    if ($checkInvitation) {
        error_log("Token exists but expired. Expiry date: " . $checkInvitation->EXPIRY_DATE);
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("Unexpected output before JSON: " . $output);
        }
        die(json_encode(['success' => false, 'error' => 'Token expired']));
    } else {
        error_log("Token not found in database");
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("Unexpected output before JSON: " . $output);
        }
        die(json_encode(['success' => false, 'error' => 'Invalid token']));
    }
}

error_log("Token validation successful for registration ID: " . $invitation->REGISTRATIONID);

// Create upload directory if it doesn't exist
$upload_dir = 'uploads/interviews/' . $invitation->REGISTRATIONID;
error_log("Upload directory: " . $upload_dir);

if (!file_exists($upload_dir)) {
    error_log("Creating upload directory: " . $upload_dir);
    if (!mkdir($upload_dir, 0777, true)) {
        error_log("Failed to create upload directory: " . $upload_dir);
        die(json_encode(['success' => false, 'error' => 'Failed to create upload directory']));
    }
}

// Check if directory is writable
if (!is_writable($upload_dir)) {
    error_log("Upload directory is not writable: " . $upload_dir);
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before JSON: " . $output);
    }
    die(json_encode(['success' => false, 'error' => 'Upload directory is not writable']));
}

// Handle file upload
error_log("Upload request received: " . json_encode($_POST));
error_log("Files: " . json_encode($_FILES));

if (isset($_FILES['video'])) {
    error_log("Video file received: " . json_encode($_FILES['video']));
    
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
        error_log("Upload error: " . $errorMessage);
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("Unexpected output before JSON: " . $output);
        }
        die(json_encode(['success' => false, 'error' => 'Upload error: ' . $errorMessage]));
    }
    $conversationTurn = isset($_POST['conversationTurn']) ? (int)$_POST['conversationTurn'] : 0;
    $questionType = isset($_POST['questionType']) ? $_POST['questionType'] : 'initial_answer';
    $currentEssentialQuestion = isset($_POST['currentEssentialQuestion']) ? (int)$_POST['currentEssentialQuestion'] : 0;
    $duration = isset($_POST['duration']) ? (float)$_POST['duration'] : 0;
    
    // Use the filename provided by the frontend
    $filename = $_FILES['video']['name'];
    $filepath = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    error_log("Moving uploaded file from " . $_FILES['video']['tmp_name'] . " to " . $filepath);
    if (move_uploaded_file($_FILES['video']['tmp_name'], $filepath)) {
        // Save recording info to database
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE) 
                VALUES 
                ('{$invitation->REGISTRATIONID}', {$currentEssentialQuestion}, '{$filepath}', {$duration}, NOW(), {$conversationTurn}, '{$questionType}')";
        $mydb->setQuery($sql);
        
        if ($mydb->executeQuery()) {
            error_log("Recording saved successfully: {$filepath}");
            $output = ob_get_clean();
            if (!empty($output)) {
                error_log("Unexpected output before JSON: " . $output);
            }
            echo json_encode(['success' => true, 'filepath' => $filepath]);
        } else {
            error_log("Database insert failed for recording: " . $mydb->getLastError());
            unlink($filepath); // Delete the file if database insert fails
            $output = ob_get_clean();
            if (!empty($output)) {
                error_log("Unexpected output before JSON: " . $output);
            }
            echo json_encode(['success' => false, 'error' => 'Failed to save recording info']);
        }
    } else {
        error_log("Failed to move uploaded file. Error: " . error_get_last()['message']);
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("Unexpected output before JSON: " . $output);
        }
        echo json_encode(['success' => false, 'error' => 'Failed to save video file: ' . error_get_last()['message']]);
    }
} else {
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output before JSON: " . $output);
    }
    echo json_encode(['success' => false, 'error' => 'No video file uploaded']);
}
?> 