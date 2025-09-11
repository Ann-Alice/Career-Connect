<?php
require_once('include/initialize.php');

// Check registration ID 3
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS, INTERVIEW_COMPLETED_AT FROM tbljobregistration WHERE REGISTRATIONID = 3";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

echo "<h2>Registration ID 3 Status</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";

// Check if there are any recordings for registration ID 3
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings WHERE REGISTRATIONID = 3";
$mydb->setQuery($sql);
$recordings_count = $mydb->loadSingleResult();

echo "<h2>Recordings for Registration ID 3</h2>";
echo "<p>Number of recordings: " . $recordings_count->count . "</p>";

if ($recordings_count->count > 0) {
    // Get the recordings
    $sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 3 ORDER BY RECORDED_AT";
    $mydb->setQuery($sql);
    $recordings = $mydb->loadResultList();
    
    echo "<h3>Recording Details</h3>";
    echo "<pre>";
    print_r($recordings);
    echo "</pre>";
}
?>