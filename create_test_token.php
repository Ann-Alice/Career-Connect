<?php
// Create a test token for registration ID 3
require_once('include/initialize.php');

// Delete any existing test tokens for registration ID 3
$sql = "DELETE FROM tblinterviewinvitations WHERE REGISTRATIONID = '3' AND TOKEN LIKE 'test%'";
$mydb->setQuery($sql);
$mydb->executeQuery();

// Create a new test token
$token = 'test_' . md5(uniqid(rand(), true));
$expiry_date = date('Y-m-d H:i:s', strtotime('+1 hour'));

$sql = "INSERT INTO tblinterviewinvitations 
        (REGISTRATIONID, JOBID, TOKEN, EXPIRY_DATE, STATUS) 
        VALUES 
        ('3', '1', '{$token}', '{$expiry_date}', 'pending')";

$mydb->setQuery($sql);
if ($mydb->executeQuery()) {
    echo "Test token created successfully: " . $token . "\n";
    echo "Expiry date: " . $expiry_date . "\n";
} else {
    echo "Failed to create test token: " . $mydb->error_msg . "\n";
}

// Also check the current status of registration ID 3
$sql = "SELECT INTERVIEW_STATUS FROM tbljobregistration WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result) {
    echo "Current interview status for registration ID 3: " . $result->INTERVIEW_STATUS . "\n";
    
    // If it's completed, reset it for testing
    if ($result->INTERVIEW_STATUS === 'Completed') {
        $sql = "UPDATE tbljobregistration SET INTERVIEW_STATUS = 'Pending', INTERVIEW_RESULTS = NULL WHERE REGISTRATIONID = '3'";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "Interview status reset to Pending for testing.\n";
        } else {
            echo "Failed to reset interview status: " . $mydb->error_msg . "\n";
        }
    }
} else {
    echo "No registration found for ID 3.\n";
}
?>