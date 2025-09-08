<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Get structure of original table
echo "Original table structure:\n";
$result = mysqli_query($conn, "SHOW CREATE TABLE tblusers");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo $row['Create Table'] . "\n";
}

// Get structure of test table
echo "\nTest table structure:\n";
$result = mysqli_query($conn, "SHOW CREATE TABLE tblusers_test");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo $row['Create Table'] . "\n";
}

// Check for any differences in the data
echo "\nChecking for data differences...\n";
$result1 = mysqli_query($conn, "SELECT * FROM tblusers WHERE USERID='2018001'");
$result2 = mysqli_query($conn, "SELECT * FROM tblusers_test WHERE USERID='2018001'");

if ($result1 && $result2) {
    $row1 = mysqli_fetch_assoc($result1);
    $row2 = mysqli_fetch_assoc($result2);
    
    echo "Original data:\n";
    print_r($row1);
    
    echo "\nTest data:\n";
    print_r($row2);
    
    // Compare the data
    foreach ($row1 as $key => $value) {
        if (!isset($row2[$key]) || $row2[$key] !== $value) {
            echo "Difference in field '$key': '" . ($value ?? 'NULL') . "' vs '" . ($row2[$key] ?? 'NULL') . "'\n";
        }
    }
}

mysqli_close($conn);
echo "Comparison complete!\n";
?>