<?php
require_once("include/initialize.php");

// Connect to database
global $mydb;

echo "Debugging database fields for tblusers...\n";

try {
    // Get the actual table fields
    $fields = $mydb->getfieldsononetable('tblusers');
    echo "Database fields:\n";
    print_r($fields);
    
    // Get a user record to examine
    $sql = "SELECT * FROM tblusers WHERE USERID = '2018001'";
    $mydb->setQuery($sql);
    $user_record = $mydb->loadSingleResult();
    
    echo "\nUser record from database:\n";
    print_r($user_record);
    
    // Check what properties the User class thinks it has
    echo "\nChecking User class properties...\n";
    $user = new User();
    $reflection = new ReflectionClass($user);
    $properties = $reflection->getProperties();
    echo "Class properties:\n";
    foreach ($properties as $property) {
        echo "- " . $property->getName() . " (" . $property->getDeclaringClass()->getName() . ")\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Debug complete!\n";
?>