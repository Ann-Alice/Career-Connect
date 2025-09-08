<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

// Get applicant ID
$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($applicant_id <= 0) {
    die('Invalid applicant ID');
}

// Get resume file from database - STRICT applicant-specific access only
$sql = "SELECT af.*, a.FNAME, a.LNAME FROM tblattachmentfile af 
        INNER JOIN tblapplicants a ON af.USERATTACHMENTID = a.APPLICANTID
        WHERE af.USERATTACHMENTID = $applicant_id AND af.FILE_NAME = 'Resume' AND a.APPLICANTID = $applicant_id
        ORDER BY af.ID DESC 
        LIMIT 1";

$mydb->setQuery($sql);
$attachment = $mydb->loadSingleResult();

if (!$attachment) {
    die('No resume file found for this specific applicant');
}

// Build file path - handle both direct and photos subdirectory paths
$file_location = $attachment->FILE_LOCATION;

// If the path already includes photos/, use it as is
if (strpos($file_location, 'photos/') === 0) {
    $file_path = "../../applicant/" . $file_location;
} else {
    // Try both direct path and photos subdirectory
    $direct_path = "../../applicant/photos/" . $applicant_id . "/" . basename($file_location);
    $photos_path = "../../applicant/photos/" . $applicant_id . "/" . $attachment->FILE_NAME;
    
    if (file_exists($direct_path)) {
        $file_path = $direct_path;
    } elseif (file_exists($photos_path)) {
        $file_path = $photos_path;
    } else {
        // Try to find the file in the applicant's directory
        $applicant_dir = "../../applicant/photos/" . $applicant_id . "/";
        $file_name = basename($file_location);
        $file_path = $applicant_dir . $file_name;
        
        // Check if file exists in applicant directory
        if (file_exists($file_path)) {
            // Update the database to include photos/ prefix
            $sql = "UPDATE tblattachmentfile SET FILE_LOCATION = 'photos/" . $applicant_id . "/" . $file_name . "' WHERE ID = " . $attachment->ID;
            $mydb->setQuery($sql);
            $mydb->executeQuery();
        } else {
            die('Resume file not found on server');
        }
    }
}

// Verify file exists and is readable
if (!file_exists($file_path) || !is_readable($file_path)) {
    die('Resume file not accessible on server');
}

// Get file info
$file_info = pathinfo($file_path);
$file_extension = strtolower($file_info['extension']);
$file_size = filesize($file_path);

// Validate file is not empty
if ($file_size === 0) {
    die('Resume file is empty or corrupted');
}

// Set correct MIME type based on file extension
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain',
    'rtf' => 'application/rtf'
];

$content_type = isset($mime_types[$file_extension]) ? $mime_types[$file_extension] : 'application/octet-stream';

// For PDF files, validate the file header
if ($file_extension === 'pdf') {
    $file_handle = fopen($file_path, 'rb');
    if ($file_handle) {
        $first_bytes = fread($file_handle, 4);
        fclose($file_handle);
        
        if ($first_bytes !== '%PDF') {
            die('Resume file is corrupted - invalid PDF format');
        }
    } else {
        die('Cannot validate resume file integrity');
    }
}

// Generate download filename
$applicant_name = $attachment->FNAME . '_' . $attachment->LNAME;
$applicant_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $applicant_name); // Clean filename
$download_filename = $applicant_name . '_Resume.' . $file_extension;

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set proper headers for file download
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $download_filename . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

// Read and output the file
readfile($file_path);
exit;