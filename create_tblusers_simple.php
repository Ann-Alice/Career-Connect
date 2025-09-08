<?php
echo "Creating tblusers table...<br>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "Connected to database<br>";
    
    // Drop table if it exists (to avoid conflicts)
    mysqli_query($conn, "DROP TABLE IF EXISTS tblusers");
    echo "Dropped existing table<br>";
    
    // Create the table with simple structure
    $sql = "CREATE TABLE tblusers (
        USERID int(11) NOT NULL AUTO_INCREMENT,
        UNAME varchar(90) NOT NULL,
        PASS varchar(90) NOT NULL,
        TYPE varchar(30) NOT NULL,
        PRIMARY KEY (USERID)
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "SUCCESS: Table tblusers created<br>";
        
        // Insert a default admin user
        $insert_sql = "INSERT INTO tblusers (UNAME, PASS, TYPE) VALUES ('admin', 'admin123', 'Administrator')";
        if (mysqli_query($conn, $insert_sql)) {
            echo "SUCCESS: Default admin user created<br>";
        }
        
        // Verify table exists
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'tblusers'");
        if (mysqli_num_rows($result) > 0) {
            echo "VERIFIED: tblusers table exists<br>";
        }
        
    } else {
        echo "ERROR: " . mysqli_error($conn) . "<br>";
    }
    
    mysqli_close($conn);
} else {
    echo "Connection failed: " . mysqli_connect_error() . "<br>";
}

echo "<br><a href='index.php'>Test your application now</a>";
?> 