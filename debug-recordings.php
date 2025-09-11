<?php
require_once('include/initialize.php');

// Get table structure
$sql = "DESCRIBE tblinterviewrecordings";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<pre>";
print_r($result);
echo "</pre>";

// Get recent recordings
$sql = "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

echo "<h2>Recent Recordings</h2>";
echo "<pre>";
print_r($recordings);
echo "</pre>";
?>