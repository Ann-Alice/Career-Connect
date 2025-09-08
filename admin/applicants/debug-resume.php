<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

// Get applicant ID
$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

echo "<h2>Resume Debug Information</h2>";
echo "<p><strong>Applicant ID:</strong> " . $applicant_id . "</p>";

if ($applicant_id <= 0) {
    echo "<p style='color: red;'>No valid applicant ID provided</p>";
    exit;
}

// Check if applicant exists
$sql = "SELECT * FROM tblapplicants WHERE APPLICANTID = $applicant_id";
$mydb->setQuery($sql);
$applicant = $mydb->loadSingleResult();

echo "<h3>1. Applicant Information:</h3>";
if ($applicant) {
    echo "<p style='color: green;'>✓ Applicant found: " . $applicant->FNAME . " " . $applicant->LNAME . "</p>";
} else {
    echo "<p style='color: red;'>✗ Applicant not found in database</p>";
    exit;
}

// Check job registrations for this applicant
$sql = "SELECT * FROM tbljobregistration WHERE APPLICANTID = $applicant_id";
$mydb->setQuery($sql);
$registrations = $mydb->loadResultList();

echo "<h3>2. Job Registrations:</h3>";
if ($registrations) {
    echo "<p style='color: green;'>✓ Found " . count($registrations) . " job registration(s)</p>";
    foreach ($registrations as $reg) {
        echo "<p>- Registration ID: {$reg->REGISTRATIONID}, Job ID: {$reg->JOBID}, File ID: {$reg->FILEID}</p>";
    }
} else {
    echo "<p style='color: red;'>✗ No job registrations found for this applicant</p>";
}

// Check attachment files
$sql = "SELECT af.* FROM tblattachmentfile af 
        INNER JOIN tbljobregistration jr ON af.JOBID = jr.JOBID 
        WHERE jr.APPLICANTID = $applicant_id";
$mydb->setQuery($sql);
$all_files = $mydb->loadResultList();

echo "<h3>3. All Attachment Files:</h3>";
if ($all_files) {
    echo "<p style='color: green;'>✓ Found " . count($all_files) . " attachment file(s)</p>";
    foreach ($all_files as $file) {
        echo "<p>- File: {$file->FILE_NAME}, Location: {$file->FILE_LOCATION}</p>";
    }
} else {
    echo "<p style='color: red;'>✗ No attachment files found</p>";
}

// Check specifically for resume files
$sql = "SELECT af.* FROM tblattachmentfile af 
        INNER JOIN tbljobregistration jr ON af.JOBID = jr.JOBID 
        WHERE jr.APPLICANTID = $applicant_id AND af.FILE_NAME = 'Resume'";
$mydb->setQuery($sql);
$resume_files = $mydb->loadResultList();

echo "<h3>4. Resume Files Specifically:</h3>";
if ($resume_files) {
    echo "<p style='color: green;'>✓ Found " . count($resume_files) . " resume file(s)</p>";
    foreach ($resume_files as $file) {
        $file_path = "../../applicant/" . $file->FILE_LOCATION;
        $file_exists = file_exists($file_path);
        $file_size = $file_exists ? filesize($file_path) : 0;
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<p><strong>File Name:</strong> {$file->FILE_NAME}</p>";
        echo "<p><strong>File Location (DB):</strong> {$file->FILE_LOCATION}</p>";
        echo "<p><strong>Full Path:</strong> {$file_path}</p>";
        echo "<p><strong>File Exists:</strong> " . ($file_exists ? "✓ Yes" : "✗ No") . "</p>";
        if ($file_exists) {
            echo "<p><strong>File Size:</strong> " . round($file_size / 1024 / 1024, 2) . " MB</p>";
            echo "<p><strong>Direct Link:</strong> <a href='" . web_root . "applicant/" . $file->FILE_LOCATION . "' target='_blank'>Open File</a></p>";
        }
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>✗ No resume files found</p>";
}

// Check if files exist in applicant directory
echo "<h3>5. Applicant Directory Check:</h3>";
$applicant_dir = "../../applicant/photos/";
if (is_dir($applicant_dir)) {
    $files = scandir($applicant_dir);
    echo "<p style='color: green;'>✓ Applicant photos directory exists</p>";
    echo "<p>Files in directory: " . (count($files) - 2) . "</p>"; // -2 for . and ..
    
    // Show first few files
    $count = 0;
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $count < 5) {
            echo "<p>- {$file}</p>";
            $count++;
        }
    }
    if (count($files) > 7) {
        echo "<p>... and " . (count($files) - 7) . " more files</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Applicant photos directory not found</p>";
}

echo "<hr>";
echo "<p><a href='view.php?id={$applicant_id}'>← Back to Applicant View</a></p>";
?>