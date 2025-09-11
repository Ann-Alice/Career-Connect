<?php
// Test upload recording script
require_once('include/initialize.php');

// Create a test file
$test_content = "This is a test recording file";
$test_file = 'uploads/interviews/3/test_recording.webm';
file_put_contents($test_file, $test_content);

echo "Test file created: " . $test_file . "\n";

// Try to insert into database
$sql = "INSERT INTO tblinterviewrecordings 
        (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";

$params = [
    '3',           // REGISTRATIONID
    1,             // QUESTION_NUMBER
    $test_file,    // FILE_PATH
    15.5,          // DURATION
    1,             // CONVERSATION_TURN
    'test',        // QUESTION_TYPE
    'Test transcript' // TRANSCRIPT
];
$types = 'sisidiss';

// Prepare and execute the statement
$stmt = $mydb->prepareStatement($sql, $params, $types);
if (!$stmt) {
    echo "Failed to prepare statement: " . $mydb->error_msg . "\n";
    unlink($test_file);
    exit(1);
}

if (!$mydb->executePreparedStatement($stmt)) {
    echo "Failed to execute statement: " . $mydb->error_msg . "\n";
    unlink($test_file);
    mysqli_stmt_close($stmt);
    exit(1);
}

mysqli_stmt_close($stmt);
echo "Recording inserted into database successfully.\n";

// Check if it was inserted
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

if ($recordings) {
    echo "Recordings found in database:\n";
    foreach ($recordings as $recording) {
        echo "- Recording ID: " . $recording->RECORDING_ID . ", File: " . $recording->FILE_PATH . "\n";
    }
} else {
    echo "No recordings found in database.\n";
}
?>