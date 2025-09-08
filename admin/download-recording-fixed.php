<?php
/**
 * Enhanced Video Recording Download Handler  
 * Properly serves video files with correct MIME types and headers
 * Supports actual video files and fallback to demo content
 */

require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

// Get and validate registration ID
$registration_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$demo_mode = isset($_GET['demo']) && $_GET['demo'] == '1';

if ($registration_id <= 0) {
    http_response_code(400);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>Error: Invalid Registration ID</h1>';
    echo '<p>Please provide a valid registration ID.</p>';
    exit;
}

// Handle HEAD requests for file existence check
if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    // Check if any recording exists
    $has_recording = false;
    
    // Check consolidated videos first
    $sql = "SELECT VIDEO_PATH FROM tblinterviewvideos WHERE REGISTRATIONID = ? ORDER BY CREATED_AT DESC LIMIT 1";
    $mydb->setQuery($sql);
    $mydb->bind_param('i', $registration_id);
    $video = $mydb->loadSingleResult();
    
    if ($video) {
        $video_paths = [
            "uploads/interviews/consolidated/" . $video->VIDEO_PATH,
            "uploads/interviews/" . $video->VIDEO_PATH
        ];
        
        foreach ($video_paths as $path) {
            if (file_exists($path) && filesize($path) > 0) {
                $has_recording = true;
                break;
            }
        }
    }
    
    // Check individual recordings if no consolidated video
    if (!$has_recording) {
        $sql = "SELECT FILE_PATH FROM tblinterviewrecordings WHERE REGISTRATIONID = ? ORDER BY RECORDED_AT DESC LIMIT 1";
        $mydb->setQuery($sql);
        $mydb->bind_param('i', $registration_id);
        $recording = $mydb->loadSingleResult();
        
        if ($recording && file_exists($recording->FILE_PATH) && filesize($recording->FILE_PATH) > 0) {
            $has_recording = true;
        }
    }
    
    if ($has_recording) {
        header('Content-Type: video/mp4');
        header('Content-Length: 1024');
        http_response_code(200);
    } else {
        http_response_code(404);
    }
    exit;
}

// Get interview information for filename generation
$sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
        WHERE r.REGISTRATIONID = ?";

$mydb->setQuery($sql);
$mydb->bind_param('i', $registration_id);
$interview = $mydb->loadSingleResult();

if (!$interview) {
    http_response_code(404);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>Error: Interview Not Found</h1>';
    echo '<p>No interview found with the specified registration ID.</p>';
    exit;
}

/**
 * Check if a file is a valid video file by examining its content signature
 */
function isValidVideoFile($file_path) {
    if (!file_exists($file_path) || filesize($file_path) < 100) {
        return false;
    }
    
    $file_content = file_get_contents($file_path, false, null, 0, 100);
    
    // Check for common video file signatures
    $video_signatures = [
        'ftyp',                    // MP4
        "\x1A\x45\xDF\xA3",      // WebM/MKV (EBML signature)
        'RIFF',                   // AVI
        'moov',                   // QuickTime
        'mdat',                   // MP4 data
        "\x00\x00\x00\x20ftyp"   // MP4 with size header
    ];
    
    foreach ($video_signatures as $signature) {
        if (strpos($file_content, $signature) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get appropriate MIME type based on file extension
 */
function getVideoMimeType($file_path) {
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    
    $mime_types = [
        'mp4'  => 'video/mp4',
        'webm' => 'video/webm', 
        'avi'  => 'video/x-msvideo',
        'mov'  => 'video/quicktime',
        'mkv'  => 'video/x-matroska'
    ];
    
    return $mime_types[$extension] ?? 'video/mp4';
}

/**
 * Serve video file with proper headers and range support
 */
function serveVideoFile($file_path, $interview, $registration_id) {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $file_size = filesize($file_path);
    $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $content_type = getVideoMimeType($file_path);
    
    // Generate clean filename for download
    $clean_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $interview->FNAME . '_' . $interview->LNAME);
    $download_filename = "interview_recording_{$clean_name}_{$registration_id}.{$file_extension}";
    
    // Set video headers
    header('Content-Type: ' . $content_type);
    header('Content-Disposition: attachment; filename="' . $download_filename . '"');
    header('Content-Length: ' . $file_size);
    header('Accept-Ranges: bytes');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Handle range requests for video streaming/seeking
    if (isset($_SERVER['HTTP_RANGE'])) {
        // Parse range header
        preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches);
        $offset = intval($matches[1]);
        $end = !empty($matches[2]) ? intval($matches[2]) : $file_size - 1;
        $length = $end - $offset + 1;
        
        // Send partial content headers
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes {$offset}-{$end}/{$file_size}");
        header("Content-Length: {$length}");
        
        // Send partial file content
        $file = fopen($file_path, 'rb');
        fseek($file, $offset);
        echo fread($file, $length);
        fclose($file);
    } else {
        // Send complete file
        if (!readfile($file_path)) {
            http_response_code(500);
            echo "Error reading video file.";
        }
    }
    exit;
}

// First priority: Check for consolidated videos in tblinterviewvideos
$sql = "SELECT VIDEO_PATH FROM tblinterviewvideos WHERE REGISTRATIONID = ? ORDER BY CREATED_AT DESC LIMIT 1";
$mydb->setQuery($sql);
$mydb->bind_param('i', $registration_id);
$video_record = $mydb->loadSingleResult();

if ($video_record) {
    $potential_paths = [
        "uploads/interviews/consolidated/" . $video_record->VIDEO_PATH,
        "uploads/interviews/" . $video_record->VIDEO_PATH,
        "../uploads/interviews/consolidated/" . $video_record->VIDEO_PATH,
        "../uploads/interviews/" . $video_record->VIDEO_PATH
    ];
    
    foreach ($potential_paths as $path) {
        if (isValidVideoFile($path)) {
            serveVideoFile($path, $interview, $registration_id);
        }
    }
}

// Second priority: Check for individual recordings in tblinterviewrecordings
$sql = "SELECT FILE_PATH FROM tblinterviewrecordings WHERE REGISTRATIONID = ? ORDER BY RECORDED_AT DESC LIMIT 1";
$mydb->setQuery($sql);
$mydb->bind_param('i', $registration_id);
$recording_record = $mydb->loadSingleResult();

if ($recording_record && isValidVideoFile($recording_record->FILE_PATH)) {
    serveVideoFile($recording_record->FILE_PATH, $interview, $registration_id);
}

// Third priority: Check for demo files only if demo mode is requested
if ($demo_mode) {
    $demo_paths = [
        "interview-recordings/interview_{$registration_id}.mp4",
        "recordings/interview_{$registration_id}.mp4",
        "../interview-recordings/interview_{$registration_id}.mp4", 
        "../recordings/interview_{$registration_id}.mp4"
    ];
    
    foreach ($demo_paths as $path) {
        if (file_exists($path) && filesize($path) > 0) {
            // Check if it's actually a text file (demo content)
            $content = file_get_contents($path, false, null, 0, 100);
            
            if (!isValidVideoFile($path)) {
                // Serve as text file
                header('Content-Type: text/plain; charset=UTF-8');
                header('Content-Disposition: attachment; filename="interview_transcript_' . $registration_id . '.txt"');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                exit;
            }
        }
    }
}

// No recording found - return error page
http_response_code(404);
header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Interview Recording Not Available</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { 
            max-width: 600px; 
            background: rgba(255,255,255,0.95); 
            color: #333;
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        .icon { font-size: 4rem; margin-bottom: 20px; }
        h1 { color: #495057; margin-bottom: 20px; font-size: 2rem; }
        p { color: #6c757d; line-height: 1.6; margin-bottom: 15px; }
        .btn { 
            background: #007bff; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 8px; 
            display: inline-block; 
            margin: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn:hover { 
            background: #0056b3; 
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        }
        .info-box { 
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0; 
            border-left: 4px solid #2196f3;
            text-align: left;
        }
        .details { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0;
            text-align: left;
        }
        ul { display: inline-block; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🎥</div>
        <h1>Interview Recording Not Available</h1>
        
        <div class="details">
            <p><strong>Interview Details:</strong></p>
            <ul>
                <li><strong>Candidate:</strong> <?php echo htmlspecialchars($interview->FNAME . ' ' . $interview->LNAME); ?></li>
                <li><strong>Position:</strong> <?php echo htmlspecialchars($interview->OCCUPATIONTITLE); ?></li>
                <li><strong>Company:</strong> <?php echo htmlspecialchars($interview->COMPANYNAME); ?></li>
                <li><strong>Registration ID:</strong> <?php echo $registration_id; ?></li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>Why is this happening?</h3>
            <p><strong>No video recording file exists on the server.</strong></p>
            <p>This could be due to:</p>
            <ul>
                <li>The interview was not completed successfully</li>
                <li>Technical issues occurred during recording</li>
                <li>The recording process failed or was interrupted</li>
                <li>File storage problems on the server</li>
                <li>The recording is still being processed</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="interview-results.php" class="btn">← Back to Interview Results</a>
            <a href="interview-invitation.php" class="btn">Manage Interviews</a>
            <button onclick="window.location.reload()" class="btn">🔄 Retry</button>
        </div>
        
        <p style="font-size: 12px; color: #6c757d; margin-top: 30px; opacity: 0.8;">
            If you believe this is an error, please contact the system administrator.<br>
            Registration ID: <?php echo $registration_id; ?> | Timestamp: <?php echo date('Y-m-d H:i:s'); ?>
        </p>
    </div>
</body>
</html>