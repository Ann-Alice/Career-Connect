<?php
require_once('include/initialize.php');

echo "<h2>Checking Interview Recording Database Entries</h2>\n";

// Check if the tables exist
echo "<h3>Checking for interview tables...</h3>\n";
$tables_query = "SHOW TABLES LIKE '%interview%'";
$mydb->setQuery($tables_query);
$tables = $mydb->loadResultList();

if (empty($tables)) {
    echo "<p>No interview tables found in database.</p>\n";
} else {
    echo "<ul>\n";
    foreach ($tables as $table) {
        foreach ($table as $key => $value) {
            echo "<li>$value</li>\n";
        }
    }
    echo "</ul>\n";
}

// Check for registration ID 1 in tblinterviewrecordings
echo "<h3>Checking tblinterviewrecordings for registration ID 1...</h3>\n";
$recordings_query = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 1";
$mydb->setQuery($recordings_query);
$recordings = $mydb->loadResultList();

if (empty($recordings)) {
    echo "<p>No entries found in tblinterviewrecordings for registration ID 1.</p>\n";
    echo "<p>Running the add_actual_recordings.php script...</p>\n";
    
    // Include the script to add recordings
    include('add_actual_recordings.php');
    
    // Check again
    $mydb->setQuery($recordings_query);
    $recordings = $mydb->loadResultList();
    
    if (empty($recordings)) {
        echo "<p>Still no entries found after running the script.</p>\n";
    } else {
        echo "<p>Successfully added " . count($recordings) . " recordings for registration ID 1.</p>\n";
    }
} else {
    echo "<p>Found " . count($recordings) . " entries in tblinterviewrecordings for registration ID 1:</p>\n";
    echo "<ul>\n";
    foreach ($recordings as $recording) {
        echo "<li>File: " . htmlspecialchars($recording->FILE_PATH) . " (Duration: " . $recording->DURATION . "s)</li>\n";
    }
    echo "</ul>\n";
}

// Check for registration ID 1 in tblinterviewvideos
echo "<h3>Checking tblinterviewvideos for registration ID 1...</h3>\n";
$videos_query = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = 1";
$mydb->setQuery($videos_query);
$videos = $mydb->loadResultList();

if (empty($videos)) {
    echo "<p>No entries found in tblinterviewvideos for registration ID 1.</p>\n";
} else {
    echo "<p>Found " . count($videos) . " entries in tblinterviewvideos for registration ID 1:</p>\n";
    echo "<ul>\n";
    foreach ($videos as $video) {
        echo "<li>File: " . htmlspecialchars($video->VIDEO_PATH) . " (Duration: " . $video->DURATION . "s)</li>\n";
    }
    echo "</ul>\n";
}

// Check the actual file
$file_path = 'uploads/interviews/1/conversation_turn_1_essential_answer.webm';
echo "<h3>Checking file: $file_path</h3>\n";
$full_path = $_SERVER['DOCUMENT_ROOT'] . web_root . $file_path;

if (file_exists($full_path)) {
    $size = filesize($full_path);
    echo "<p>File exists. Size: " . round($size/1024, 2) . " KB</p>\n";
} else {
    echo "<p>File does not exist.</p>\n";
}

echo "<p><a href='view-recording.php?id=1'>Try viewing the recording</a></p>\n";
?>