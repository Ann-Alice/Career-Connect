<?php
require_once('include/initialize.php');

// Check total recordings count
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

echo "Total recordings in database: " . $result->count . "\n";
?>