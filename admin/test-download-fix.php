<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

echo "<h1>Video Download Fix Test</h1>";

echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
.test-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff; }
.success { border-left-color: #28a745; background: #d4edda; }
.warning { border-left-color: #ffc107; background: #fff3cd; }
.error { border-left-color: #dc3545; background: #f8d7da; }
.btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
.btn:hover { background: #0056b3; }
</style>";

// Get some test registration IDs
$sql = "SELECT r.REGISTRATIONID, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        LIMIT 5";

$mydb->setQuery($sql);
$test_registrations = $mydb->loadResultList();

echo "<div class='test-box'>";
echo "<h2>🔧 Download Fix Implementation</h2>";
echo "<p>The video download issue has been fixed with the following improvements:</p>";
echo "<ul>";
echo "<li><strong>File Type Validation:</strong> Now checks if files are actual video files vs. text demo files</li>";
echo "<li><strong>Demo File Handling:</strong> Properly serves demo transcript files as .txt downloads</li>";
echo "<li><strong>Clear Error Messages:</strong> Shows informative HTML pages instead of browser errors</li>";
echo "<li><strong>Improved User Experience:</strong> Opens downloads in new tabs with better error handling</li>";
echo "</ul>";
echo "</div>";

if ($test_registrations) {
    echo "<div class='test-box warning'>";
    echo "<h2>🧪 Test the Fix</h2>";
    echo "<p>Click the buttons below to test the updated download system:</p>";
    
    foreach ($test_registrations as $reg) {
        echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 5px;'>";
        echo "<strong>Registration #{$reg->REGISTRATIONID}:</strong> {$reg->FNAME} {$reg->LNAME} - {$reg->OCCUPATIONTITLE}<br>";
        echo "<a href='download-recording.php?id={$reg->REGISTRATIONID}' target='_blank' class='btn' style='margin-top: 5px;'>Test Download</a>";
        echo "<a href='view-recording.php?id={$reg->REGISTRATIONID}' target='_blank' class='btn'>View Recording</a>";
        echo "</div>";
    }
    echo "</div>";
}

// Check for existing demo files
echo "<div class='test-box'>";
echo "<h2>📁 Current Demo Files Status</h2>";

$demo_files_found = [];
$recording_dirs = ['../interview-recordings/', '../recordings/', '../uploads/recordings/', '../interview-system/recordings/'];

foreach ($recording_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . 'interview_*.mp4');
        foreach ($files as $file) {
            $size = filesize($file);
            $is_demo = $size < 10000; // Files smaller than 10KB are likely demo text files
            
            $demo_files_found[] = [
                'path' => $file,
                'size' => $size,
                'is_demo' => $is_demo,
                'readable' => is_readable($file)
            ];
        }
    }
}

if (empty($demo_files_found)) {
    echo "<p style='color: #28a745;'>✅ No demo files found. The system will show proper 'not available' messages.</p>";
} else {
    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>File Path</th><th>Size</th><th>Type</th><th>Status</th></tr>";
    foreach ($demo_files_found as $file) {
        $type_class = $file['is_demo'] ? 'warning' : 'success';
        $type_text = $file['is_demo'] ? 'Demo Text File' : 'Potential Video File';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($file['path']) . "</td>";
        echo "<td>" . number_format($file['size']) . " bytes</td>";
        echo "<td style='color: " . ($file['is_demo'] ? '#ffc107' : '#28a745') . ";'>{$type_text}</td>";
        echo "<td>" . ($file['readable'] ? '✅ Readable' : '❌ Not readable') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<strong>⚠️ Demo Files Detected:</strong> The system now properly handles these as demo content and will offer them as text downloads instead of causing browser errors.";
    echo "</div>";
}

echo "</div>";

echo "<div class='test-box success'>";
echo "<h2>✅ What's Fixed</h2>";
echo "<p><strong>Before:</strong> Clicking 'Download Recording' would cause browser/download manager to show 'File wasn't available on site' error because demo text files with .mp4 extensions aren't valid video files.</p>";
echo "<p><strong>After:</strong> The system now:</p>";
echo "<ul>";
echo "<li>Validates file types properly</li>";
echo "<li>Shows clear HTML error pages for missing recordings</li>";
echo "<li>Offers demo transcript files as proper .txt downloads</li>";
echo "<li>Provides helpful navigation back to interview results</li>";
echo "</ul>";
echo "</div>";

echo "<div class='test-box'>";
echo "<h2>🚀 Next Steps</h2>";
echo "<p>To add real video recordings to the system:</p>";
echo "<ol>";
echo "<li>Implement actual video recording in the interview system</li>";
echo "<li>Save real .mp4 or .webm files to the recording directories</li>";
echo "<li>Update the interview completion process to generate actual video files</li>";
echo "</ol>";
echo "</div>";

echo "<p style='text-align: center; margin-top: 30px;'>";
echo "<a href='interview-results.php' class='btn'>← Back to Interview Results</a>";
echo "<a href='../admin/index.php' class='btn'>Admin Dashboard</a>";
echo "</p>";
?>