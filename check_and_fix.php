<?php
echo "<h2>🔍 Database Status and Fix</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Check what tables exist
    $result = mysqli_query($conn, "SHOW TABLES");
    $table_count = mysqli_num_rows($result);
    echo "<p><strong>Tables found:</strong> $table_count</p>";
    
    if ($table_count > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    
    // Try to create a table with a different name
    echo "<h3>Creating alternative table...</h3>";
    
    $sql = "CREATE TABLE IF NOT EXISTS users_table (
        USERID int(11) NOT NULL AUTO_INCREMENT,
        UNAME varchar(90) NOT NULL,
        PASS varchar(90) NOT NULL,
        TYPE varchar(30) NOT NULL,
        PRIMARY KEY (USERID)
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>✅ Alternative table 'users_table' created successfully</p>";
        
        // Insert test data
        $insert_sql = "INSERT INTO users_table (UNAME, PASS, TYPE) VALUES ('admin', 'admin123', 'Administrator')";
        if (mysqli_query($conn, $insert_sql)) {
            echo "<p style='color: green;'>✅ Test user created</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
    }
    
    mysqli_close($conn);
    
    echo "<hr>";
    echo "<h3>🎯 Solution Options:</h3>";
    echo "<ol>";
    echo "<li><strong>Use phpMyAdmin</strong> - Manually create the tables</li>";
    echo "<li><strong>Restart MySQL</strong> - This might clear the tablespace issue</li>";
    echo "<li><strong>Use alternative table names</strong> - Avoid the corrupted tablespace</li>";
    echo "</ol>";
    
    echo "<p><strong>Immediate Action:</strong> Try your application now - it might work with the existing tables!</p>";
    echo "<p><a href='index.php'>Test Application</a></p>";
    
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 