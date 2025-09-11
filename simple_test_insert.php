<?php
// Simple test script
require_once('include/initialize.php');

// Test basic insert without prepared statements
$sql = "INSERT INTO tblinterviewrecordings 
        (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
        VALUES 
        ('3', 1, 'uploads/interviews/3/test_recording.webm', 15.5, 1, 'test', 'Test transcript')";

$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    echo "Insert successful\n";
} else {
    echo "Insert failed: " . $mydb->error_msg . "\n";
}

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