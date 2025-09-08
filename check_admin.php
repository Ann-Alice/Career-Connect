<?php
require_once("include/initialize.php");

echo "<h2>Admin Table Diagnostic</h2>";

// Check admin table structure
$sql = "DESCRIBE tbladmin";
$mydb->setQuery($sql);
$structure = $mydb->loadResultList();
echo "<h3>Admin Table Structure:</h3>";
echo "<pre>";
foreach ($structure as $column) {
    echo "{$column->Field}: {$column->Type}\n";
}
echo "</pre>";

// Check for admin records
$sql = "SELECT * FROM tbladmin";
$mydb->setQuery($sql);
$admins = $mydb->loadResultList();
echo "<h3>Admin Records:</h3>";
if ($admins) {
    echo "<pre>";
    foreach ($admins as $admin) {
        echo "ID: {$admin->ADMINID}\n";
        echo "Name: {$admin->FULLNAME}\n";
        echo "Username: {$admin->USERNAME}\n";
        echo "----------------------------------------\n";
    }
    echo "</pre>";
} else {
    echo "No admin records found.<br>";
    
    // Create default admin if none exists
    echo "<h3>Creating Default Admin:</h3>";
    $sql = "INSERT INTO tbladmin (FULLNAME, USERNAME, PASS) 
            VALUES ('System Administrator', 'admin', 'admin123')";
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        echo "✅ Default admin created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "❌ Failed to create default admin.<br>";
    }
}
?> 