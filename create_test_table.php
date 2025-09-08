<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Create a test table
echo "Creating test table...\n";
$sql = "CREATE TABLE IF NOT EXISTS tblusers_test LIKE tblusers";
if (mysqli_query($conn, $sql)) {
    echo "Test table created successfully\n";
} else {
    echo "Error creating test table: " . mysqli_error($conn) . "\n";
}

// Copy data to test table
echo "Copying data to test table...\n";
$sql = "INSERT IGNORE INTO tblusers_test SELECT * FROM tblusers";
if (mysqli_query($conn, $sql)) {
    echo "Data copied successfully\n";
} else {
    echo "Error copying data: " . mysqli_error($conn) . "\n";
}

// Try to update the test table
echo "Testing update on test table...\n";
$sql = "UPDATE tblusers_test SET FULLNAME='Test User' WHERE USERID='2018001'";
if (mysqli_query($conn, $sql)) {
    echo "Test table update successful\n";
} else {
    echo "Error updating test table: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
echo "Test complete!\n";
?>