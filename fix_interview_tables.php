<?php
require_once('include/initialize.php');

echo "<h2>Database Table Structure Analysis</h2>\n";

// Check the structure of tblinterviewrecordings
echo "<h3>tblinterviewrecordings Structure</h3>\n";
$structure_query = "DESCRIBE tblinterviewrecordings";
$mydb->setQuery($structure_query);
$structure = $mydb->loadResultList();

if (empty($structure)) {
    echo "<p>Table tblinterviewrecordings does not exist.</p>\n";
    
    // Try to create it
    echo "<p>Attempting to create tblinterviewrecordings...</p>\n";
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
        echo "<p>Failed to create tblinterviewrecordings table: " . $e->getMessage() . "</p>\n";
    }
} else {
    echo "<table border='1'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
    foreach ($structure as $row) {
        echo "<tr>";
        echo "<td>" . $row->Field . "</td>";
        echo "<td>" . $row->Type . "</td>";
        echo "<td>" . $row->Null . "</td>";
        echo "<td>" . $row->Key . "</td>";
        echo "<td>" . $row->Default . "</td>";
        echo "<td>" . $row->Extra . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

// Check the structure of tblinterviewvideos
echo "<h3>tblinterviewvideos Structure</h3>\n";
$structure_query = "DESCRIBE tblinterviewvideos";
$mydb->setQuery($structure_query);
$structure = $mydb->loadResultList();

if (empty($structure)) {
    echo "<p>Table tblinterviewvideos does not exist.</p>\n";
    
    // Try to create it
    echo "<p>Attempting to create tblinterviewvideos...</p>\n";
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
        echo "<p>Failed to create tblinterviewvideos table: " . $e->getMessage() . "</p>\n";
    }
} else {
    echo "<table border='1'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
    foreach ($structure as $row) {
        echo "<tr>";
        echo "<td>" . $row->Field . "</td>";
        echo "<td>" . $row->Type . "</td>";
        echo "<td>" . $row->Null . "</td>";
        echo "<td>" . $row->Key . "</td>";
        echo "<td>" . $row->Default . "</td>";
        echo "<td>" . $row->Extra . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

// Now add the recording for registration ID 1
echo "<h3>Adding Registration ID 1 Recording</h3>\n";

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
            echo "<p>Successfully added recording for registration ID 1.</p>\n";
            echo "<p>File: $file_path</p>\n";
            echo "<p>Size: " . round($file_size/1024, 2) . " KB</p>\n";
            echo "<p>Estimated duration: " . $estimated_duration . "s</p>\n";
        } catch (Exception $e) {
            echo "<p>Failed to add recording: " . $e->getMessage() . "</p>\n";
        }
    } else {
        echo "<p>File does not exist: $full_path</p>\n";
    }
}

echo "<p><a href='admin/view-recording.php?id=1'>Try viewing the recording</a></p>\n";
?>