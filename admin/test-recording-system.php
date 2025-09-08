<!DOCTYPE html>
<html>
<head>
    <title>🔧 Interview Recording System Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .section { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .code { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; font-family: monospace; font-size: 12px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        video { width: 100%; max-width: 400px; background: #000; border-radius: 10px; margin: 20px 0; }
        .recording { border: 3px solid #dc3545 !important; animation: recording-pulse 1s infinite; }
        @keyframes recording-pulse { 0%, 100% { border-color: #dc3545; } 50% { border-color: #ff6b7a; } }
    </style>
</head>
<body>
    <h1>🔧 Interview Recording System Test</h1>
    
    <div class="info">
        <h3>📋 This tool tests the complete recording upload and download system</h3>
        <p>We'll verify that recordings can be properly uploaded, stored, and downloaded with correct MIME types and headers.</p>
    </div>

<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    echo "<div class='error'>❌ Please log in as admin first</div>";
    echo "<a href='login.php' class='btn'>Login</a>";
    exit;
}

echo "<div class='section'>";
echo "<h2>1. 🗃️ Database Structure Check</h2>";

// Check database tables
$tables_to_check = [
    'tblinterviewvideos' => ['VIDEOID', 'REGISTRATIONID', 'VIDEO_PATH', 'CREATED_AT'],
    'tblinterviewrecordings' => ['RECORDINGID', 'REGISTRATIONID', 'QUESTION_NUMBER', 'FILE_PATH', 'DURATION', 'RECORDED_AT', 'CONVERSATION_TURN', 'QUESTION_TYPE', 'TRANSCRIPT']
];

foreach ($tables_to_check as $table => $required_columns) {
    echo "<h4>Checking table: $table</h4>";
    
    try {
        $sql = "DESCRIBE $table";
        $mydb->setQuery($sql);
        $columns = $mydb->loadResultList();
        
        if ($columns) {
            echo "<div class='success'>✅ Table '$table' exists</div>";
            
            $existing_columns = array_map(function($col) { return $col->Field; }, $columns);
            $missing_columns = array_diff($required_columns, $existing_columns);
            
            if (empty($missing_columns)) {
                echo "<div class='success'>✅ All required columns present</div>";
            } else {
                echo "<div class='error'>❌ Missing columns: " . implode(', ', $missing_columns) . "</div>";
            }
            
            // Show table structure
            echo "<table><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
            foreach ($columns as $col) {
                echo "<tr><td>{$col->Field}</td><td>{$col->Type}</td><td>{$col->Key}</td></tr>";
            }
            echo "</table>";
            
        } else {
            echo "<div class='error'>❌ Table '$table' does not exist</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error checking table '$table': " . $e->getMessage() . "</div>";
    }
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>2. 📁 File System Check</h2>";

// Check upload directories
$upload_dirs = [
    'uploads',
    'uploads/interviews',  
    'uploads/interviews/consolidated',
    'uploads/recordings'
];

foreach ($upload_dirs as $dir) {
    $full_path = "../$dir";
    echo "<h4>Checking directory: $dir</h4>";
    
    if (is_dir($full_path)) {
        echo "<div class='success'>✅ Directory exists</div>";
        
        if (is_writable($full_path)) {
            echo "<div class='success'>✅ Directory is writable</div>";
        } else {
            echo "<div class='error'>❌ Directory is not writable</div>";
        }
        
        $files = scandir($full_path);
        $files = array_filter($files, function($file) { return !in_array($file, ['.', '..']); });
        
        if (count($files) > 0) {
            echo "<p><strong>Files found:</strong> " . count($files) . "</p>";
        } else {
            echo "<div class='warning'>⚠️ No files in directory</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Directory does not exist</div>";
        
        if (mkdir($full_path, 0755, true)) {
            echo "<div class='success'>✅ Directory created successfully</div>";
        } else {
            echo "<div class='error'>❌ Failed to create directory</div>";
        }
    }
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>3. 🎥 Live Recording Test</h2>";

echo "<div class='info'>";
echo "<h4>Test the complete recording workflow:</h4>";
echo "<ol>";
echo "<li>Record a short video clip</li>";
echo "<li>Upload it to the server</li>";
echo "<li>Verify file storage</li>";
echo "<li>Test download with proper headers</li>";
echo "</ol>";
echo "</div>";

?>

<div style="text-align: center;">
    <video id="testVideo" autoplay muted playsinline></video>
    
    <div>
        <button id="startCamera" class="btn btn-success">📹 Start Camera</button>
        <button id="startRecording" class="btn btn-success" disabled>🔴 Start Recording</button>
        <button id="stopRecording" class="btn btn-danger" disabled>⏹️ Stop Recording</button>
        <button id="uploadRecording" class="btn" disabled>📤 Upload Recording</button>
    </div>
    
    <div id="recordingStatus" style="margin: 20px 0; padding: 15px; border-radius: 5px;"></div>
</div>

<script>
let stream = null;
let mediaRecorder = null;
let recordedChunks = [];
let recordedBlob = null;
let isRecording = false;

function updateStatus(message, type = 'info') {
    const statusDiv = document.getElementById('recordingStatus');
    statusDiv.textContent = message;
    statusDiv.className = type;
    console.log(message);
}

document.getElementById('startCamera').addEventListener('click', async () => {
    try {
        updateStatus('Requesting camera access...', 'info');
        
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { width: 640, height: 480 }, 
            audio: true 
        });
        
        const video = document.getElementById('testVideo');
        video.srcObject = stream;
        
        updateStatus('✅ Camera ready! You can now start recording.', 'success');
        
        document.getElementById('startCamera').disabled = true;
        document.getElementById('startRecording').disabled = false;
        
    } catch (error) {
        updateStatus('❌ Camera access failed: ' + error.message, 'error');
    }
});

document.getElementById('startRecording').addEventListener('click', () => {
    if (!stream) {
        updateStatus('❌ No camera stream available', 'error');
        return;
    }
    
    try {
        recordedChunks = [];
        
        let mimeType = 'video/webm;codecs=vp8,opus';
        if (!MediaRecorder.isTypeSupported(mimeType)) {
            mimeType = 'video/webm';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'video/mp4';
            }
        }
        
        mediaRecorder = new MediaRecorder(stream, {
            mimeType: mimeType,
            videoBitsPerSecond: 500000,
            audioBitsPerSecond: 64000
        });
        
        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.push(event.data);
                updateStatus(`Recording... ${recordedChunks.length} chunks captured`, 'info');
            }
        };
        
        mediaRecorder.onstart = () => {
            updateStatus('🔴 Recording in progress...', 'warning');
            isRecording = true;
            
            const video = document.getElementById('testVideo');
            video.classList.add('recording');
            
            document.getElementById('startRecording').disabled = true;
            document.getElementById('stopRecording').disabled = false;
        };
        
        mediaRecorder.onstop = () => {
            updateStatus('⏹️ Recording stopped', 'info');
            isRecording = false;
            
            const video = document.getElementById('testVideo');
            video.classList.remove('recording');
            
            document.getElementById('startRecording').disabled = false;
            document.getElementById('stopRecording').disabled = true;
            
            if (recordedChunks.length > 0) {
                recordedBlob = new Blob(recordedChunks, { type: mimeType });
                updateStatus(`✅ Recording complete: ${Math.round(recordedBlob.size/1024)} KB`, 'success');
                
                document.getElementById('uploadRecording').disabled = false;
            } else {
                updateStatus('❌ No recording data captured', 'error');
            }
        };
        
        mediaRecorder.onerror = (event) => {
            updateStatus('❌ Recording error: ' + event.error, 'error');
        };
        
        mediaRecorder.start(1000);
        
    } catch (error) {
        updateStatus('❌ Failed to start recording: ' + error.message, 'error');
    }
});

document.getElementById('stopRecording').addEventListener('click', () => {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
    }
});

document.getElementById('uploadRecording').addEventListener('click', async () => {
    if (!recordedBlob) {
        updateStatus('❌ No recording to upload', 'error');
        return;
    }
    
    try {
        updateStatus('📤 Uploading recording...', 'info');
        
        const formData = new FormData();
        const timestamp = Date.now();
        formData.append('video', recordedBlob, `test-recording-${timestamp}.webm`);
        formData.append('test', 'true');
        formData.append('timestamp', timestamp);
        formData.append('duration', 5.0);
        formData.append('conversationTurn', 1);
        formData.append('questionType', 'test_upload');
        formData.append('currentEssentialQuestion', 0);
        formData.append('transcript', 'This is a test recording for system verification');
        
        const response = await fetch('../upload-recording-enhanced.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                updateStatus(`✅ Upload successful! File: ${result.filename} (${Math.round(result.file_size/1024)} KB)`, 'success');
            } else {
                updateStatus(`❌ Upload failed: ${result.error}`, 'error');
            }
        } else {
            updateStatus(`❌ Upload failed: ${response.status} ${response.statusText}`, 'error');
        }
        
    } catch (error) {
        updateStatus('❌ Upload error: ' + error.message, 'error');
    }
});
</script>

<?php

echo "</div>";

// Check existing recordings
echo "<div class='section'>";
echo "<h2>4. 📊 Existing Recordings Check</h2>";

try {
    // Check for existing recordings
    $sql = "SELECT COUNT(*) as total FROM tblinterviewrecordings";
    $mydb->setQuery($sql);
    $recordings_count = $mydb->loadSingleResult();
    
    $sql = "SELECT COUNT(*) as total FROM tblinterviewvideos";
    $mydb->setQuery($sql);
    $videos_count = $mydb->loadSingleResult();
    
    echo "<h4>Database Records:</h4>";
    echo "<p><strong>Interview Recordings:</strong> {$recordings_count->total}</p>";
    echo "<p><strong>Interview Videos:</strong> {$videos_count->total}</p>";
    
    if ($recordings_count->total > 0) {
        echo "<h4>Recent Recordings:</h4>";
        $sql = "SELECT r.*, jr.REGISTRATIONID, a.FNAME, a.LNAME 
                FROM tblinterviewrecordings r 
                LEFT JOIN tbljobregistration jr ON r.REGISTRATIONID = jr.REGISTRATIONID 
                LEFT JOIN tblapplicants a ON jr.APPLICANTID = a.APPLICANTID 
                ORDER BY r.RECORDED_AT DESC LIMIT 5";
        $mydb->setQuery($sql);
        $recent_recordings = $mydb->loadResultList();
        
        if ($recent_recordings) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Candidate</th><th>File Path</th><th>Size</th><th>Exists</th><th>Actions</th></tr>";
            
            foreach ($recent_recordings as $rec) {
                $file_exists = file_exists($rec->FILE_PATH);
                $file_size = $file_exists ? filesize($rec->FILE_PATH) : 0;
                $candidate_name = ($rec->FNAME ?? 'Unknown') . ' ' . ($rec->LNAME ?? 'User');
                
                echo "<tr>";
                echo "<td>{$rec->REGISTRATIONID}</td>";
                echo "<td>{$candidate_name}</td>";
                echo "<td>" . basename($rec->FILE_PATH) . "</td>";
                echo "<td>" . round($file_size/1024) . " KB</td>";
                echo "<td>" . ($file_exists ? '✅' : '❌') . "</td>";
                echo "<td>";
                if ($file_exists) {
                    echo "<a href='download-recording.php?id={$rec->REGISTRATIONID}' class='btn btn-success'>📥 Download</a>";
                } else {
                    echo "<span style='color: #6c757d;'>No file</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error checking recordings: " . $e->getMessage() . "</div>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>5. 🔧 System Fixes Applied</h2>";

echo "<div class='success'>";
echo "<h4>✅ Upload System Enhancements:</h4>";
echo "<ul>";
echo "<li><strong>Enhanced File Storage:</strong> Files are now stored in organized directory structure (uploads/interviews/[registration_id]/)</li>";
echo "<li><strong>Unique Filenames:</strong> Generated with timestamp to prevent conflicts</li>";
echo "<li><strong>File Extension Validation:</strong> Supports webm, mp4, avi, mov formats</li>";
echo "<li><strong>Database Integration:</strong> File paths stored in database with metadata</li>";
echo "<li><strong>Error Handling:</strong> Comprehensive error logging and user feedback</li>";
echo "</ul>";
echo "</div>";

echo "<div class='success'>";
echo "<h4>✅ Download System Enhancements:</h4>";
echo "<ul>";
echo "<li><strong>Proper MIME Types:</strong> Serves files with correct video/* content types</li>";
echo "<li><strong>Range Request Support:</strong> Enables video seeking and streaming</li>";
echo "<li><strong>Video File Detection:</strong> Validates actual video content vs text files</li>";
echo "<li><strong>Priority System:</strong> Checks consolidated videos first, then individual recordings</li>";
echo "<li><strong>Clean Filenames:</strong> Generates descriptive download filenames</li>";
echo "</ul>";
echo "</div>";

echo "<div class='info'>";
echo "<h4>📋 Files Updated:</h4>";
echo "<ul>";
echo "<li><strong>upload-recording-enhanced.php:</strong> New enhanced upload handler</li>";
echo "<li><strong>simple-upload-working.php:</strong> Enhanced with better file handling</li>";
echo "<li><strong>download-recording.php:</strong> Fixed with proper video serving</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

?>

<div class="section">
    <h2>6. 🧪 Next Steps</h2>
    <div class="info">
        <h4>To fully test the system:</h4>
        <ol>
            <li><strong>Record a test video</strong> using the tool above</li>
            <li><strong>Upload it</strong> and verify file storage</li>
            <li><strong>Complete a full interview</strong> in the main system</li>
            <li><strong>Test download</strong> from the admin Interview Results page</li>
            <li><strong>Verify video playback</strong> in media players</li>
        </ol>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <a href="../interview.php" class="btn">🎤 Test Full Interview</a>
        <a href="interview-results.php" class="btn">📊 View Interview Results</a>
        <a href="../quick-recording-test.php" class="btn">🎥 Quick Recording Test</a>
    </div>
</div>

</body>
</html>