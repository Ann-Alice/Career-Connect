<!DOCTYPE html>
<html>
<head>
    <title>Video Status Checker</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
        .btn:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🎥 Video Status for Registration ID: 3 (James Carta)</h1>
    
    <?php
    require_once('include/initialize.php');
    
    $reg_id = 3;
    
    echo "<h2>Database Records</h2>";
    
    // Check tblinterviewvideos
    echo "<h3>Consolidated Videos (tblinterviewvideos)</h3>";
    $sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = $reg_id";
    $mydb->setQuery($sql);
    $videos = $mydb->loadResultList();
    
    if ($videos) {
        foreach ($videos as $v) {
            echo "<div class='info'>";
            echo "<strong>Video Record Found:</strong><br>";
            echo "ID: {$v->ID}<br>";
            echo "Video Path: {$v->VIDEO_PATH}<br>";
            echo "Created: {$v->CREATED_AT}<br>";
            
            $path1 = "uploads/interviews/consolidated/{$v->VIDEO_PATH}";
            $path2 = "uploads/interviews/{$v->VIDEO_PATH}";
            
            echo "<br><strong>File Locations:</strong><br>";
            if (file_exists($path1)) {
                $size = filesize($path1);
                echo "✅ $path1 - EXISTS ($size bytes)<br>";
            } else {
                echo "❌ $path1 - NOT FOUND<br>";
            }
            
            if (file_exists($path2)) {
                $size = filesize($path2);
                echo "✅ $path2 - EXISTS ($size bytes)<br>";
            } else {
                echo "❌ $path2 - NOT FOUND<br>";
            }
            echo "</div>";
        }
    } else {
        echo "<div class='error'>❌ No videos found in tblinterviewvideos</div>";
    }
    
    // Check tblinterviewrecordings
    echo "<h3>Individual Recordings (tblinterviewrecordings)</h3>";
    $sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = $reg_id";
    $mydb->setQuery($sql);
    $recordings = $mydb->loadResultList();
    
    if ($recordings) {
        foreach ($recordings as $r) {
            echo "<div class='info'>";
            echo "<strong>Recording Record Found:</strong><br>";
            echo "ID: {$r->RECORDINGID}<br>";
            echo "Question: {$r->QUESTION_NUMBER}<br>";
            echo "File Path: {$r->FILE_PATH}<br>";
            echo "Duration: {$r->DURATION}s<br>";
            echo "Recorded: {$r->RECORDED_AT}<br>";
            
            if (file_exists($r->FILE_PATH)) {
                $size = filesize($r->FILE_PATH);
                echo "✅ File EXISTS ($size bytes)<br>";
            } else {
                echo "❌ File NOT FOUND<br>";
            }
            echo "</div>";
        }
    } else {
        echo "<div class='error'>❌ No recordings found in tblinterviewrecordings</div>";
    }
    
    echo "<h2>📥 Download Options</h2>";
    echo "<div class='info'>";
    echo "<p><strong>Current Download URL:</strong></p>";
    echo "<a href='admin/download-recording.php?id=$reg_id' class='btn'>Download Video</a>";
    echo "<p><strong>Upload Tool:</strong></p>";
    echo "<a href='admin/create-real-videos.php' class='btn'>Upload Real Video</a>";
    echo "</div>";
    ?>
    
    <div class="info">
        <h3>🔧 Next Steps:</h3>
        <ol>
            <li>If no video files exist above, go to <strong>Upload Real Video</strong></li>
            <li>Upload any MP4 file from your computer</li>
            <li>Return here and click <strong>Download Video</strong></li>
            <li>You should get an actual MP4 file instead of demo text</li>
        </ol>
    </div>
</body>
</html>