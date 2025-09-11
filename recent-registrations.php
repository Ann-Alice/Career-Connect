<?php
require_once('include/initialize.php');

// Get the 5 most recent job registrations
$sql = "SELECT * FROM tbljobregistration ORDER BY REGISTRATIONID DESC LIMIT 5";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

echo "<h2>5 Most Recent Job Registrations</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>