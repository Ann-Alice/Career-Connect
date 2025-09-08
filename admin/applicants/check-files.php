<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

echo "<h2>File Location Analysis</h2>";

// Get all attachment files
$sql = "SELECT af.*, a.FNAME, a.LNAME FROM tblattachmentfile af 
        LEFT JOIN tblapplicants a ON af.USERATTACHMENTID = a.APPLICANTID
        WHERE af.FILE_NAME = 'Resume'
        ORDER BY af.ID DESC";

$mydb->setQuery($sql);
$attachments = $mydb->loadResultList();

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>ID</th><th>Applicant</th><th>File Location (DB)</th><th>Direct Path Exists</th><th>Photos Path Exists</th><th>Action</th>";
echo "</tr>";

if ($attachments) {
    foreach ($attachments as $att) {
        $direct_path = "../../applicant/" . $att->FILE_LOCATION;
        $photos_path = "../../applicant/photos/" . $att->FILE_LOCATION;
        
        $direct_exists = file_exists($direct_path);
        $photos_exists = file_exists($photos_path);
        
        echo "<tr>";
        echo "<td>{$att->ID}</td>";
        echo "<td>" . ($att->FNAME ? $att->FNAME . " " . $att->LNAME : "Unknown") . "</td>";
        echo "<td>{$att->FILE_LOCATION}</td>";
        echo "<td>" . ($direct_exists ? "✓ Yes" : "✗ No") . "</td>";
        echo "<td>" . ($photos_exists ? "✓ Yes" : "✗ No") . "</td>";
        
        if ($direct_exists || $photos_exists) {
            $working_path = $direct_exists ? $direct_path : $photos_path;
            echo "<td style='color: green;'>File found: " . ($direct_exists ? "Direct" : "Photos") . "</td>";
        } else {
            echo "<td style='color: red;'>File missing</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No resume files found</td></tr>";
}

echo "</table>";

echo "<hr>";
echo "<h3>Applicant Directory Contents:</h3>";

$applicant_dir = "../../applicant/";
if (is_dir($applicant_dir)) {
    $files = scandir($applicant_dir);
    echo "<h4>Main applicant directory:</h4>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($applicant_dir . $file)) {
                echo "📁 {$file}/<br>";
            } else {
                echo "📄 {$file}<br>";
            }
        }
    }
}

$photos_dir = "../../applicant/photos/";
if (is_dir($photos_dir)) {
    $files = scandir($photos_dir);
    echo "<h4>Photos subdirectory:</h4>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $size = round(filesize($photos_dir . $file) / 1024, 1);
            echo "📄 {$file} ({$size} KB)<br>";
        }
    }
}
?>