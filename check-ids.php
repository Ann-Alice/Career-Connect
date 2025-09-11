<?php
require_once('include/initialize.php');

// Get distinct registration IDs from recordings
$sql = "SELECT DISTINCT REGISTRATIONID FROM tblinterviewrecordings ORDER BY REGISTRATIONID";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "Registration IDs with recordings: ";
foreach($result as $row) {
    echo $row->REGISTRATIONID . " ";
}
echo "\n";

// Get all registration IDs
$sql = "SELECT REGISTRATIONID FROM tbljobregistration ORDER BY REGISTRATIONID";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "All registration IDs: ";
foreach($result as $row) {
    echo $row->REGISTRATIONID . " ";
}
echo "\n";
?>