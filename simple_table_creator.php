<?php
echo "Creating tblusers table...\n";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "Connected to database\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS tblusers (USERID int(11) NOT NULL AUTO_INCREMENT, UNAME varchar(90) NOT NULL, PASS varchar(90) NOT NULL, TYPE varchar(30) NOT NULL, PRIMARY KEY (USERID))";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table tblusers created successfully\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
    
    mysqli_close($conn);
} else {
    echo "Connection failed: " . mysqli_connect_error() . "\n";
}
?> 