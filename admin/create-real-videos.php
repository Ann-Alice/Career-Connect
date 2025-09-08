<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

echo "<h1>🎥 Create Real Video Files for Testing</h1>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
.success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
.error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
.info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
.warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
.btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
.btn:hover { background: #0056b3; }
.btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
.btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video_file'])) {
    try {
        $registration_id = isset($_POST['registration_id']) ? intval($_POST['registration_id']) : 0;
        
        if ($registration_id <= 0) {
            throw new Exception('Invalid registration ID');
        }
        
        // Validate uploaded file
        if ($_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload failed with error code: ' . $_FILES['video_file']['error']);
        }
        
        $file_info = pathinfo($_FILES['video_file']['name']);
        $allowed_extensions = ['mp4', 'webm', 'avi', 'mov'];
        
        if (!isset($file_info['extension']) || !in_array(strtolower($file_info['extension']), $allowed_extensions)) {
            throw new Exception('Invalid file type. Please upload MP4, WebM, AVI, or MOV files only.');
        }
        
        // Create directories
        $upload_dir = '../uploads/interviews';
        $consolidated_dir = '../uploads/interviews/consolidated';
        $recordings_dir = '../uploads/recordings';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if (!is_dir($consolidated_dir)) mkdir($consolidated_dir, 0777, true);
        if (!is_dir($recordings_dir)) mkdir($recordings_dir, 0777, true);
        
        // Generate filenames
        $timestamp = time();
        $original_filename = "interview_recording_{$registration_id}_{$timestamp}." . $file_info['extension'];
        $consolidated_filename = "interview_complete_{$registration_id}_{$timestamp}." . $file_info['extension'];
        
        $original_path = $recordings_dir . '/' . $original_filename;
        $consolidated_path = $consolidated_dir . '/' . $consolidated_filename;
        
        // Move uploaded file to recordings directory
        if (!move_uploaded_file($_FILES['video_file']['tmp_name'], $original_path)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        // Copy to consolidated directory
        if (!copy($original_path, $consolidated_path)) {
            throw new Exception('Failed to create consolidated copy');
        }
        
        // Insert into tblinterviewrecordings
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE) 
                VALUES 
                ('{$registration_id}', 1, '{$original_path}', 30.0, NOW(), 1, 'essential_answer')";
        $mydb->setQuery($sql);
        if (!$mydb->executeQuery()) {
            throw new Exception('Failed to save recording record: ' . $mydb->getLastError());
        }
        
        // Insert into tblinterviewvideos
        $sql = "INSERT INTO tblinterviewvideos (REGISTRATIONID, VIDEO_PATH, CREATED_AT) 
                VALUES ('{$registration_id}', '{$consolidated_filename}', NOW())";
        $mydb->setQuery($sql);
        if (!$mydb->executeQuery()) {
            throw new Exception('Failed to save video record: ' . $mydb->getLastError());
        }
        
        echo "<div class='success'>";
        echo "<h3>✅ Video Upload Successful!</h3>";
        echo "<p><strong>Original File:</strong> $original_path</p>";
        echo "<p><strong>Consolidated File:</strong> $consolidated_path</p>";
        echo "<p><strong>File Size:</strong> " . round(filesize($original_path) / 1024 / 1024, 2) . " MB</p>";
        echo "<p>Now you can test the download functionality!</p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Upload Error: " . $e->getMessage() . "</div>";
    }
}

// Handle sample video generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_sample') {
    try {
        $registration_id = isset($_POST['registration_id']) ? intval($_POST['registration_id']) : 0;
        
        if ($registration_id <= 0) {
            throw new Exception('Invalid registration ID');
        }
        
        // Create a simple MP4 file using FFmpeg (if available) or create a dummy binary file
        $upload_dir = '../uploads/interviews';
        $consolidated_dir = '../uploads/interviews/consolidated';
        $recordings_dir = '../uploads/recordings';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if (!is_dir($consolidated_dir)) mkdir($consolidated_dir, 0777, true);
        if (!is_dir($recordings_dir)) mkdir($recordings_dir, 0777, true);
        
        $timestamp = time();
        $original_filename = "interview_recording_{$registration_id}_{$timestamp}.mp4";
        $consolidated_filename = "interview_complete_{$registration_id}_{$timestamp}.mp4";
        
        $original_path = $recordings_dir . '/' . $original_filename;
        $consolidated_path = $consolidated_dir . '/' . $consolidated_filename;
        
        // Create a more complete MP4 file structure that browsers can recognize
        // This creates a minimal but valid MP4 file with proper atom structure
        $ftyp_atom = pack('N', 32) . 'ftyp' . 'isom' . pack('N', 512) . 'isomiso2avc1mp41';
        $mdat_header = pack('N', 8) . 'mdat';
        
        // Create a minimal video data payload
        $video_data = str_repeat("\x00\x00\x00\x01\x67\x42\x00\x1E\x96\x54\x05\x01\xED\x80", 100); // Minimal H.264 data
        $mdat_atom = pack('N', strlen($video_data) + 8) . 'mdat' . $video_data;
        
        $dummy_video = $ftyp_atom . $mdat_atom;
        
        if (!file_put_contents($original_path, $dummy_video)) {
            throw new Exception('Failed to create sample video file');
        }
        if (!file_put_contents($consolidated_path, $dummy_video)) {
            throw new Exception('Failed to create consolidated video file');
        }
        
        // Insert into database
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, RECORDED_AT, CONVERSATION_TURN, QUESTION_TYPE) 
                VALUES 
                ('{$registration_id}', 1, '{$original_path}', 30.0, NOW(), 1, 'essential_answer')";
        $mydb->setQuery($sql);
        if (!$mydb->executeQuery()) {
            throw new Exception('Failed to save recording record: ' . $mydb->getLastError());
        }
        
        $sql = "INSERT INTO tblinterviewvideos (REGISTRATIONID, VIDEO_PATH, CREATED_AT) 
                VALUES ('{$registration_id}', '{$consolidated_filename}', NOW())";
        $mydb->setQuery($sql);
        if (!$mydb->executeQuery()) {
            throw new Exception('Failed to save video record: ' . $mydb->getLastError());
        }
        
        echo "<div class='success'>";
        echo "<h3>✅ Sample Video Generated!</h3>";
        echo "<p><strong>Sample File:</strong> $original_path</p>";
        echo "<p><strong>Consolidated File:</strong> $consolidated_path</p>";
        echo "<p><strong>File Size:</strong> " . round(filesize($original_path) / 1024, 2) . " KB</p>";
        echo "<p>This is a dummy MP4 file for testing download functionality.</p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Generation Error: " . $e->getMessage() . "</div>";
    }
}

// Get completed interviews
$sql = "SELECT r.REGISTRATIONID, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME,
               (SELECT COUNT(*) FROM tblinterviewrecordings ir WHERE ir.REGISTRATIONID = r.REGISTRATIONID) as recording_count,
               (SELECT COUNT(*) FROM tblinterviewvideos iv WHERE iv.REGISTRATIONID = r.REGISTRATIONID) as video_count
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
        WHERE r.INTERVIEW_STATUS = 'Completed'
        ORDER BY r.REGISTRATIONDATE DESC";

$mydb->setQuery($sql);
$interviews = $mydb->loadResultList();

echo "<div class='info'>";
echo "<h3>📝 Purpose</h3>";
echo "<p>This tool helps you create actual video files for testing the download functionality instead of getting demo text files.</p>";
echo "<p>You can either upload real video files or generate sample video files for testing.</p>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>🎯 Quick Solution for Testing</h3>";
echo "<p><strong>Option 1:</strong> Upload any MP4 video file from your computer (even a short phone video)</p>";
echo "<p><strong>Option 2:</strong> Download a test video: <a href='https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4' target='_blank' class='btn btn-success'>Download Sample MP4</a></p>";
echo "<p><strong>Option 3:</strong> Generate a basic sample file (may not play in all browsers)</p>";
echo "</div>";

if ($interviews) {
    echo "<h2>🎬 Upload or Generate Videos for Completed Interviews</h2>";
    
    echo "<div class='warning'>";
    echo "<h4>⚠️ Important Notes:</h4>";
    echo "<ul>";
    echo "<li><strong>For best results:</strong> Upload a real MP4, WebM, AVI, or MOV file from your computer</li>";
    echo "<li><strong>Quick test:</strong> Even a short phone video or downloaded sample will work</li>";
    echo "<li>Files will be stored in uploads/interviews/ and uploads/recordings/</li>";
    echo "<li>Generated samples create basic MP4 structure but may not play in all browsers</li>";
    echo "<li>After uploading, test the download from Interview Results page</li>";
    echo "</ul>";
    echo "</div>";
    
    foreach ($interviews as $interview) {
        $has_recordings = $interview->recording_count > 0;
        $has_videos = $interview->video_count > 0;
        
        echo "<div style='background: white; border-radius: 10px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>";
        echo "<h4>{$interview->FNAME} {$interview->LNAME} - {$interview->OCCUPATIONTITLE}</h4>";
        echo "<p><strong>Company:</strong> {$interview->COMPANYNAME} | <strong>Registration ID:</strong> {$interview->REGISTRATIONID}</p>";
        echo "<p><strong>Status:</strong> ";
        
        if ($has_recordings && $has_videos) {
            echo "<span style='color: #28a745;'>✅ Has both recordings and videos ({$interview->recording_count} recordings, {$interview->video_count} videos)</span>";
        } elseif ($has_recordings || $has_videos) {
            echo "<span style='color: #ffc107;'>⚠️ Partial ({$interview->recording_count} recordings, {$interview->video_count} videos)</span>";
        } else {
            echo "<span style='color: #dc3545;'>❌ No video files</span>";
        }
        echo "</p>";
        
        if (!$has_recordings || !$has_videos) {
            echo "<div style='margin-top: 15px;'>";
            
            // Upload form
            echo "<form method='POST' enctype='multipart/form-data' style='display: inline-block; margin-right: 20px;'>";
            echo "<input type='hidden' name='registration_id' value='{$interview->REGISTRATIONID}'>";
            echo "<label>Upload Video File:</label><br>";
            echo "<input type='file' name='video_file' accept='.mp4,.webm,.avi,.mov' required style='margin: 5px 0;'><br>";
            echo "<button type='submit' class='btn btn-success'>📤 Upload Real Video</button>";
            echo "</form>";
            
            // Generate sample form
            echo "<form method='POST' style='display: inline-block;'>";
            echo "<input type='hidden' name='action' value='generate_sample'>";
            echo "<input type='hidden' name='registration_id' value='{$interview->REGISTRATIONID}'>";
            echo "<button type='submit' class='btn btn-warning'>🎭 Generate Sample Video</button>";
            echo "</form>";
            
            echo "</div>";
        } else {
            echo "<div style='margin-top: 15px;'>";
            echo "<a href='download-video.php?id={$interview->REGISTRATIONID}' class='btn btn-success'>📥 Test Download</a>";
            echo "<a href='view-recording.php?id={$interview->REGISTRATIONID}' class='btn' target='_blank'>👁️ Test View</a>";
            echo "</div>";
        }
        
        echo "</div>";
    }
} else {
    echo "<div class='error'>";
    echo "<h3>❌ No Completed Interviews Found</h3>";
    echo "<p>You need to have completed interviews in the system first.</p>";
    echo "<p>Make sure you have:</p>";
    echo "<ul>";
    echo "<li>Applicants in the system</li>";
    echo "<li>Job registrations with INTERVIEW_STATUS = 'Completed'</li>";
    echo "<li>Completed AI interviews</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div style='margin-top: 30px; text-align: center;'>";
echo "<a href='interview-results.php' class='btn'>📊 Back to Interview Results</a>";
echo "<a href='video-validation.php' class='btn'>🔍 Video Validation Tool</a>";
echo "</div>";

echo "<div class='info' style='margin-top: 30px;'>";
echo "<h3>🔧 How This Works:</h3>";
echo "<ol>";
echo "<li><strong>Upload Real Video:</strong> Upload an actual MP4/WebM video file</li>";
echo "<li><strong>Generate Sample:</strong> Create a dummy video file with proper headers</li>";
echo "<li><strong>Database Storage:</strong> Files are saved to both tblinterviewrecordings and tblinterviewvideos tables</li>";
echo "<li><strong>Test Download:</strong> Go to Interview Results and try downloading the video</li>";
echo "</ol>";
echo "</div>";
?>