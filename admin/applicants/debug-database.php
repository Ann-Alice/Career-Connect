<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

echo "<h2>Database Content Debug</h2>";

// Show all applicants
echo "<h3>1. All Applicants:</h3>";
$sql = "SELECT APPLICANTID, FNAME, LNAME, EMAILADDRESS FROM tblapplicants LIMIT 10";
$mydb->setQuery($sql);
$applicants = $mydb->loadResultList();

if ($applicants) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>";
    foreach ($applicants as $app) {
        echo "<tr>";
        echo "<td>{$app->APPLICANTID}</td>";
        echo "<td>{$app->FNAME} {$app->LNAME}</td>";
        echo "<td>{$app->EMAILADDRESS}</td>";
        echo "<td><a href='debug-resume.php?id={$app->APPLICANTID}'>Debug Resume</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No applicants found</p>";
}

// Show all attachment files
echo "<h3>2. All Attachment Files:</h3>";
$sql = "SELECT * FROM tblattachmentfile LIMIT 10";
$mydb->setQuery($sql);
$files = $mydb->loadResultList();

if ($files) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>File ID</th><th>Job ID</th><th>File Name</th><th>Location</th><th>User ID</th></tr>";
    foreach ($files as $file) {
        echo "<tr>";
        echo "<td>{$file->ID}</td>";
        echo "<td>" . (isset($file->FILEID) ? $file->FILEID : 'N/A') . "</td>";
        echo "<td>{$file->JOBID}</td>";
        echo "<td>{$file->FILE_NAME}</td>";
        echo "<td>{$file->FILE_LOCATION}</td>";
        echo "<td>{$file->USERATTACHMENTID}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No attachment files found</p>";
}

// Show all job registrations
echo "<h3>3. All Job Registrations:</h3>";
$sql = "SELECT * FROM tbljobregistration LIMIT 10";
$mydb->setQuery($sql);
$regs = $mydb->loadResultList();

if ($regs) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Reg ID</th><th>Company ID</th><th>Job ID</th><th>Applicant ID</th><th>File ID</th><th>Date</th></tr>";
    foreach ($regs as $reg) {
        echo "<tr>";
        echo "<td>{$reg->REGISTRATIONID}</td>";
        echo "<td>" . (isset($reg->COMPANYID) ? $reg->COMPANYID : 'N/A') . "</td>";
        echo "<td>{$reg->JOBID}</td>";
        echo "<td>{$reg->APPLICANTID}</td>";
        echo "<td>" . (isset($reg->FILEID) ? $reg->FILEID : 'N/A') . "</td>";
        echo "<td>" . (isset($reg->REGISTRATIONDATE) ? $reg->REGISTRATIONDATE : 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No job registrations found</p>";
}

echo "<hr>";
echo "<p><a href='list.php'>← Back to Applicants List</a></p>";
?>