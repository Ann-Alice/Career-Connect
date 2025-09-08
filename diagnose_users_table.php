<?php
require_once("include/initialize.php");

// Connect to database
global $mydb;

echo "Diagnosing tblusers table structure...\n";

try {
    // Get the actual table structure
    $sql = "DESCRIBE tblusers";
    $mydb->setQuery($sql);
    $result = $mydb->loadResultList();
    
    echo "Current tblusers table structure:\n";
    foreach($result as $row) {
        echo "- " . $row->Field . " " . $row->Type . " " . 
             ($row->Null == "NO" ? "NOT NULL" : "NULL") . 
             ($row->Default ? " DEFAULT '" . $row->Default . "'" : "") . 
             ($row->Key ? " KEY:" . $row->Key : "") . "\n";
    }
    
    // Check if there are any records with potential decimal issues
    echo "\nChecking for records with potential decimal issues...\n";
    $sql = "SELECT * FROM tblusers LIMIT 5";
    $mydb->setQuery($sql);
    $records = $mydb->loadResultList();
    
    foreach($records as $record) {
        echo "USERID: " . $record->USERID . "\n";
        foreach($record as $field => $value) {
            echo "  $field: '$value'\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Diagnosis complete!\n";
?>