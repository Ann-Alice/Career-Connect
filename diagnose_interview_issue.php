<?php
require_once('include/initialize.php');

echo "<h2>Checking Interview Database Tables</h2>\n";

// Check if interview tables exist
$tables_query = "SHOW TABLES LIKE '%interview%'";
$mydb->setQuery($tables_query);
$tables = $mydb->loadResultList();

if (empty($tables)) {
    echo "<p><strong>No interview tables found!</strong></p>\n";
} else {
    echo "<p><strong>Found interview tables:</strong></p>\n";
    echo "<ul>\n";
    foreach ($tables as $table) {
        foreach ($table as $key => $value) {
            echo "<li>$value</li>\n";
        }
    }
    echo "</ul>\n";
}

// Check specifically for the tables we need
$required_tables = ['tblinterviewrecordings', 'tblinterviewvideos'];
foreach ($required_tables as $table) {
    $check_query = "SHOW TABLES LIKE '$table'";
    $mydb->setQuery($check_query);
    $result = $mydb->loadResultList();
    
    if (empty($result)) {
        echo "<p><strong>Missing table: $table</strong></p>\n";
    } else {
        echo "<p>Table exists: $table</p>\n";
    }
}

// Check for registration ID 1 recordings
echo "<h3>Checking for Registration ID 1 Recordings</h3>\n";

$recordings_query = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 1";
$mydb->setQuery($recordings_query);
$recordings = $mydb->loadResultList();

if (empty($recordings)) {
    echo "<p>No recordings found for registration ID 1.</p>\n";
} else {
    echo "<p>Found " . count($recordings) . " recordings for registration ID 1:</p>\n";
    echo "<pre>\n";
    foreach ($recordings as $recording) {
        print_r($recording);
    }
    echo "</pre>\n";
}

// Check for registration ID 1 videos
$videos_query = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = 1";
$mydb->setQuery($videos_query);
$videos = $mydb->loadResultList();

if (empty($videos)) {
    echo "<p>No videos found for registration ID 1.</p>\n";
} else {
    echo "<p>Found " . count($videos) . " videos for registration ID 1:</p>\n";
    echo "<pre>\n";
    foreach ($videos as $video) {
        print_r($video);
    }
    echo "</pre>\n";
}

// Check if the actual file exists
$file_path = 'uploads/interviews/1/conversation_turn_1_essential_answer.webm';
$full_path = $_SERVER['DOCUMENT_ROOT'] . web_root . $file_path;

echo "<h3>Checking File: $file_path</h3>\n";
if (file_exists($full_path)) {
    $size = filesize($full_path);
    echo "<p><strong>File exists!</strong> Size: " . round($size/1024, 2) . " KB</p>\n";
} else {
    echo "<p><strong>File does not exist!</strong></p>\n";
    echo "<p>Full path: $full_path</p>\n";
}

echo "<p><a href='admin/view-recording.php?id=1'>Try viewing the recording</a></p>\n";
?>