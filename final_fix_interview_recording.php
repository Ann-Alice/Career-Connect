<?php
require_once('include/initialize.php');

echo "<h2>Fixing Interview Recording Issue for Registration ID 1</h2>\n";

// First, ensure the tables exist
echo "<h3>Checking and Creating Tables if Needed</h3>\n";

// Check and create tblinterviewrecordings
$check_query = "SHOW TABLES LIKE 'tblinterviewrecordings'";
$mydb->setQuery($check_query);
$result = $mydb->loadResultList();

if (empty($result)) {
    echo "<p>Creating tblinterviewrecordings table...</p>\n";
    $create_query = "CREATE TABLE IF NOT EXISTS `tblinterviewrecordings` (
        `RECORDINGID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` int(11) NOT NULL,
        `QUESTION_NUMBER` int(11) DEFAULT NULL,
        `FILE_PATH` varchar(500) NOT NULL,
        `DURATION` decimal(10,3) DEFAULT NULL,
        `CONVERSATION_TURN` int(11) DEFAULT NULL,
        `QUESTION_TYPE` varchar(50) DEFAULT NULL,
        `RECORDED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`RECORDINGID`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    try {
        $mydb->setQuery($create_query);
        $mydb->executeQuery();
        echo "<p>Successfully created tblinterviewrecordings table.</p>\n";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Failed to create tblinterviewrecordings table: " . $e->getMessage() . "</p>\n";
    }
} else {
    echo "<p>tblinterviewrecordings table already exists.</p>\n";
}

// Check and create tblinterviewvideos
$check_query = "SHOW TABLES LIKE 'tblinterviewvideos'";
$mydb->setQuery($check_query);
$result = $mydb->loadResultList();

if (empty($result)) {
    echo "<p>Creating tblinterviewvideos table...</p>\n";
    $create_query = "CREATE TABLE IF NOT EXISTS `tblinterviewvideos` (
        `VIDEOID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` int(11) NOT NULL,
        `VIDEO_PATH` varchar(500) NOT NULL,
        `DURATION` decimal(10,3) DEFAULT NULL,
        `FILE_SIZE` int(11) DEFAULT NULL,
        `STATUS` varchar(50) DEFAULT 'pending',
        `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`VIDEOID`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    try {
        $mydb->setQuery($create_query);
        $mydb->executeQuery();
        echo "<p>Successfully created tblinterviewvideos table.</p>\n";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Failed to create tblinterviewvideos table: " . $e->getMessage() . "</p>\n";
    }
} else {
    echo "<p>tblinterviewvideos table already exists.</p>\n";
}

// Now add the recording for registration ID 1
echo "<h3>Adding Recording for Registration ID 1</h3>\n";

// Check if it already exists
$check_query = "SELECT COUNT(*) as count FROM tblinterviewrecordings WHERE REGISTRATIONID = 1 AND FILE_PATH LIKE '%conversation_turn_1_essential_answer.webm'";
$mydb->setQuery($check_query);
$result = $mydb->loadSingleResult();

if ($result && $result->count > 0) {
    echo "<p>Recording already exists in database.</p>\n";
} else {
    // Add the recording
    $file_path = 'uploads/interviews/1/conversation_turn_1_essential_answer.webm';
    $full_path = $_SERVER['DOCUMENT_ROOT'] . web_root . $file_path;
    
    if (file_exists($full_path)) {
        $file_size = filesize($full_path);
        $estimated_duration = round($file_size / 100000, 3); // Rough estimate
        
        $insert_query = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, CONVERSATION_TURN, QUESTION_TYPE) 
                VALUES 
                (1, 1, '$file_path', $estimated_duration, 1, 'essential_answer')";
        
        try {
            $mydb->setQuery($insert_query);
            $mydb->executeQuery();
            echo "<p style='color: green;'>Successfully added recording for registration ID 1.</p>\n";
            echo "<p>File: $file_path</p>\n";
            echo "<p>Size: " . round($file_size/1024, 2) . " KB</p>\n";
            echo "<p>Estimated duration: " . $estimated_duration . "s</p>\n";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Failed to add recording: " . $e->getMessage() . "</p>\n";
        }
    } else {
        echo "<p style='color: red;'>File does not exist: $full_path</p>\n";
    }
}

// Verify the recording was added
echo "<h3>Verifying Recording</h3>\n";
$verify_query = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 1";
$mydb->setQuery($verify_query);
$recordings = $mydb->loadResultList();

if (empty($recordings)) {
    echo "<p style='color: red;'>No recordings found for registration ID 1.</p>\n";
} else {
    echo "<p style='color: green;'>Found " . count($recordings) . " recording(s) for registration ID 1:</p>\n";
    echo "<ul>\n";
    foreach ($recordings as $recording) {
        echo "<li>File: " . htmlspecialchars($recording->FILE_PATH) . " (Duration: " . $recording->DURATION . "s)</li>\n";
    }
    echo "</ul>\n";
}

echo "<h3>Testing Video Streaming</h3>\n";
echo "<p><a href='admin/view-recording.php?id=1' target='_blank'>Click here to view the recording</a></p>\n";
echo "<p>If the recording plays correctly, the issue has been resolved.</p>\n";
?>