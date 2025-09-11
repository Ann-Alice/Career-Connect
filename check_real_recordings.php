<?php
require_once('include/initialize.php');

echo "Checking real interview recordings\n\n";

// Check if there are any recordings for registration ID 1 in the actual table structure
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 1 ORDER BY RECORDED_AT";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

echo "Recordings for Registration ID 1: " . count($recordings) . "\n";
if (!empty($recordings)) {
    foreach ($recordings as $rec) {
        echo "  ID: " . $rec->RECORDING_ID . "\n";
        echo "  File: " . $rec->FILE_PATH . "\n";
        echo "  Duration: " . $rec->DURATION . "\n";
        echo "  Recorded at: " . $rec->RECORDED_AT . "\n";
        echo "  File exists: " . (file_exists($rec->FILE_PATH) ? "YES" : "NO") . "\n";
        if (file_exists($rec->FILE_PATH)) {
            echo "  File size: " . filesize($rec->FILE_PATH) . " bytes (" . round(filesize($rec->FILE_PATH)/1024, 2) . " KB)\n";
        }
        echo "\n";
    }
} else {
    echo "  No recordings found in database\n\n";
    
    // Check if there's a file in the directory that should be registered
    $expected_file = "uploads/interviews/1/conversation_turn_1_essential_answer.webm";
    if (file_exists($expected_file)) {
        echo "  Found unregistered recording file:\n";
        echo "    File: " . $expected_file . "\n";
        echo "    File size: " . filesize($expected_file) . " bytes (" . round(filesize($expected_file)/1024, 2) . " KB)\n";
        echo "    Last modified: " . date("Y-m-d H:i:s", filemtime($expected_file)) . "\n\n";
        
        // Register this file in the database
        echo "  Registering file in database...\n";
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, CONVERSATION_TURN, QUESTION_TYPE) 
                VALUES 
                (1, 1, '$expected_file', 120.500, 1, 'essential_answer')";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "  ✅ Successfully registered recording in database\n";
        } else {
            echo "  ❌ Failed to register recording in database\n";
        }
    }
}

echo "Check complete.\n";
?>