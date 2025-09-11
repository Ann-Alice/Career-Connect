<?php
require_once('include/initialize.php');

// Find orphaned recordings (recordings with registration IDs that don't exist in job registration table)
$sql = "SELECT DISTINCT ir.REGISTRATIONID 
        FROM tblinterviewrecordings ir 
        WHERE ir.REGISTRATIONID NOT IN (SELECT REGISTRATIONID FROM tbljobregistration)";
$mydb->setQuery($sql);
$orphaned_ids = $mydb->loadResultList();

echo "<h2>Orphaned Registration IDs in Recordings</h2>";
if (!empty($orphaned_ids)) {
    echo "<p>These registration IDs exist in recordings but not in job registrations:</p>";
    echo "<ul>";
    foreach ($orphaned_ids as $row) {
        echo "<li>Registration ID: " . $row->REGISTRATIONID . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No orphaned registration IDs found.</p>";
}

// Check if there are any incomplete registrations that might have recordings
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS 
        FROM tbljobregistration 
        WHERE INTERVIEW_STATUS != 'Completed' OR INTERVIEW_RESULTS IS NULL OR INTERVIEW_RESULTS = '' OR INTERVIEW_RESULTS = 'null'";
$mydb->setQuery($sql);
$incomplete = $mydb->loadResultList();

echo "<h2>Incomplete Registrations</h2>";
if (!empty($incomplete)) {
    echo "<p>These registrations are not marked as completed or don't have results:</p>";
    echo "<pre>";
    print_r($incomplete);
    echo "</pre>";
} else {
    echo "<p>All registrations are properly completed.</p>";
}
?>