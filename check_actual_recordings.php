<?php
require_once('include/initialize.php');

echo "Checking all actual recordings in the database:\n\n";

// Check individual recordings
echo "1. Checking all individual recordings in tblinterviewrecordings:\n";
$sql = "SELECT * FROM tblinterviewrecordings";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

if ($recordings) {
    foreach ($recordings as $recording) {
        echo "   RECORDING_ID: " . $recording->RECORDING_ID . "\n";
        echo "   REGISTRATIONID: " . $recording->REGISTRATIONID . "\n";
        echo "   FILE_PATH: " . $recording->FILE_PATH . "\n";
        echo "   DURATION: " . $recording->DURATION . "\n";
        echo "   RECORDED_AT: " . $recording->RECORDED_AT . "\n";
        echo "   File exists: " . (file_exists($recording->FILE_PATH) ? "YES" : "NO") . "\n";
        if (file_exists($recording->FILE_PATH)) {
            echo "   File size: " . filesize($recording->FILE_PATH) . " bytes\n";
        }
        echo "\n";
    }
} else {
    echo "   No individual recordings found\n\n";
}

// Check consolidated videos
echo "2. Checking all consolidated videos in tblinterviewvideos:\n";
$sql = "SELECT * FROM tblinterviewvideos";
$mydb->setQuery($sql);
$videos = $mydb->loadResultList();

if ($videos) {
    foreach ($videos as $video) {
        echo "   VIDEO_ID: " . $video->VIDEO_ID . "\n";
        echo "   REGISTRATIONID: " . $video->REGISTRATIONID . "\n";
        echo "   VIDEO_PATH: " . $video->VIDEO_PATH . "\n";
        echo "   DURATION: " . $video->DURATION . "\n";
        echo "   CREATED_AT: " . $video->CREATED_AT . "\n";
        
        // Check different possible paths
        $paths_to_check = [
            "uploads/interviews/consolidated/" . $video->VIDEO_PATH,
            "uploads/interviews/" . $video->VIDEO_PATH,
            "../uploads/interviews/consolidated/" . $video->VIDEO_PATH,
            "../uploads/interviews/" . $video->VIDEO_PATH
        ];
        
        $found_path = null;
        foreach ($paths_to_check as $path) {
            if (file_exists($path)) {
                $found_path = $path;
                break;
            }
        }
        
        if ($found_path) {
            echo "   File found at: " . $found_path . "\n";
            echo "   File size: " . filesize($found_path) . " bytes\n";
        } else {
            echo "   File not found at any expected location\n";
            foreach ($paths_to_check as $path) {
                echo "      Checked: " . $path . " - " . (file_exists($path) ? "EXISTS" : "NOT FOUND") . "\n";
            }
        }
        echo "\n";
    }
} else {
    echo "   No consolidated videos found\n\n";
}

// Check all registrations
echo "3. Checking all registrations:\n";
$sql = "SELECT * FROM tbljobregistration ORDER BY REGISTRATIONID";
$mydb->setQuery($sql);
$registrations = $mydb->loadResultList();

if ($registrations) {
    foreach ($registrations as $reg) {
        echo "   REGISTRATIONID: " . $reg->REGISTRATIONID . "\n";
        echo "   APPLICANTID: " . $reg->APPLICANTID . "\n";
        echo "   JOBID: " . $reg->JOBID . "\n";
        echo "   STATUS: " . $reg->STATUS . "\n";
        echo "   REMARKS: " . $reg->REMARKS . "\n";
        echo "\n";
    }
} else {
    echo "   No registrations found\n";
}
?>