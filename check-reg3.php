<?php
require_once('include/initialize.php');

// Check recordings for registration ID 3
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 3 ORDER BY RECORDED_AT DESC LIMIT 5";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>Recent Recordings for Registration ID 3</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";

// Check the status of registration ID 3
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE REGISTRATIONID = 3";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>Status of Registration ID 3</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>