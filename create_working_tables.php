<?php
echo "<h2>🏗️ Creating Working Tables</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Create essential tables one by one
    $tables = [
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
        )",
        
        "tbluser" => "CREATE TABLE tbluser (
            USERID int(11) NOT NULL AUTO_INCREMENT,
            UNAME varchar(90) NOT NULL,
            PASS varchar(90) NOT NULL,
            TYPE varchar(30) NOT NULL,
            PRIMARY KEY (USERID)
        )",
        
        "tblcompany" => "CREATE TABLE tblcompany (
            COMPANYID int(11) NOT NULL AUTO_INCREMENT,
            COMPANYNAME varchar(90) NOT NULL,
            COMPANYADDRESS varchar(255) NOT NULL,
            COMPANYCONTACTNO varchar(90) NOT NULL,
            PRIMARY KEY (COMPANYID)
        )",
        
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
        )"
    ];
    
    $created = 0;
    foreach ($tables as $name => $sql) {
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ Table '$name' created</p>";
            $created++;
        } else {
            echo "<p style='color: orange;'>⚠️ Table '$name': " . mysqli_error($conn) . "</p>";
        }
    }
    
    echo "<p><strong>Tables created:</strong> $created</p>";
    
    // Verify tables exist
    $result = mysqli_query($conn, "SHOW TABLES");
    $table_count = mysqli_num_rows($result);
    echo "<p><strong>Total tables:</strong> $table_count</p>";
    
    if ($table_count > 0) {
        echo "<h3>🎉 Database is ready!</h3>";
        echo "<p><a href='index.php'>Test your application now</a></p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 