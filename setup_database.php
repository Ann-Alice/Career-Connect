<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting database setup...\n";

// First, try to connect without password
$conn = @mysqli_connect('localhost', 'root', '');
if (!$conn) {
    echo "Initial connection failed. Trying to set root password...\n";
    
    // Try to connect with default XAMPP password
    $conn = @mysqli_connect('localhost', 'root', 'xampp');
    if (!$conn) {
        echo "Connection with default password failed. Creating new connection...\n";
        
        // Create new connection with no password
        $conn = new mysqli('localhost', 'root', '');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Set new password
        $sql = "ALTER USER 'root'@'localhost' IDENTIFIED BY 'xampp'";
        if ($conn->query($sql)) {
            echo "Successfully set root password to 'xampp'\n";
        } else {
            echo "Error setting password: " . $conn->error . "\n";
        }
        
        // Close connection
        $conn->close();
        
        // Try connecting with new password
        $conn = @mysqli_connect('localhost', 'root', 'xampp');
        if (!$conn) {
            die("Failed to connect with new password. Please check MySQL service is running.\n");
        }
    }
}

echo "Connected to MySQL successfully\n";

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS erisdb";
if ($conn->query($sql)) {
    echo "Database 'erisdb' created or already exists\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db('erisdb');

// Read and execute the SQL file
$sqlFile = file_get_contents('erisdb.sql');
if ($sqlFile === false) {
    die("Error reading erisdb.sql file\n");
}

// Split the SQL file into individual queries
$queries = explode(';', $sqlFile);

// Execute each query
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query)) {
            echo "Executed query successfully\n";
        } else {
            echo "Error executing query: " . $conn->error . "\n";
        }
    }
}

echo "Database setup completed!\n";

// Update config.php with the correct password
$configFile = 'include/config.php';
$configContent = file_get_contents($configFile);
if ($configContent === false) {
    die("Error reading config.php file\n");
}

// Update the password in config.php
$configContent = preg_replace(
    "/defined\('pass'\) \? null : define\(\"pass\", \"[^\"]*\"\);/",
    "defined('pass') ? null : define(\"pass\", \"xampp\");",
    $configContent
);

if (file_put_contents($configFile, $configContent)) {
    echo "Updated config.php with new password\n";
} else {
    echo "Error updating config.php\n";
}

echo "Setup completed successfully!\n";
?> 