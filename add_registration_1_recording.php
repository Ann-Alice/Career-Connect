<?php
require_once('include/initialize.php');

// Check if the recording already exists
$sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings WHERE REGISTRATIONID = 1 AND FILE_PATH LIKE '%conversation_turn_1_essential_answer.webm'";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result && $result->count > 0) {
    echo "Recording already exists in database.\n";
} else {
    // Add the recording to the database
    $file_path = 'uploads/interviews/1/conversation_turn_1_essential_answer.webm';
    $full_path = $_SERVER['DOCUMENT_ROOT'] . web_root . $file_path;
    
    if (file_exists($full_path)) {
        $file_size = filesize($full_path);
        $estimated_duration = round($file_size / 100000, 3); // Rough estimate
        
        $sql = "INSERT INTO tblinterviewrecordings 
                (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, CONVERSATION_TURN, QUESTION_TYPE) 
                VALUES 
                (1, 1, '$file_path', $estimated_duration, 1, 'essential_answer')";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "Successfully added recording for registration ID 1.\n";
            echo "File: $file_path\n";
            echo "Size: " . round($file_size/1024, 2) . " KB\n";
            echo "Estimated duration: " . $estimated_duration . "s\n";
        } else {
            echo "Failed to add recording to database.\n";
        }
    } else {
        echo "File does not exist: $full_path\n";
    }
}

// Also check if we need to add a consolidated video entry
$sql = "SELECT COUNT(*) as count FROM tblinterviewvideos WHERE REGISTRATIONID = 1";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result && $result->count > 0) {
    echo "Consolidated video entry already exists in database.\n";
} else {
    // Check if there's a consolidated video file
    $consolidated_files = glob('uploads/interviews/consolidated/*1*.webm');
    if (!empty($consolidated_files)) {
        $consolidated_file = $consolidated_files[0];
        $filename = basename($consolidated_file);
        $file_size = filesize($consolidated_file);
        $estimated_duration = round($file_size / 200000, 3); // Rough estimate
        
        $sql = "INSERT INTO tblinterviewvideos 
                (REGISTRATIONID, VIDEO_PATH, DURATION, FILE_SIZE, STATUS) 
                VALUES 
                (1, 'consolidated/$filename', $estimated_duration, $file_size, 'completed')";
        
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "Successfully added consolidated video entry for registration ID 1.\n";
        } else {
            echo "Failed to add consolidated video entry to database.\n";
        }
    } else {
        echo "No consolidated video file found for registration ID 1.\n";
    }
}
?>