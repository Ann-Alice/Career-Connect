<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Try the exact same update query
$sql = "UPDATE tblusers SET FULLNAME='Chambe Narciso', USERNAME='Narciso', PASS='f3593fd40c55c33d1788309d4137e82f5eab0dea' WHERE USERID=2018001";
echo "Executing SQL: " . $sql . "\n";

if (mysqli_query($conn, $sql)) {
    echo "Query executed successfully\n";
} else {
    echo "Error: " . mysqli_error($conn) . "\n";
}

// Let's also check if there are any triggers on the table
echo "\nChecking for triggers...\n";
$trigger_sql = "SHOW TRIGGERS LIKE 'tblusers'";
$result = mysqli_query($conn, $trigger_sql);
if ($result && mysqli_num_rows($result) > 0) {
    echo "Triggers found:\n";
    while($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
} else {
    echo "No triggers found\n";
}

mysqli_close($conn);
echo "Test complete!\n";
?>