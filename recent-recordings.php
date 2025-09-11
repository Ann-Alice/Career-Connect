<?php
require_once('include/initialize.php');

// Get the 5 most recent recordings
$sql = "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>5 Most Recent Recordings</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>