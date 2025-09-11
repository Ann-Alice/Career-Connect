<?php
require_once('include/initialize.php');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Interview Issue Diagnosis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .error { color: red; }
        .success { color: green; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Interview Issue Diagnosis</h1>";

// Check registration ID 3
echo "<div class='section'>
    <h2>Registration ID 3 Status</h2>";

$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE REGISTRATIONID = 3";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result) {
    echo "<p class='success'>Found registration data</p>";
    echo "<h3>Job Registration Data:</h3>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    // Check interview results
    if ($result->INTERVIEW_RESULTS) {
        $interview_data = json_decode($result->INTERVIEW_RESULTS, true);
        echo "<h3>Interview Results Data:</h3>";
        echo "<pre>" . print_r($interview_data, true) . "</pre>";
        
        // Check interview duration
        if (isset($interview_data['interview_duration'])) {
            $duration_seconds = $interview_data['interview_duration'];
            $duration_minutes = $duration_seconds / 60;
            echo "<h3>Interview Duration:</h3>";
            echo "<p><strong>" . $duration_seconds . " seconds</strong> (" . round($duration_minutes, 2) . " minutes)</p>";
        } else {
            echo "<p class='warning'>No interview duration found in results</p>";
        }
    } else {
        echo "<p class='warning'>No interview results data found</p>";
    }
} else {
    echo "<p class='error'>No data found for registration ID 3</p>";
}

echo "</div>";

// Check recordings for registration ID 3
echo "<div class='section'>
    <h2>Interview Recordings for Registration ID 3</h2>";

$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 3 ORDER BY RECORDED_AT DESC";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

if ($recordings) {
    echo "<p class='success'>Found " . count($recordings) . " recording(s)</p>";
    echo "<pre>" . print_r($recordings, true) . "</pre>";
    
    // Check file paths
    foreach ($recordings as $recording) {
        $file_path = $recording->FILE_PATH;
        if (file_exists($file_path)) {
            $file_size = filesize($file_path);
            echo "<p class='success'>File exists: " . $file_path . " (" . $file_size . " bytes)</p>";
        } else {
            echo "<p class='error'>File does not exist: " . $file_path . "</p>";
        }
    }
} else {
    echo "<p class='error'>No recordings found for registration ID 3</p>";
}

echo "</div>";

// Check consolidated videos for registration ID 3
echo "<div class='section'>
    <h2>Consolidated Videos for Registration ID 3</h2>";

$sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = 3 ORDER BY CREATED_AT DESC";
$mydb->setQuery($sql);
$videos = $mydb->loadResultList();

if ($videos) {
    echo "<p class='success'>Found " . count($videos) . " consolidated video(s)</p>";
    echo "<pre>" . print_r($videos, true) . "</pre>";
} else {
    echo "<p class='warning'>No consolidated videos found for registration ID 3</p>";
}

echo "</div>";

// Check upload directory
echo "<div class='section'>
    <h2>Upload Directory Check</h2>";

$upload_dir = 'uploads/interviews/3';
if (is_dir($upload_dir)) {
    echo "<p class='success'>Upload directory exists: " . $upload_dir . "</p>";
    $files = scandir($upload_dir);
    echo "<p>Files in directory:</p>";
    echo "<pre>" . print_r($files, true) . "</pre>";
} else {
    echo "<p class='warning'>Upload directory does not exist: " . $upload_dir . "</p>";
    
    // Check base upload directory
    $base_upload_dir = 'uploads/interviews';
    if (is_dir($base_upload_dir)) {
        echo "<p class='success'>Base upload directory exists: " . $base_upload_dir . "</p>";
        $dirs = scandir($base_upload_dir);
        echo "<p>Subdirectories in base directory:</p>";
        echo "<pre>" . print_r($dirs, true) . "</pre>";
    } else {
        echo "<p class='error'>Base upload directory does not exist: " . $base_upload_dir . "</p>";
    }
}

echo "</div>";

echo "</body>
</html>";
?>