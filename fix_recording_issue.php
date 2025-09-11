<?php
require_once('include/initialize.php');

// Test database connection and check if tables exist
echo "<h2>Database Connection Test</h2>\n";

// Check if the interview tables exist
$tables_query = "SHOW TABLES LIKE '%interview%'";
$mydb->setQuery($tables_query);
$tables = $mydb->loadResultList();

if (empty($tables)) {
    echo "<p>No interview tables found in database.</p>\n";
    echo "<p>Creating tables...</p>\n";
    
    // Try to create the tables
    $sql1 = "CREATE TABLE IF NOT EXISTS `tblinterviewrecordings` (
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

    $sql2 = "CREATE TABLE IF NOT EXISTS `tblinterviewvideos` (
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
        $mydb->setQuery($sql1);
        $mydb->executeQuery();
        echo "<p>Created tblinterviewrecordings table.</p>\n";
        
        $mydb->setQuery($sql2);
        $mydb->executeQuery();
        echo "<p>Created tblinterviewvideos table.</p>\n";
    } catch (Exception $e) {
        echo "<p>Error creating tables: " . $e->getMessage() . "</p>\n";
    }
} else {
    echo "<p>Found interview tables:</p>\n";
    echo "<ul>\n";
    foreach ($tables as $table) {
        foreach ($table as $key => $value) {
            echo "<li>$value</li>\n";
        }
    }
    echo "</ul>\n";
}

// Now run the add_registration_1_recording script
echo "<h2>Adding Registration 1 Recording</h2>\n";
include('add_registration_1_recording.php');

echo "<p><a href='admin/view-recording.php?id=1'>Try viewing the recording</a></p>\n";
?>