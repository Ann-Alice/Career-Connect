<?php
require_once('include/initialize.php');

// Get the next registration ID
$sql = "SELECT MAX(REGISTRATIONID) as max_id FROM tbljobregistration";
$mydb->setQuery($sql);
$max_result = $mydb->loadSingleResult();
$new_registration_id = $max_result->max_id + 1;

echo "Next registration ID will be: " . $new_registration_id . "\n";

// Create a simple insert query
$sql = "INSERT INTO tbljobregistration 
        (REGISTRATIONID, COMPANYID, JOBID, APPLICANTID, APPLICANT, REGISTRATIONDATE, REMARKS, FILEID, PENDINGAPPLICATION, HVIEW, DATETIMEAPPROVED) 
        VALUES 
        (" . $new_registration_id . ", 2, 2, 2018015, 'Janry Tan', NOW(), 'Approved', 2147483647, 0, 0, NOW())";

$mydb->setQuery($sql);

if ($mydb->executeQuery()) {
    echo "New registration created successfully!\n";
    echo "Registration ID: " . $new_registration_id . "\n";
    echo "Use this ID for your new interview submission.\n";
} else {
    echo "Error creating new registration: " . $mydb->getLastError() . "\n";
}
?>