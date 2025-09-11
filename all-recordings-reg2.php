<?php
require_once('include/initialize.php');

// Get all recordings for registration ID 2 ordered by date
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 2 ORDER BY RECORDED_AT DESC";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>All Recordings for Registration ID 2</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>