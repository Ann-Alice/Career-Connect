<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Check for any DECIMAL or numeric columns
echo "Checking for DECIMAL/numeric columns in tblusers...\n";
$result = mysqli_query($conn, "DESCRIBE tblusers");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        if (stripos($row['Type'], 'decimal') !== false || 
            stripos($row['Type'], 'int') !== false || 
            stripos($row['Type'], 'float') !== false || 
            stripos($row['Type'], 'double') !== false) {
            echo "Numeric column: " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    }
} else {
    echo "Error: " . mysqli_error($conn) . "\n";
}

// Let's try a different approach - check if we can update just one field
echo "\nTrying to update only FULLNAME field...\n";
$sql = "UPDATE tblusers SET FULLNAME='Test User' WHERE USERID='2018001'";
echo "Executing: " . $sql . "\n";
if (mysqli_query($conn, $sql)) {
    echo "SUCCESS\n";
} else {
    echo "ERROR: " . mysqli_error($conn) . "\n";
}

// Try updating with a simple query that includes all the fields we know about
echo "\nTrying to update with explicit fields...\n";
$sql = "UPDATE tblusers SET FULLNAME='Chambe Narciso', USERNAME='Narciso', PASS='f3593fd40c55c33d1788309d4137e82f5eab0dea', ROLE='Employee', PICLOCATION='', IS_ACTIVE=1 WHERE USERID='2018001'";
echo "Executing: " . $sql . "\n";
if (mysqli_query($conn, $sql)) {
    echo "SUCCESS\n";
} else {
    echo "ERROR: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
echo "Check complete!\n";
?>