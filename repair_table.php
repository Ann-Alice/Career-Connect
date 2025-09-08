<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Repair the table
echo "Repairing tblusers table...\n";
$result = mysqli_query($conn, "REPAIR TABLE tblusers");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
} else {
    echo "Error repairing table: " . mysqli_error($conn) . "\n";
}

// Optimize the table
echo "\nOptimizing tblusers table...\n";
$result = mysqli_query($conn, "OPTIMIZE TABLE tblusers");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
} else {
    echo "Error optimizing table: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
echo "Repair complete!\n";
?>