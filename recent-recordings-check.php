<?php
require_once('include/initialize.php');

// Check recent recordings
$sql = "SELECT * FROM tblinterviewrecordings WHERE RECORDED_AT > '2025-09-09 00:30:00' ORDER BY RECORDED_AT DESC";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>Recent Recordings (After 2025-09-09 00:30:00)</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>