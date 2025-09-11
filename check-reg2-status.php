<?php
require_once('include/initialize.php');

// Check registration ID 2
$registration_id = 2;

echo "Checking registration ID: " . $registration_id . "\n";

// Get registration details
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE REGISTRATIONID = " . $registration_id;
$mydb->setQuery($sql);
$registration = $mydb->loadSingleResult();

if ($registration) {
    echo "Registration found:\n";
    echo "- Status: " . $registration->INTERVIEW_STATUS . "\n";
    echo "- Results: " . ($registration->INTERVIEW_RESULTS ? "Present" : "Missing") . "\n";
    
    // Check if it should appear on interview results page
    $should_appear = ($registration->INTERVIEW_STATUS == 'Completed' || $registration->INTERVIEW_STATUS == 'completed') 
        && $registration->INTERVIEW_RESULTS 
        && $registration->INTERVIEW_RESULTS != '' 
        && $registration->INTERVIEW_RESULTS != 'null';
    
    echo "Should appear on interview results page: " . ($should_appear ? "Yes" : "No") . "\n";
    
    // Get recordings count
    $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings WHERE REGISTRATIONID = " . $registration_id;
    $mydb->setQuery($sql);
    $recordings_count = $mydb->loadSingleResult();
    
    echo "Number of recordings: " . $recordings_count->count . "\n";
} else {
    echo "Registration not found.\n";
}
?>