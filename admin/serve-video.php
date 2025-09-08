<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    http_response_code(403);
    die('Access denied');
}

$registration_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($registration_id <= 0) {
    http_response_code(400);
    die('Invalid registration ID');
}

// Get video record - prioritize consolidated videos
$sql = "SELECT v.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
        FROM tblinterviewvideos v 
        JOIN tbljobregistration r ON v.REGISTRATIONID = r.REGISTRATIONID 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        WHERE v.REGISTRATIONID = '{$registration_id}' 
        ORDER BY v.CREATED_AT DESC LIMIT 1";
$mydb->setQuery($sql);
$video = $mydb->loadSingleResult();

if (!$video) {
    // Try to find individual recordings if no consolidated video exists
    $sql = "SELECT r.FILE_PATH, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, 
                   MAX(r.DURATION) as DURATION, MIN(r.RECORDED_AT) as CREATED_AT
            FROM tblinterviewrecordings r 
            JOIN tbljobregistration jr ON r.REGISTRATIONID = jr.REGISTRATIONID 
            JOIN tblapplicants a ON jr.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON jr.JOBID = j.JOBID 
            WHERE r.REGISTRATIONID = '{$registration_id}' 
            GROUP BY r.REGISTRATIONID
            ORDER BY r.DURATION DESC LIMIT 1";
    $mydb->setQuery($sql);
    $recording = $mydb->loadSingleResult();
    
    if ($recording) {
        // Create a pseudo video object from recording data
        $video = (object)[
            'VIDEO_PATH' => $recording->FILE_PATH,
            'FNAME' => $recording->FNAME,
            'LNAME' => $recording->LNAME,
            'OCCUPATIONTITLE' => $recording->OCCUPATIONTITLE,
            'CREATED_AT' => $recording->CREATED_AT
        ];
    }
}

if (!$video) {
    http_response_code(404);
    die('No video recording found for this interview');
}

$video_path = $video->VIDEO_PATH;

// Determine full path based on video type
if (strpos($video_path, 'uploads/interviews/consolidated/') === 0) {
    // Consolidated video
    $full_path = web_root . $video_path;
} elseif (strpos($video_path, '/') !== false || strpos($video_path, '\\') !== false) {
    // Individual recording with full path
    $full_path = $video_path;
} else {
    // Legacy format - video filename only
    $full_path = web_root . 'uploads/interviews/' . $video_path;
}

// Verify file exists and is readable
if (!file_exists($full_path) || !is_readable($full_path)) {
    http_response_code(404);
    die('Video file not accessible');
}

// Get file info
$file_size = filesize($full_path);
$file_info = pathinfo($full_path);
$file_extension = strtolower($file_info['extension']);

// Only serve video files
$allowed_extensions = ['webm', 'mp4', 'avi', 'mov'];
if (!in_array($file_extension, $allowed_extensions)) {
    http_response_code(400);
    die('Invalid video file format');
}

// Validate file is not empty
if ($file_size === 0) {
    http_response_code(404);
    die('Video file is empty');
}

// Set MIME type based on extension
$mime_types = [
    'webm' => 'video/webm',
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mov' => 'video/quicktime'
];

$content_type = isset($mime_types[$file_extension]) ? $mime_types[$file_extension] : 'video/webm';

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set proper headers for video streaming
header('Content-Type: ' . $content_type);
header('Content-Length: ' . $file_size);
header('Content-Disposition: inline; filename="Interview_' . $video->FNAME . '_' . $video->LNAME . '.' . $file_extension . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
header('Accept-Ranges: bytes');

// Handle range requests for video streaming
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];
    $ranges = explode('=', $range);
    $offsets = explode('-', $ranges[1]);
    $offset = intval($offsets[0]);
    $length = intval($offsets[1]) ? intval($offsets[1]) - $offset + 1 : $file_size - $offset;
    
    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' . $offset . '-' . ($offset + $length - 1) . '/' . $file_size);
    header('Content-Length: ' . $length);
    
    $file = fopen($full_path, 'rb');
    fseek($file, $offset);
    echo fread($file, $length);
    fclose($file);
} else {
    // Serve the complete file
    readfile($full_path);
}

exit;
?>