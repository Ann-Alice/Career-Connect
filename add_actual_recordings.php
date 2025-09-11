<?php
require_once('include/initialize.php');

echo "Adding actual recordings to the database...\n\n";

// Get all directories in uploads/interviews
$interviews_dir = "uploads/interviews";
$directories = array_filter(glob($interviews_dir . '/*'), 'is_dir');

foreach ($directories as $dir) {
    $registration_id = basename($dir);
    
    // Skip the consolidated directory
    if ($registration_id === 'consolidated') {
        continue;
    }
    
    echo "Processing registration ID: " . $registration_id . "\n";
    
    // Get all files in this directory
    $files = glob($dir . '/*');
    
    foreach ($files as $file) {
        $filename = basename($file);
        
        // Extract question information from filename
        $question_number = 1;
        $conversation_turn = 1;
        $question_type = 'initial_answer';
        
        // Try to extract information from filename
        if (preg_match('/question_(\d+)_/', $filename, $matches)) {
            $question_number = $matches[1];
        }
        
        if (preg_match('/conversation_turn_(\d+)_/', $filename, $matches)) {
            $conversation_turn = $matches[1];
        }
        
        if (preg_match('/_(initial_answer|followup_answer|essential_answer)\./', $filename, $matches)) {
            $question_type = $matches[1];
        }
        
        // Check if this recording already exists in the database
        $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings 
                WHERE REGISTRATIONID = '$registration_id' 
                AND FILE_PATH = '$file'";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        
        if ($result && $result->count == 0) {
            // Add the recording to the database
            // Estimate duration based on file size (rough estimate)
            $file_size = filesize($file);
            $estimated_duration = round($file_size / 100000, 3); // Rough estimate
            
            $sql = "INSERT INTO tblinterviewrecordings 
                    (REGISTRATIONID, QUESTION_NUMBER, FILE_PATH, DURATION, CONVERSATION_TURN, QUESTION_TYPE) 
                    VALUES 
                    ('$registration_id', $question_number, '$file', $estimated_duration, $conversation_turn, '$question_type')";
            
            $mydb->setQuery($sql);
            if ($mydb->executeQuery()) {
                echo "   Added recording: " . $filename . " (Duration: " . $estimated_duration . "s)\n";
            } else {
                echo "   Failed to add recording: " . $filename . "\n";
            }
        } else {
            echo "   Recording already exists in database: " . $filename . "\n";
        }
    }
    
    echo "\n";
}

// Now check for consolidated videos
echo "Checking for consolidated videos...\n";
$consolidated_dir = "uploads/interviews/consolidated";
if (is_dir($consolidated_dir)) {
    $files = glob($consolidated_dir . '/*');
    
    foreach ($files as $file) {
        $filename = basename($file);
        
        // Extract registration ID from filename
        $registration_id = null;
        if (preg_match('/interview_complete_(\d+)_/', $filename, $matches)) {
            $registration_id = $matches[1];
        } elseif (preg_match('/interview_(\d+)_/', $filename, $matches)) {
            $registration_id = $matches[1];
        }
        
        if ($registration_id) {
            // Check if this video already exists in the database
            $sql = "SELECT COUNT(*) as count FROM tblinterviewvideos 
                    WHERE REGISTRATIONID = '$registration_id' 
                    AND VIDEO_PATH = 'consolidated/" . $filename . "'";
            $mydb->setQuery($sql);
            $result = $mydb->loadSingleResult();
            
            if ($result && $result->count == 0) {
                // Add the video to the database
                $file_size = filesize($file);
                $estimated_duration = round($file_size / 200000, 3); // Rough estimate for consolidated videos
                
                $sql = "INSERT INTO tblinterviewvideos 
                        (REGISTRATIONID, VIDEO_PATH, DURATION, FILE_SIZE, STATUS) 
                        VALUES 
                        ('$registration_id', 'consolidated/" . $filename . "', $estimated_duration, $file_size, 'completed')";
                
                $mydb->setQuery($sql);
                if ($mydb->executeQuery()) {
                    echo "   Added consolidated video: " . $filename . " (Duration: " . $estimated_duration . "s)\n";
                } else {
                    echo "   Failed to add consolidated video: " . $filename . "\n";
                }
            } else {
                echo "   Consolidated video already exists in database: " . $filename . "\n";
            }
        }
    }
}

echo "\nDone!\n";
?>