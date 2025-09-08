<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

if (!isset($_SESSION['ADMINID'])) {
    redirect(web_root . "admin/login.php");
}

if (!isset($_GET['id'])) {
    message("Invalid request.", "error");
    redirect("interview-results.php");
}

$registration_id = $_GET['id'];

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
    message("No video recording found for this interview.", "error");
    redirect("interview-results.php");
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

// Try different possible paths if the main path doesn't exist
if (!file_exists($full_path)) {
    // Try looking in subdirectories
    $uploads_dir = web_root . 'uploads/interviews/';
    $found_file = null;
    
    if (is_dir($uploads_dir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploads_dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'webm') {
                // Check if this file belongs to this registration
                if (strpos($file->getFilename(), $registration_id) !== false) {
                    $found_file = $file->getPathname();
                    break;
                }
            }
        }
    }
    
    if ($found_file) {
        $full_path = $found_file;
        error_log("Found video file in subdirectory: " . $full_path);
    }
}

// Debug information
error_log("Download request for registration ID: " . $registration_id);
error_log("Video path from DB: " . $video_path);
error_log("Full path: " . $full_path);
error_log("File exists: " . (file_exists($full_path) ? 'Yes' : 'No'));

if (!file_exists($full_path)) {
    message("Video file not found on server.", "error");
    redirect("interview-results.php");
}

// Generate filename for download
$candidate_name = $video->FNAME . '_' . $video->LNAME;
$position = str_replace(' ', '_', $video->OCCUPATIONTITLE);
$date = date('Y-m-d', strtotime($video->CREATED_AT));
$filename = "Interview_{$candidate_name}_{$position}_{$date}.webm";

// Get file size for display
$file_size = filesize($full_path);
$file_size_mb = round($file_size / (1024 * 1024), 2);

// Set headers for download
header('Content-Type: video/webm');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Output file
readfile($full_path);
exit;
?> 