<?php
require_once('include/initialize.php');

// Get recent job registrations
$sql = "SELECT * FROM tbljobregistration ORDER BY REGISTRATIONID DESC LIMIT 10";
$mydb->setQuery($sql);
$registrations = $mydb->loadResultList();

echo "<h2>Recent Job Registrations</h2>";
echo "<pre>";
print_r($registrations);
echo "</pre>";

// Check if there are any registrations without recordings
$sql = "SELECT r.*, j.OCCUPATIONTITLE, a.FNAME, a.LNAME 
        FROM tbljobregistration r 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        WHERE r.REGISTRATIONID NOT IN (SELECT DISTINCT REGISTRATIONID FROM tblinterviewrecordings) 
        ORDER BY r.REGISTRATIONID DESC 
        LIMIT 5";
$mydb->setQuery($sql);
$registrationsWithoutRecordings = $mydb->loadResultList();

echo "<h2>Registrations Without Recordings</h2>";
echo "<pre>";
print_r($registrationsWithoutRecordings);
echo "</pre>";
?>