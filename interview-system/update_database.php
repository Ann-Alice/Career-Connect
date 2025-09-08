<?php
require_once '../include/initialize.php';

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/sql/update_schema.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement separately
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $mydb->setQuery($statement);
            $mydb->executeQuery();
        }
    }
    
    // Create necessary directories
    $directories = [
        '../uploads/interviews',
        '../uploads/interviews/temp'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    
    echo "Database and directory structure updated successfully!";
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage();
} 