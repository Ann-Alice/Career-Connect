<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    http_response_code(403);
    die('Access denied');
}

// Get applicant ID
$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($applicant_id <= 0) {
    http_response_code(400);
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
    http_response_code(404);
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
        
        if (!file_exists($file_path)) {
            http_response_code(404);
            die('Resume file not found on server');
        }
    }
}

// Verify file exists and is readable
if (!file_exists($file_path) || !is_readable($file_path)) {
    http_response_code(404);
    die('Resume file not accessible');
}

// Get file info
$file_info = pathinfo($file_path);
$file_extension = strtolower($file_info['extension']);
$file_size = filesize($file_path);

// Only serve PDF files through this script
if ($file_extension !== 'pdf') {
    http_response_code(400);
    die('This script only serves PDF files');
}

// Validate file is not empty
if ($file_size === 0) {
    http_response_code(404);
    die('PDF file is empty');
}

// Validate PDF header
$file_handle = fopen($file_path, 'rb');
if ($file_handle) {
    $first_bytes = fread($file_handle, 4);
    fclose($file_handle);
    
    if ($first_bytes !== '%PDF') {
        http_response_code(400);
        die('Invalid PDF file format');
    }
} else {
    http_response_code(500);
    die('Cannot read PDF file');
}

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set proper headers for PDF inline viewing
header('Content-Type: application/pdf');
header('Content-Length: ' . $file_size);
header('Content-Disposition: inline; filename="Resume_' . $attachment->FNAME . '_' . $attachment->LNAME . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
header('Accept-Ranges: bytes');

// Handle range requests for better PDF viewer compatibility
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];
    $ranges = explode('=', $range);
    $offsets = explode('-', $ranges[1]);
    $offset = intval($offsets[0]);
    $length = intval($offsets[1]) ? intval($offsets[1]) - $offset : $file_size - $offset;
    
    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' . $offset . '-' . ($offset + $length - 1) . '/' . $file_size);
    header('Content-Length: ' . $length);
    
    $file = fopen($file_path, 'rb');
    fseek($file, $offset);
    echo fread($file, $length);
    fclose($file);
} else {
    // Serve the complete file
    readfile($file_path);
}

exit;
?>