<?php
require_once('include/initialize.php');

// Check if there are any registrations without recordings
$sql = "SELECT COUNT(*) as count FROM tbljobregistration";
$mydb->setQuery($sql);
$total = $mydb->loadSingleResult();

$sql = "SELECT COUNT(DISTINCT REGISTRATIONID) as count FROM tblinterviewrecordings";
$mydb->setQuery($sql);
$with_recordings = $mydb->loadSingleResult();

echo "Total registrations: " . $total->count . "\n";
echo "Registrations with recordings: " . $with_recordings->count . "\n";
echo "Registrations without recordings: " . ($total->count - $with_recordings->count) . "\n";

// Get the highest registration ID
$sql = "SELECT MAX(REGISTRATIONID) as max_id FROM tbljobregistration";
$mydb->setQuery($sql);
$max = $mydb->loadSingleResult();
echo "Highest registration ID: " . $max->max_id . "\n";

// Check if the highest registration ID has recordings
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings WHERE REGISTRATIONID = " . $max->max_id;
$mydb->setQuery($sql);
$has_recordings = $mydb->loadSingleResult();
echo "Highest registration ID has recordings: " . ($has_recordings->count > 0 ? "Yes" : "No") . "\n";
?>