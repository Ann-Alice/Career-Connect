<?php
echo "<h2>🏗️ Creating All Required Tables</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // All required tables for ERIS application
    $tables = [
        "tblusers" => "CREATE TABLE tblusers (
            USERID int(11) NOT NULL AUTO_INCREMENT,
            UNAME varchar(90) NOT NULL,
            PASS varchar(90) NOT NULL,
            TYPE varchar(30) NOT NULL,
            PRIMARY KEY (USERID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblapplicants" => "CREATE TABLE tblapplicants (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblcompany" => "CREATE TABLE tblcompany (
            COMPANYID int(11) NOT NULL AUTO_INCREMENT,
            COMPANYNAME varchar(90) NOT NULL,
            COMPANYADDRESS varchar(255) NOT NULL,
            COMPANYCONTACTNO varchar(90) NOT NULL,
            PRIMARY KEY (COMPANYID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tbljob" => "CREATE TABLE tbljob (
            JOBID int(11) NOT NULL AUTO_INCREMENT,
            CATEGORY varchar(90) NOT NULL,
            OCCUPATIONTITLE varchar(90) NOT NULL,
            REQ_NO_EMPLOYEES int(11) NOT NULL,
            SALARIES decimal(10,2) NOT NULL,
            DURATION_EMPLOYMENT varchar(90) NOT NULL,
            QUALIFICATION_WORKEXPERIENCE text NOT NULL,
            JOBDESCRIPTION text NOT NULL,
            PREFEREDSEX varchar(90) NOT NULL,
            SECTOR_VACANCY varchar(90) NOT NULL,
            JOBSTATUS varchar(90) NOT NULL,
            PRIMARY KEY (JOBID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblautonumbers" => "CREATE TABLE tblautonumbers (
            AUTOID int(11) NOT NULL AUTO_INCREMENT,
            AUTOSTART varchar(30) NOT NULL,
            AUTOEND int(11) NOT NULL,
            AUTOINC int(11) NOT NULL,
            AUTOKEY varchar(30) NOT NULL,
            PRIMARY KEY (AUTOID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblcategories" => "CREATE TABLE tblcategories (
            CATEGORYID int(11) NOT NULL AUTO_INCREMENT,
            CATEGORY varchar(90) NOT NULL,
            PRIMARY KEY (CATEGORYID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblemployees" => "CREATE TABLE tblemployees (
            EMPLOYEEID int(11) NOT NULL AUTO_INCREMENT,
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
            EMPLOYEEPHOTO varchar(255) NOT NULL,
            NATIONALID varchar(255) NOT NULL,
            PRIMARY KEY (EMPLOYEEID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tbljobregistration" => "CREATE TABLE tbljobregistration (
            REGISTRATIONID int(11) NOT NULL AUTO_INCREMENT,
            APPLICANTID int(11) NOT NULL,
            JOBID int(11) NOT NULL,
            REGISTRATIONDATE date NOT NULL,
            REMARKS varchar(255) NOT NULL,
            PRIMARY KEY (REGISTRATIONID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
    
    $created = 0;
    $errors = 0;
    
    foreach ($tables as $name => $sql) {
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ Table '$name' created</p>";
            $created++;
        } else {
            $error_msg = mysqli_error($conn);
            if (strpos($error_msg, 'already exists') !== false) {
                echo "<p style='color: blue;'>ℹ️ Table '$name' already exists</p>";
                $created++;
            } else {
                echo "<p style='color: red;'>❌ Table '$name': $error_msg</p>";
                $errors++;
            }
        }
    }
    
    echo "<p><strong>Tables created/skipped:</strong> $created</p>";
    if ($errors > 0) {
        echo "<p style='color: orange;'><strong>Errors:</strong> $errors</p>";
    }
    
    // Verify tables exist
    $result = mysqli_query($conn, "SHOW TABLES");
    $table_count = mysqli_num_rows($result);
    echo "<p><strong>Total tables in database:</strong> $table_count</p>";
    
    if ($table_count > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        
        echo "<h3>🎉 Database is ready!</h3>";
        echo "<p><a href='index.php'>Test your application now</a></p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 