<?php
echo "<h2>🔧 Creating Critical Tables</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Create tblusers first (this is causing your current error)
    $sql = "CREATE TABLE IF NOT EXISTS tblusers (
        USERID int(11) NOT NULL AUTO_INCREMENT,
        UNAME varchar(90) NOT NULL,
        PASS varchar(90) NOT NULL,
        TYPE varchar(30) NOT NULL,
        PRIMARY KEY (USERID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>✅ Table 'tblusers' created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating tblusers: " . mysqli_error($conn) . "</p>";
    }
    
    // Create tblapplicants
    $sql2 = "CREATE TABLE IF NOT EXISTS tblapplicants (
        APPLICANTID int(11) NOT NULL AUTO_INCREMENT,
        FNAME varchar(90) NOT NULL,
        LNAME varchar(90) NOT NULL,
        MNAME varchar(90) NOT NULL,
        ADDRESS varchar(255) NOT NULL,
        SEX varchar(11) NOT NULL,
        CIVILSTATUS varchar(30) NOT NULL,
        BIRTHDATE date NOT NULL,
        BIRTHPLACE varchar(255) NOT NULL,
        AGE int(2) NOT NULL,
        USERNAME varchar(90) NOT NULL,
        PASS varchar(90) NOT NULL,
        EMAILADDRESS varchar(90) NOT NULL,
        CONTACTNO varchar(90) NOT NULL,
        DEGREE text NOT NULL,
        APPLICANTPHOTO varchar(255) NOT NULL,
        NATIONALID varchar(255) NOT NULL,
        PRIMARY KEY (APPLICANTID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $sql2)) {
        echo "<p style='color: green;'>✅ Table 'tblapplicants' created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating tblapplicants: " . mysqli_error($conn) . "</p>";
    }
    
    // Check final status
    $result = mysqli_query($conn, "SHOW TABLES");
    $table_count = mysqli_num_rows($result);
    echo "<p><strong>Total tables:</strong> $table_count</p>";
    
    if ($table_count > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        
        if ($table_count >= 2) {
            echo "<h3>🎉 Critical tables created!</h3>";
            echo "<p><a href='index.php'>Test your application now</a></p>";
        }
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 