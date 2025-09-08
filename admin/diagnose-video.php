<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

$registration_id = isset($_GET['id']) ? intval($_GET['id']) : 3; // Default to 3 (James Carta)
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Diagnostics - Registration ID: <?php echo $registration_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
        .btn:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .test-section { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <h1>🔍 Video Diagnostic Report - Registration ID: <?php echo $registration_id; ?></h1>
    
    <?php
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
        echo "<div class='error'>❌ Interview not found for Registration ID: $registration_id</div>";
        exit;
    }
    
    echo "<div class='info'>";
    echo "<h3>📋 Interview Details</h3>";
    echo "<p><strong>Candidate:</strong> {$interview->FNAME} {$interview->LNAME}</p>";
    echo "<p><strong>Position:</strong> {$interview->OCCUPATIONTITLE}</p>";
    echo "<p><strong>Company:</strong> {$interview->COMPANYNAME}</p>";
    echo "</div>";
    ?>
    
    <div class="test-section">
        <h3>1️⃣ Database Video Records</h3>
        <?php
        // Check tblinterviewvideos
        echo "<h4>Consolidated Videos (tblinterviewvideos)</h4>";
        $sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = $registration_id";
        $mydb->setQuery($sql);
        $videos = $mydb->loadResultList();
        
        if ($videos) {
            foreach ($videos as $v) {
                echo "<div class='success'>";
                echo "<p><strong>✅ Database Record Found:</strong></p>";
                echo "<p>ID: {$v->ID} | Video Path: {$v->VIDEO_PATH} | Created: {$v->CREATED_AT}</p>";
                
                // Check file existence
                $potential_paths = [
                    "../uploads/interviews/consolidated/{$v->VIDEO_PATH}",
                    "../uploads/interviews/{$v->VIDEO_PATH}",
                    "uploads/interviews/consolidated/{$v->VIDEO_PATH}",
                    "uploads/interviews/{$v->VIDEO_PATH}"
                ];
                
                $file_found = false;
                foreach ($potential_paths as $path) {
                    if (file_exists($path)) {
                        $size = filesize($path);
                        echo "<p><strong>✅ File Found:</strong> $path ($size bytes)</p>";
                        
                        // Check if it's a real video file
                        $file_content = file_get_contents($path, false, null, 0, 100);
                        $is_video = (
                            strpos($file_content, 'ftyp') !== false ||  // MP4
                            strpos($file_content, 'WEBM') !== false ||  // WebM
                            strpos($file_content, 'RIFF') !== false    // AVI
                        );
                        
                        if ($is_video) {
                            echo "<p><strong>✅ Video Signature:</strong> Valid video file detected</p>";
                        } else {
                            echo "<p><strong>⚠️ Video Signature:</strong> This appears to be a text file, not a video</p>";
                            echo "<pre>" . htmlspecialchars(substr($file_content, 0, 200)) . "</pre>";
                        }
                        $file_found = true;
                        break;
                    }
                }
                
                if (!$file_found) {
                    echo "<p><strong>❌ File Status:</strong> No file found at any expected location</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='warning'>⚠️ No consolidated videos found in database</div>";
        }
        
        // Check tblinterviewrecordings
        echo "<h4>Individual Recordings (tblinterviewrecordings)</h4>";
        $sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = $registration_id ORDER BY QUESTION_NUMBER";
        $mydb->setQuery($sql);
        $recordings = $mydb->loadResultList();
        
        if ($recordings) {
            foreach ($recordings as $r) {
                echo "<div class='info'>";
                echo "<p><strong>📹 Recording Found:</strong> Question {$r->QUESTION_NUMBER}</p>";
                echo "<p>File: {$r->FILE_PATH} | Duration: {$r->DURATION}s</p>";
                
                if (file_exists($r->FILE_PATH)) {
                    $size = filesize($r->FILE_PATH);
                    echo "<p><strong>✅ File Exists:</strong> $size bytes</p>";
                    
                    // Check video signature
                    $file_content = file_get_contents($r->FILE_PATH, false, null, 0, 100);
                    $is_video = (
                        strpos($file_content, 'ftyp') !== false ||
                        strpos($file_content, 'WEBM') !== false ||
                        strpos($file_content, 'RIFF') !== false
                    );
                    
                    if ($is_video) {
                        echo "<p><strong>✅ Video File:</strong> Valid video detected</p>";
                    } else {
                        echo "<p><strong>❌ Not Video:</strong> This is a text file</p>";
                    }
                } else {
                    echo "<p><strong>❌ File Missing:</strong> File does not exist</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='warning'>⚠️ No individual recordings found in database</div>";
        }
        ?>
    </div>
    
    <div class="test-section">
        <h3>2️⃣ Video Serving Test</h3>
        <?php
        echo "<p>Testing the video serving endpoint that the video player uses:</p>";
        
        $serve_url = "serve-video.php?id=$registration_id";
        echo "<p><strong>Video URL:</strong> <a href='$serve_url' target='_blank'>$serve_url</a></p>";
        
        // Test the serve-video.php endpoint
        echo "<div class='info'>";
        echo "<h4>🔍 Testing Video Server Response</h4>";
        
        $context = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'header' => "User-Agent: Mozilla/5.0\r\n"
            ]
        ]);
        
        $headers = @get_headers("http://localhost/eris/admin/$serve_url", 1, $context);
        
        if ($headers) {
            $status = $headers[0];
            echo "<p><strong>HTTP Status:</strong> $status</p>";
            
            if (strpos($status, '200') !== false) {
                echo "<p><strong>✅ Response:</strong> Video server is responding correctly</p>";
                if (isset($headers['Content-Type'])) {
                    echo "<p><strong>Content-Type:</strong> {$headers['Content-Type']}</p>";
                }
                if (isset($headers['Content-Length'])) {
                    echo "<p><strong>Content-Length:</strong> {$headers['Content-Length']} bytes</p>";
                }
            } else {
                echo "<p><strong>❌ Error:</strong> Video server returned error: $status</p>";
            }
        } else {
            echo "<p><strong>❌ Connection Failed:</strong> Cannot connect to video server</p>";
        }
        echo "</div>";
        ?>
    </div>
    
    <div class="test-section">
        <h3>3️⃣ Live Video Player Test</h3>
        <p>Testing the actual video player that would be shown in "Watch Recording":</p>
        
        <div style="background: #000; border-radius: 10px; margin: 20px 0; overflow: hidden;">
            <video width="100%" height="400" controls style="background: #000;" id="testVideo">
                <source src="serve-video.php?id=<?php echo $registration_id; ?>" type="video/webm">
                <source src="serve-video.php?id=<?php echo $registration_id; ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        
        <div id="videoStatus" class="info">
            <p><strong>Video Status:</strong> <span id="statusText">Loading...</span></p>
        </div>
        
        <script>
        const video = document.getElementById('testVideo');
        const statusText = document.getElementById('statusText');
        const videoStatus = document.getElementById('videoStatus');
        
        video.addEventListener('loadstart', () => {
            statusText.textContent = 'Started loading video...';
        });
        
        video.addEventListener('loadedmetadata', () => {
            statusText.textContent = 'Video metadata loaded successfully!';
            videoStatus.className = 'success';
        });
        
        video.addEventListener('loadeddata', () => {
            statusText.textContent = 'Video data loaded - ready to play!';
            videoStatus.className = 'success';
        });
        
        video.addEventListener('canplay', () => {
            statusText.textContent = 'Video can start playing!';
            videoStatus.className = 'success';
        });
        
        video.addEventListener('error', (e) => {
            statusText.textContent = 'Video failed to load: ' + e.message;
            videoStatus.className = 'error';
            console.error('Video error:', e);
        });
        
        video.addEventListener('stalled', () => {
            statusText.textContent = 'Video loading stalled';
            videoStatus.className = 'warning';
        });
        
        // Check after 5 seconds
        setTimeout(() => {
            if (video.readyState === 0) {
                statusText.textContent = 'Video failed to load - no video file available or file is corrupted';
                videoStatus.className = 'error';
            }
        }, 5000);
        </script>
    </div>
    
    <div class="test-section">
        <h3>4️⃣ Quick Actions</h3>
        <a href="create-real-videos.php" class="btn">📤 Upload Real Video File</a>
        <a href="view-recording.php?id=<?php echo $registration_id; ?>" class="btn">👁️ Open Watch Recording</a>
        <a href="download-recording.php?id=<?php echo $registration_id; ?>" class="btn">📥 Test Download</a>
        <a href="interview-results.php" class="btn">📊 Back to Results</a>
    </div>
    
    <div class="test-section">
        <h3>5️⃣ Diagnosis Summary</h3>
        <div class="warning">
            <h4>🩺 Most Likely Issues:</h4>
            <ol>
                <li><strong>No actual video recorded:</strong> The interview system may not be recording videos properly during interviews</li>
                <li><strong>Demo content only:</strong> System has text files instead of video files</li>
                <li><strong>File path issues:</strong> Video files exist but in wrong location</li>
                <li><strong>Corrupted files:</strong> Video files exist but are corrupted/empty</li>
            </ol>
            
            <h4>✅ To Fix:</h4>
            <ol>
                <li>Check the video player test above - if it shows an error, no real video exists</li>
                <li>Use "Upload Real Video File" to add a test video</li>
                <li>Check if the interview recording system is working during actual interviews</li>
                <li>Verify the consolidated video creation process in the interview completion</li>
            </ol>
        </div>
    </div>
</body>
</html>