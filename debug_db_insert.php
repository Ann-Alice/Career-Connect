<?php
// Debug the database insert operation
require_once('include/initialize.php');

// Test the database insert with a simple query first
$sql = "INSERT INTO tblinterviewrecordings 
        (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
        VALUES 
        ('3', 2, 'uploads/interviews/3/test2.webm', 20.0, 2, 'test2', 'Test transcript 2')";

$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    echo "Simple insert successful\n";
    
    // Get the insert ID
    $insert_id = $mydb->insert_id();
    echo "Insert ID: " . $insert_id . "\n";
} else {
    echo "Simple insert failed: " . $mydb->error_msg . "\n";
}

// Now test with prepared statements
$sql = "INSERT INTO tblinterviewrecordings 
        (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE, TRANSCRIPT) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";

$params = ['3', 3, 'uploads/interviews/3/test3.webm', 25.0, 3, 'test3', 'Test transcript 3'];
$types = 'sisidiss';

echo "Preparing statement...\n";
$stmt = $mydb->prepareStatement($sql, $params, $types);
if ($stmt) {
    echo "Statement prepared successfully\n";
    
    echo "Executing statement...\n";
    $result = $mydb->executePreparedStatement($stmt);
    if ($result) {
        echo "Statement executed successfully\n";
    } else {
        echo "Statement execution failed: " . $mydb->error_msg . "\n";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Statement preparation failed: " . $mydb->error_msg . "\n";
}
?>