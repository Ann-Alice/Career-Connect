<?php
require_once('include/initialize.php');

// Create a new job registration entry
// We'll copy the structure from registration ID 2 but with a new ID
$sql = "SELECT * FROM tbljobregistration WHERE REGISTRATIONID = 2";
$mydb->setQuery($sql);
$source_registration = $mydb->loadSingleResult();

if ($source_registration) {
    // Get the next registration ID
    $sql = "SELECT MAX(REGISTRATIONID) as max_id FROM tbljobregistration";
    $mydb->setQuery($sql);
    $max_result = $mydb->loadSingleResult();
    $new_registration_id = $max_result->max_id + 1;
    
    echo "Creating new registration with ID: " . $new_registration_id . "\n";
    
    // Insert new registration (copying most fields from registration ID 2)
    $sql = "INSERT INTO tbljobregistration 
            (REGISTRATIONID, COMPANYID, JOBID, APPLICANTID, APPLICANT, REGISTRATIONDATE, REMARKS, FILEID, PENDINGAPPLICATION, HVIEW, DATETIMEAPPROVED) 
            VALUES 
            (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, NOW())";
    
    $mydb->setQuery($sql);
    $mydb->bind_param('iiisssiiii', 
        $new_registration_id,
        $source_registration->COMPANYID,
        $source_registration->JOBID,
        $source_registration->APPLICANTID,
        $source_registration->APPLICANT,
        $source_registration->REMARKS,
        $source_registration->FILEID,
        $source_registration->PENDINGAPPLICATION,
        $source_registration->HVIEW
    );
    
    if ($mydb->executeQuery()) {
        echo "New registration created successfully!\n";
        echo "Use registration ID " . $new_registration_id . " for your new interview submission.\n";
    } else {
        echo "Error creating new registration: " . $mydb->getLastError() . "\n";
    }
} else {
    echo "Could not find source registration to copy from.\n";
}
?>