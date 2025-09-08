<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

$registration_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($registration_id <= 0) {
    die('Invalid registration ID');
}

// Get interview information
$sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
        WHERE r.REGISTRATIONID = $registration_id";

$mydb->setQuery($sql);
$interview = $mydb->loadSingleResult();

if (!$interview) {
    die('Interview not found');
}

// Check for actual video recordings first
$actual_video_paths = [
    "../uploads/interviews/consolidated/interview_complete_{$registration_id}_*.webm",
    "../uploads/interviews/interview_{$registration_id}_*.webm"
];

$actual_recording_file = null;
foreach ($actual_video_paths as $pattern) {
    $matches = glob($pattern);
    if ($matches && count($matches) > 0) {
        $actual_recording_file = $matches[0]; // Get the first match
        break;
    }
}

// If no actual video, check database for video records
if (!$actual_recording_file) {
    $sql = "SELECT VIDEO_PATH FROM tblinterviewvideos WHERE REGISTRATIONID = $registration_id ORDER BY CREATED_AT DESC LIMIT 1";
    $mydb->setQuery($sql);
    $video_record = $mydb->loadSingleResult();
    
    if ($video_record) {
        $potential_path = "../uploads/interviews/" . $video_record->VIDEO_PATH;
        if (file_exists($potential_path) && filesize($potential_path) > 0) {
            $actual_recording_file = $potential_path;
        }
    }
}

// Define possible demo recording file locations
$recording_paths = [
    "../interview-recordings/interview_{$registration_id}.mp4",
    "../recordings/interview_{$registration_id}.mp4", 
    "../uploads/recordings/interview_{$registration_id}.mp4",
    "../interview-system/recordings/interview_{$registration_id}.mp4"
];

$demo_recording_file = null;
if (!$actual_recording_file) {
    foreach ($recording_paths as $path) {
        if (file_exists($path) && filesize($path) > 0) {
            $demo_recording_file = $path;
            break;
        }
    }
}

// Determine what content to show
$show_actual_video = false;
$recording_file = null;

if ($actual_recording_file) {
    $recording_file = $actual_recording_file;
    $show_actual_video = true;
} elseif ($demo_recording_file) {
    $recording_file = $demo_recording_file;
    $show_actual_video = false;
} else {
    // Create demo recording file for testing
    $recording_file = "../interview-recordings/interview_{$registration_id}.mp4";
    
    // Create directory if it doesn't exist
    $recording_dir = dirname($recording_file);
    if (!is_dir($recording_dir)) {
        mkdir($recording_dir, 0777, true);
    }
    
    // Create a demo text file that can be viewed (since we don't have actual video)
    if (!file_exists($recording_file)) {
        $demo_content = "DEMO INTERVIEW RECORDING\n";
        $demo_content .= "========================\n\n";
        $demo_content .= "Registration ID: {$registration_id}\n";
        $demo_content .= "Candidate: {$interview->FNAME} {$interview->LNAME}\n";
        $demo_content .= "Position: {$interview->OCCUPATIONTITLE}\n";
        $demo_content .= "Company: {$interview->COMPANYNAME}\n";
        $demo_content .= "Recording Date: " . date('Y-m-d H:i:s') . "\n\n";
        $demo_content .= "This is a demonstration file showing where the actual interview recording would be stored.\n\n";
        $demo_content .= "In production, this would be a video file (.mp4 or .webm) containing the actual interview session.\n";
        
        file_put_contents($recording_file, $demo_content);
        chmod($recording_file, 0644);
    }
    $show_actual_video = false;
}

// Get file info
$file_size = filesize($recording_file);
$demo_content = file_get_contents($recording_file);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Interview Recording - <?php echo htmlspecialchars($interview->FNAME . ' ' . $interview->LNAME); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .recording-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .demo-video {
            background: #000;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            color: white;
            margin: 20px 0;
            position: relative;
        }
        .demo-content {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
        }
        .btn-action {
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="recording-container">
        <div class="header">
            <h3>
                <i class="fa fa-video-camera"></i> 
                Interview Recording
            </h3>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">
                <?php echo htmlspecialchars($interview->FNAME . ' ' . $interview->LNAME); ?> - 
                <?php echo htmlspecialchars($interview->OCCUPATIONTITLE); ?>
            </p>
        </div>
        
        <?php if ($show_actual_video): ?>
            <!-- Actual Video Player -->
            <div style="background: #000; border-radius: 10px; margin: 20px 0; overflow: hidden;">
                <video width="100%" height="400" controls style="background: #000;">
                    <source src="serve-video.php?id=<?php echo $registration_id; ?>" type="video/webm">
                    <source src="serve-video.php?id=<?php echo $registration_id; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div style="background: rgba(0,255,0,0.1); border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    <div style="text-align: left; margin-bottom: 10px;">
                        <strong>Registration ID:</strong> <?php echo $registration_id; ?><br>
                        <strong>Video File:</strong> <?php echo basename($recording_file); ?><br>
                        <strong>File Size:</strong> <?php echo round(filesize($recording_file) / 1024 / 1024, 2); ?> MB
                    </div>
                    <div style="text-align: right;">
                        <strong>Status:</strong> <span style="color: #28a745;">✅ Recording Available</span><br>
                        <strong>Type:</strong> Actual Interview Video<br>
                        <strong>Quality:</strong> <?php echo pathinfo($recording_file, PATHINFO_EXTENSION); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Demo Content Display -->
            <div class="demo-video">
                <i class="fa fa-play-circle" style="font-size: 4rem; margin-bottom: 15px; opacity: 0.7;"></i>
                <h4>Demo Interview Recording</h4>
                <p style="opacity: 0.8; margin-bottom: 20px;">This is a demonstration of the interview recording system</p>
                
                <div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                        <div style="text-align: left; margin-bottom: 10px;">
                            <strong>Registration ID:</strong> <?php echo $registration_id; ?><br>
                            <strong>Company:</strong> <?php echo htmlspecialchars($interview->COMPANYNAME); ?><br>
                            <strong>File Size:</strong> <?php echo round(filesize($recording_file) / 1024, 2); ?> KB
                        </div>
                        <div style="text-align: right;">
                            <strong>Status:</strong> Demo Mode<br>
                            <strong>Duration:</strong> N/A<br>
                            <strong>Quality:</strong> Demo
                        </div>
                    </div>
                </div>
                
                <p style="font-size: 12px; opacity: 0.6; margin-top: 20px;">
                    In production, this would show the actual video recording of the AI interview session
                </p>
            </div>
            
            <div class="demo-content">
                <?php echo htmlspecialchars(file_get_contents($recording_file)); ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center">
            <button type="button" class="btn btn-success btn-action" onclick="downloadRecording()">
                <i class="fa fa-download"></i> Download Recording
            </button>
            <button type="button" class="btn btn-info btn-action" onclick="window.opener.location.reload(); window.close();">
                <i class="fa fa-refresh"></i> Refresh & Close
            </button>
            <button type="button" class="btn btn-default btn-action" onclick="window.close();">
                <i class="fa fa-times"></i> Close Window
            </button>
        </div>
        
        <div style="background: #e9ecef; border-radius: 8px; padding: 15px; margin-top: 20px;">
            <h5><i class="fa fa-info-circle"></i> Production Features</h5>
            <ul style="margin-bottom: 0;">
                <li>Full HD video recording (1080p)</li>
                <li>Audio analysis and transcription</li>
                <li>AI behavioral analysis overlay</li>
                <li>Timestamp markers for key moments</li>
                <li>Download in multiple formats (MP4, WebM)</li>
                <li>Playback speed controls</li>
            </ul>
        </div>
    </div>
    
    <script>
    function downloadRecording() {
        <?php if ($show_actual_video): ?>
        // Download actual video
        if (window.opener && window.opener.downloadVideoRecording) {
            window.opener.downloadVideoRecording(<?php echo $registration_id; ?>);
        } else {
            window.location.href = 'download-video.php?id=<?php echo $registration_id; ?>';
        }
        <?php else: ?>
        // Download demo content
        if (window.opener && window.opener.downloadVideoRecording) {
            window.opener.downloadVideoRecording(<?php echo $registration_id; ?>);
        } else {
            window.location.href = 'download-recording.php?id=<?php echo $registration_id; ?>&demo=1';
        }
        <?php endif; ?>
    }
    </script>
</body>
</html>