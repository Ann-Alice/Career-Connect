<?php
require_once('include/initialize.php');

// Check for registration ID 1 in tblinterviewrecordings
$recordings_query = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 1";
$mydb->setQuery($recordings_query);
$recordings = $mydb->loadResultList();

if (empty($recordings)) {
    echo "No entries found in tblinterviewrecordings for registration ID 1.\n";
    echo "Running the add_actual_recordings.php script...\n";
    
    // Include the script to add recordings
    include('add_actual_recordings.php');
    
    // Check again
    $mydb->setQuery($recordings_query);
    $recordings = $mydb->loadResultList();
    
    if (empty($recordings)) {
        echo "Still no entries found after running the script.\n";
    } else {
        echo "Successfully added " . count($recordings) . " recordings for registration ID 1.\n";
    }
} else {
    echo "Found " . count($recordings) . " entries in tblinterviewrecordings for registration ID 1:\n";
    foreach ($recordings as $recording) {
        echo "- File: " . $recording->FILE_PATH . " (Duration: " . $recording->DURATION . "s)\n";
    }
}

// Check for registration ID 1 in tblinterviewvideos
$videos_query = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = 1";
$mydb->setQuery($videos_query);
$videos = $mydb->loadResultList();

if (empty($videos)) {
    echo "No entries found in tblinterviewvideos for registration ID 1.\n";
} else {
    echo "Found " . count($videos) . " entries in tblinterviewvideos for registration ID 1:\n";
    foreach ($videos as $video) {
        echo "- File: " . $video->VIDEO_PATH . " (Duration: " . $video->DURATION . "s)\n";
    }
}
?>