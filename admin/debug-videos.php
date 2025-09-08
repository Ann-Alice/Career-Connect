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

echo "<h1>Video Debug Information</h1>";

// Check database for video records
echo "<h2>Database Video Records</h2>";
$sql = "SELECT * FROM tblinterviewvideos ORDER BY CREATED_AT DESC LIMIT 10";
$mydb->setQuery($sql);
$videos = $mydb->loadResultList();

if ($videos) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>VIDEOID</th><th>REGISTRATIONID</th><th>VIDEO_PATH</th><th>CREATED_AT</th><th>File Exists</th><th>Full Path</th></tr>";
    
    foreach ($videos as $video) {
        $fullPath = web_root . 'uploads/interviews/' . $video->VIDEO_PATH;
        $fileExists = file_exists($fullPath) ? 'Yes' : 'No';
        
        echo "<tr>";
        echo "<td>{$video->VIDEOID}</td>";
        echo "<td>{$video->REGISTRATIONID}</td>";
        echo "<td>{$video->VIDEO_PATH}</td>";
        echo "<td>{$video->CREATED_AT}</td>";
        echo "<td>{$fileExists}</td>";
        echo "<td>{$fullPath}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No video records found in database.</p>";
}

// Check uploads directory
echo "<h2>Uploads Directory Contents</h2>";
$uploadsDir = web_root . 'uploads/interviews/';
echo "<p>Uploads directory: {$uploadsDir}</p>";

if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filePath = $uploadsDir . $file;
            $fileSize = is_file($filePath) ? filesize($filePath) : 'N/A';
            echo "<li>{$file} (Size: {$fileSize} bytes)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Uploads directory does not exist.</p>";
}

// Check web_root configuration
echo "<h2>Configuration</h2>";
echo "<p>web_root: " . web_root . "</p>";
echo "<p>Current directory: " . getcwd() . "</p>";

// Test file access
echo "<h2>File Access Test</h2>";
$testFile = web_root . 'uploads/interviews/9/conversation_turn_1_initial_answer.webm';
echo "<p>Testing access to: {$testFile}</p>";
echo "<p>File exists: " . (file_exists($testFile) ? 'Yes' : 'No') . "</p>";
echo "<p>File readable: " . (is_readable($testFile) ? 'Yes' : 'No') . "</p>";
if (file_exists($testFile)) {
    echo "<p>File size: " . filesize($testFile) . " bytes</p>";
}
?> 