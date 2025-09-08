<?php
require_once("include/initialize.php");

// Connect to database
global $mydb;

echo "Applying fix for employee table columns...\n";

try {
    // Modify the WORKSTATS column to have a default value
    $sql = "ALTER TABLE `tblemployees` MODIFY `WORKSTATS` VARCHAR(90) NOT NULL DEFAULT 'Active'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    echo "SUCCESS: WORKSTATS column has been modified to have a default value of 'Active'.\n";
    
    // Modify the EMPPHOTO column to have a default value
    $sql = "ALTER TABLE `tblemployees` MODIFY `EMPPHOTO` VARCHAR(255) NOT NULL DEFAULT ''";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    echo "SUCCESS: EMPPHOTO column has been modified to have a default value of empty string.\n";
    
    // Modify the CELLNO column to have a default value
    $sql = "ALTER TABLE `tblemployees` MODIFY `CELLNO` VARCHAR(30) NOT NULL DEFAULT ''";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    echo "SUCCESS: CELLNO column has been modified to have a default value of empty string.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "All fixes applied successfully!\n";
?>