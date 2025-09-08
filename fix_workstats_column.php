<?php
require_once("include/initialize.php");

// Connect to database
global $mydb;

echo "Applying fix for WORKSTATS column...\n";

try {
    // Modify the WORKSTATS column to have a default value
    $sql = "ALTER TABLE `tblemployees` MODIFY `WORKSTATS` VARCHAR(90) NOT NULL DEFAULT 'Active'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    
    echo "SUCCESS: WORKSTATS column has been modified to have a default value of 'Active'.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Fix applied successfully!\n";
?>