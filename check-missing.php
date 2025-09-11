<?php
require_once('include/initialize.php');

// Check recordings for registration IDs 3, 8, 9
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID IN (3, 8, 9) ORDER BY REGISTRATIONID, RECORDED_AT";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>Recordings for Registration IDs 3, 8, 9</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";

// Check if these registration IDs exist in job registration table
$sql = "SELECT * FROM tbljobregistration WHERE REGISTRATIONID IN (3, 8, 9)";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>Job Registrations for IDs 3, 8, 9</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>