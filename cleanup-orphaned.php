<?php
require_once('include/initialize.php');

// Find orphaned recordings
$sql = "SELECT DISTINCT ir.REGISTRATIONID 
        FROM tblinterviewrecordings ir 
        WHERE ir.REGISTRATIONID NOT IN (SELECT REGISTRATIONID FROM tbljobregistration)";
$mydb->setQuery($sql);
$orphaned_ids = $mydb->loadResultList();

if (!empty($orphaned_ids)) {
    echo "Found " . count($orphaned_ids) . " orphaned registration IDs:\n";
    foreach ($orphaned_ids as $row) {
        echo "- Registration ID: " . $row->REGISTRATIONID . "\n";
        
        // Get all recordings for this orphaned ID
        $sql = "SELECT RECORDING_ID, FILE_PATH FROM tblinterviewrecordings WHERE REGISTRATIONID = " . $row->REGISTRATIONID;
        $mydb->setQuery($sql);
        $recordings = $mydb->loadResultList();
        
        echo "  Recordings to delete:\n";
        foreach ($recordings as $recording) {
            echo "    - Recording ID: " . $recording->RECORDING_ID . " (File: " . $recording->FILE_PATH . ")\n";
            
            // Delete the file if it exists
            if (file_exists($recording->FILE_PATH)) {
                unlink($recording->FILE_PATH);
                echo "      File deleted\n";
            }
        }
        
        // Delete recordings from database
        $sql = "DELETE FROM tblinterviewrecordings WHERE REGISTRATIONID = " . $row->REGISTRATIONID;
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "  Recordings deleted from database\n";
        } else {
            echo "  Error deleting recordings from database\n";
        }
    }
} else {
    echo "No orphaned registration IDs found.\n";
}
?>