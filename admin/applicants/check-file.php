<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

echo "<h2>File Integrity Check</h2>";

if ($applicant_id <= 0) {
    echo "<p style='color: red;'>Invalid applicant ID</p>";
    exit;
}

// Get file info
$sql = "SELECT af.*, a.FNAME, a.LNAME FROM tblattachmentfile af 
        INNER JOIN tbljobregistration jr ON af.JOBID = jr.JOBID 
        INNER JOIN tblapplicants a ON jr.APPLICANTID = a.APPLICANTID
        WHERE jr.APPLICANTID = $applicant_id AND af.FILE_NAME = 'Resume'
        ORDER BY af.ID DESC 
        LIMIT 1";

$mydb->setQuery($sql);
$attachment = $mydb->loadSingleResult();

if (!$attachment) {
    echo "<p style='color: red;'>No resume file found for applicant ID: $applicant_id</p>";
    exit;
}

$file_path = "../../applicant/" . $attachment->FILE_LOCATION;

echo "<h3>File Information:</h3>";
echo "<p><strong>Applicant:</strong> {$attachment->FNAME} {$attachment->LNAME}</p>";
echo "<p><strong>File Location (DB):</strong> {$attachment->FILE_LOCATION}</p>";
echo "<p><strong>Full Path:</strong> {$file_path}</p>";

if (!file_exists($file_path)) {
    echo "<p style='color: red;'>File does not exist on disk!</p>";
    exit;
}

$file_size = filesize($file_path);
$file_info = pathinfo($file_path);

echo "<p><strong>File Size:</strong> " . round($file_size / 1024 / 1024, 2) . " MB</p>";
echo "<p><strong>File Extension:</strong> {$file_info['extension']}</p>";
echo "<p><strong>File Readable:</strong> " . (is_readable($file_path) ? "Yes" : "No") . "</p>";

// Check if it's a valid PDF
$file_handle = fopen($file_path, 'rb');
if ($file_handle) {
    $first_bytes = fread($file_handle, 8);
    fclose($file_handle);
    
    echo "<p><strong>File Header:</strong> " . bin2hex($first_bytes) . "</p>";
    echo "<p><strong>File Starts With:</strong> " . htmlspecialchars(substr($first_bytes, 0, 4)) . "</p>";
    
    $is_pdf = (substr($first_bytes, 0, 4) === '%PDF');
    echo "<p><strong>Valid PDF Header:</strong> " . ($is_pdf ? "Yes" : "No") . "</p>";
    
    if (!$is_pdf) {
        echo "<p style='color: red;'>This file is not a valid PDF! The header should start with '%PDF'</p>";
        
        // Try to detect what type of file it actually is
        $mime_type = mime_content_type($file_path);
        echo "<p><strong>Detected MIME Type:</strong> {$mime_type}</p>";
        
        // Show first 100 characters to see what the file contains
        $file_content = file_get_contents($file_path, false, null, 0, 100);
        echo "<p><strong>First 100 characters:</strong><br>";
        echo "<textarea style='width: 100%; height: 100px;'>" . htmlspecialchars($file_content) . "</textarea></p>";
    }
} else {
    echo "<p style='color: red;'>Cannot open file for reading!</p>";
}

echo "<hr>";
echo "<p><strong>Download Tests:</strong></p>";
echo "<p><a href='download-resume.php?id={$applicant_id}&debug=0' target='_blank'>Test Direct Download</a></p>";
echo "<p><a href='" . web_root . "applicant/{$attachment->FILE_LOCATION}' target='_blank'>Test Direct File Access</a></p>";
echo "<p><a href='view.php?id={$applicant_id}'>← Back to Applicant View</a></p>";
?>