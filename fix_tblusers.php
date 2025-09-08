<?php
echo "Fixing tblusers table...\n";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "Connected to database\n";
    
    // Drop the problematic table
    if (mysqli_query($conn, "DROP TABLE IF EXISTS tblusers")) {
        echo "Dropped existing tblusers table\n";
    }
    
    // Create new table
    $sql = "CREATE TABLE tblusers (
        USERID int(11) NOT NULL AUTO_INCREMENT,
        UNAME varchar(90) NOT NULL,
        PASS varchar(90) NOT NULL,
        TYPE varchar(30) NOT NULL,
        PRIMARY KEY (USERID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table tblusers created successfully\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
    
    // Check tables
    $result = mysqli_query($conn, "SHOW TABLES");
    $count = mysqli_num_rows($result);
    echo "Total tables: $count\n";
    
    mysqli_close($conn);
} else {
    echo "Connection failed: " . mysqli_connect_error() . "\n";
}
?> 