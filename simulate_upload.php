<?php
// Simulate the upload process to see what's happening
require_once('include/initialize.php');

// Create a test file
$test_content = "This is a test recording file content";
$base_upload_dir = 'uploads/interviews';
$registration_id = '3';
$registration_upload_dir = $base_upload_dir . '/' . $registration_id;

// Create directories if they don't exist
if (!is_dir($base_upload_dir)) {
    mkdir($base_upload_dir, 0755, true);
}
if (!is_dir($registration_upload_dir)) {
    mkdir($registration_upload_dir, 0755, true);
}

$timestamp = time();
$unique_filename = "recording_{$registration_id}_{$timestamp}.webm";
$file_path = $registration_upload_dir . '/' . $unique_filename;

// Write test file
file_put_contents($file_path, $test_content);
echo "Test file created: " . $file_path . "\n";

// Try to insert into database using the same method as upload-recording-enhanced.php
$sql = "INSERT INTO tblinterviewrecordings 
        (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";

$params = [
    $registration_id,  // REGISTRATIONID
    1,                 // QUESTION_NUMBER
    $file_path,        // FILE_PATH
    15.5,              // DURATION
    1,                 // CONVERSATION_TURN
    'test',            // QUESTION_TYPE
    'Test transcript'  // TRANSCRIPT
];
$types = 'sisidiss';

// Prepare and execute the statement
$stmt = $mydb->prepareStatement($sql, $params, $types);
if (!$stmt) {
    echo "Failed to prepare statement: " . $mydb->error_msg . "\n";
    unlink($file_path);
    exit(1);
}

if (!$mydb->executePreparedStatement($stmt)) {
    echo "Failed to execute statement: " . $mydb->error_msg . "\n";
    unlink($file_path);
    mysqli_stmt_close($stmt);
    exit(1);
}

mysqli_stmt_close($stmt);
echo "Recording inserted into database successfully.\n";

// Check if it was inserted
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = '3' ORDER BY RECORDED_AT DESC LIMIT 1";
$mydb->setQuery($sql);
$recording = $mydb->loadSingleResult();

if ($recording) {
    echo "Latest recording for registration ID 3:\n";
    echo "- Recording ID: " . $recording->RECORDING_ID . "\n";
    echo "- File Path: " . $recording->FILE_PATH . "\n";
    echo "- Duration: " . $recording->DURATION . "\n";
    echo "- Recorded At: " . $recording->RECORDED_AT . "\n";
} else {
    echo "No recording found.\n";
}
?>