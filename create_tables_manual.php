<?php
require_once 'include/config.php';

echo "<h2>🔨 Manual Table Creation</h2>";

try {
    $conn = mysqli_connect(server, user, pass, database_name, mysql_port);
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Connected to database: " . database_name . "</p>";
        
        // Create essential tables
        $tables = [
            "CREATE TABLE IF NOT EXISTS `tblapplicants` (
                `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
                `FNAME` varchar(90) NOT NULL,
                `LNAME` varchar(90) NOT NULL,
                `MNAME` varchar(90) NOT NULL,
                `ADDRESS` varchar(255) NOT NULL,
                `SEX` varchar(11) NOT NULL,
                `CIVILSTATUS` varchar(30) NOT NULL,
                `BIRTHDATE` date NOT NULL,
                `BIRTHPLACE` varchar(255) NOT NULL,
                `AGE` int(2) NOT NULL,
                `USERNAME` varchar(90) NOT NULL,
                `PASS` varchar(90) NOT NULL,
                `EMAILADDRESS` varchar(90) NOT NULL,
                `CONTACTNO` varchar(90) NOT NULL,
                `DEGREE` text NOT NULL,
                `APPLICANTPHOTO` varchar(255) NOT NULL,
                `NATIONALID` varchar(255) NOT NULL,
                PRIMARY KEY (`APPLICANTID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS `tbluser` (
                `USERID` int(11) NOT NULL AUTO_INCREMENT,
                `UNAME` varchar(90) NOT NULL,
                `PASS` varchar(90) NOT NULL,
                `TYPE` varchar(30) NOT NULL,
                PRIMARY KEY (`USERID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS `tblcompany` (
                `COMPANYID` int(11) NOT NULL AUTO_INCREMENT,
                `COMPANYNAME` varchar(90) NOT NULL,
                `COMPANYADDRESS` varchar(255) NOT NULL,
                `COMPANYCONTACTNO` varchar(90) NOT NULL,
                PRIMARY KEY (`COMPANYID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS `tbljob` (
                `JOBID` int(11) NOT NULL AUTO_INCREMENT,
                `CATEGORY` varchar(90) NOT NULL,
                `OCCUPATIONTITLE` varchar(90) NOT NULL,
                `REQ_NO_EMPLOYEES` int(11) NOT NULL,
                `SALARIES` decimal(10,2) NOT NULL,
                `DURATION_EMPLOYMENT` varchar(90) NOT NULL,
                `QUALIFICATION_WORKEXPERIENCE` text NOT NULL,
                `JOBDESCRIPTION` text NOT NULL,
                `PREFEREDSEX` varchar(90) NOT NULL,
                `SECTOR_VACANCY` varchar(90) NOT NULL,
                `JOBSTATUS` varchar(90) NOT NULL,
                PRIMARY KEY (`JOBID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];
        
        $created = 0;
        foreach ($tables as $sql) {
            if (mysqli_query($conn, $sql)) {
                $created++;
                echo "<p style='color: green;'>✅ Table created successfully</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ " . mysqli_error($conn) . "</p>";
            }
        }
        
        echo "<p><strong>Tables created:</strong> $created</p>";
        
        // Check final status
        $result = mysqli_query($conn, "SHOW TABLES");
        $table_count = mysqli_num_rows($result);
        
        echo "<p><strong>Total tables in database:</strong> $table_count</p>";
        
        mysqli_close($conn);
        
        if ($table_count > 0) {
            echo "<h3>🎉 Database is ready!</h3>";
            echo "<p><a href='index.php'>Test your application now</a></p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 