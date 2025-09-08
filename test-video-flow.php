<!DOCTYPE html>
<html>
<head>
    <title>Test Complete Video Interview Flow</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; color: white; text-decoration: none; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
        .test-section { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🎥 Test Complete Video Interview Flow</h1>
    
    <div class="info">
        <h3>📋 What This Test Does</h3>
        <p>This page will help you test the complete video interview recording and download flow:</p>
        <ol>
            <li><strong>Test Interview Completion:</strong> Simulate completing an interview with video consolidation</li>
            <li><strong>Test Video Download:</strong> Download the consolidated video from the interview results page</li>
            <li><strong>Verify Video Playback:</strong> Ensure downloaded videos are in proper MP4/WebM format</li>
        </ol>
    </div>
    
    <div class="test-section">
        <h3>🎬 Step 1: Start a Test Interview</h3>
        <p>To test the complete flow, you need an active interview session:</p>
        <?php
        require_once('include/initialize.php');
        
        // Get a recent interview invitation
        $sql = "SELECT i.TOKEN, r.REGISTRATIONID, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
                FROM tblinterviewinvitations i
                JOIN tbljobregistration r ON i.REGISTRATIONID = r.REGISTRATIONID
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID
                JOIN tbljob j ON r.JOBID = j.JOBID
                WHERE i.EXPIRY_DATE > NOW()
                ORDER BY i.CREATED_AT DESC
                LIMIT 5";
        $mydb->setQuery($sql);
        $invitations = $mydb->loadResultList();
        
        if ($invitations) {
            echo "<div class='success'>";
            echo "<h4>✅ Available Interview Links:</h4>";
            foreach ($invitations as $inv) {
                $interview_url = "interview.php?token=" . $inv->TOKEN;
                echo "<p><strong>{$inv->FNAME} {$inv->LNAME}</strong> - {$inv->OCCUPATIONTITLE} (Registration ID: {$inv->REGISTRATIONID})</p>";
                echo "<a href='$interview_url' class='btn btn-success' target='_blank'>🎤 Start Interview</a>";
                echo "<a href='admin/interview-results.php' class='btn' target='_blank'>📊 View Results</a>";
                echo "<hr>";
            }
            echo "</div>";
        } else {
            echo "<div class='warning'>";
            echo "<h4>⚠️ No Active Interview Invitations Found</h4>";
            echo "<p>You need to create interview invitations first. Go to:</p>";
            echo "<a href='admin/interview-invitation.php' class='btn btn-warning'>📧 Create Interview Invitations</a>";
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="test-section">
        <h3>🔄 Step 2: Complete the Interview Flow</h3>
        <div class="info">
            <h4>Complete Interview Process:</h4>
            <ol>
                <li>Click "Start Interview" above to open the interview page</li>
                <li>Allow camera and microphone permissions</li>
                <li>Select voice preference (male/female)</li>
                <li>Click "Start Interview" button</li>
                <li>Answer a few questions (speak for a few seconds each)</li>
                <li>Click "Stop Answering" after each response</li>
                <li>Click "Complete Interview" when done</li>
                <li>Rate your experience or skip rating</li>
                <li>Click final "Complete Interview" button</li>
            </ol>
        </div>
    </div>
    
    <div class="test-section">
        <h3>📥 Step 3: Test Video Download</h3>
        <div class="info">
            <h4>Download Process:</h4>
            <ol>
                <li>Go to <a href="admin/interview-results.php" target="_blank">Interview Results</a></li>
                <li>Find your completed interview</li>
                <li>Click "Download Recording" button</li>
                <li>You should get an actual video file (MP4/WebM) instead of demo text</li>
                <li>Verify the video file plays in your media player</li>
            </ol>
        </div>
        
        <a href="admin/interview-results.php" class="btn btn-success">📊 Go to Interview Results</a>
        <a href="video-status.php" class="btn">🔍 Check Video Status</a>
    </div>
    
    <div class="test-section">
        <h3>🛠️ Step 4: Troubleshooting</h3>
        <div class="warning">
            <h4>If You Still Get Demo Content:</h4>
            <ol>
                <li><strong>Use the Upload Tool:</strong> <a href="admin/create-real-videos.php" class="btn btn-warning">📤 Upload Real Videos</a></li>
                <li><strong>Check Video Status:</strong> <a href="video-status.php" class="btn">🔍 Video Status Checker</a></li>
                <li><strong>Validate System:</strong> <a href="admin/video-validation.php" class="btn">✅ Video Validation</a></li>
            </ol>
        </div>
    </div>
    
    <div class="test-section">
        <h3>🎯 What Should Happen</h3>
        <div class="success">
            <h4>✅ Expected Results:</h4>
            <ul>
                <li><strong>During Interview:</strong> Video recordings are saved to the database as you answer questions</li>
                <li><strong>On Completion:</strong> A consolidated video is created and saved to the `tblinterviewvideos` table</li>
                <li><strong>On Download:</strong> You get an actual MP4/WebM video file that plays in media players</li>
                <li><strong>File Location:</strong> Videos are stored in `uploads/interviews/consolidated/` directory</li>
                <li><strong>Database Records:</strong> Both individual recordings and consolidated video are in the database</li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h3>🔧 Technical Implementation</h3>
        <div class="info">
            <h4>How It Works:</h4>
            <ul>
                <li><strong>Recording:</strong> Each question response is recorded and saved via `simple-upload-working.php`</li>
                <li><strong>Consolidation:</strong> On completion, a final 5-second consolidated video is created</li>
                <li><strong>Storage:</strong> Consolidated video saved via `save-consolidated-video.php`</li>
                <li><strong>Download:</strong> Interview Results page downloads from `tblinterviewvideos` table first</li>
                <li><strong>Fallback:</strong> If no consolidated video, downloads individual recordings</li>
            </ul>
        </div>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="interview.php" class="btn btn-success">🎤 Test Interview System</a>
        <a href="admin/interview-results.php" class="btn">📊 View Results</a>
        <a href="admin/create-real-videos.php" class="btn btn-warning">📤 Upload Videos</a>
    </div>
    
    <div class="success">
        <h3>🎉 Success Criteria</h3>
        <p><strong>The implementation is successful when:</strong></p>
        <ul>
            <li>✅ Interview videos are recorded during the session</li>
            <li>✅ Complete interview button creates consolidated video</li>
            <li>✅ Interview results page shows "Download Recording" button</li>
            <li>✅ Downloaded file is actual video (MP4/WebM) that plays</li>
            <li>✅ No more demo text files in downloads</li>
        </ul>
    </div>
</body>
</html>