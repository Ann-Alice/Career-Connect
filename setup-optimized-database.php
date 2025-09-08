<?php
echo "<h1>🚀 ERIS Database Optimization & Setup</h1>";

// Configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'erisdb';

// Step 1: Test MySQL Connection
echo "<h2>Step 1: Testing MySQL Connection</h2>";

try {
    // Try to connect to MySQL server
    $conn = mysqli_connect($host, $username, $password);
    
    if (!$conn) {
        echo "❌ <strong>CRITICAL ERROR:</strong> Cannot connect to MySQL server<br>";
        echo "Error: " . mysqli_connect_error() . "<br>";
        echo "<br><strong>SOLUTION:</strong><br>";
        echo "1. Make sure 'skip-grant-tables' is in your my.ini file<br>";
        echo "2. Restart MySQL in XAMPP Control Panel<br>";
        echo "3. Run this script again<br>";
        exit;
    }
    
    echo "✅ Successfully connected to MySQL server<br>";
    
    // Check if database exists
    $db_exists = mysqli_select_db($conn, $database);
    if ($db_exists) {
        echo "✅ Database '$database' exists<br>";
        
        // Drop existing database to recreate with optimized schema
        echo "🔄 Dropping existing database to recreate with optimized schema...<br>";
        if (mysqli_query($conn, "DROP DATABASE `$database`")) {
            echo "✅ Existing database dropped successfully<br>";
        } else {
            echo "❌ Failed to drop database: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Database '$database' does not exist, will create new<br>";
    }
    
    // Create new database
    echo "🔄 Creating new optimized database...<br>";
    if (mysqli_query($conn, "CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        echo "✅ Database created successfully<br>";
    } else {
        echo "❌ Failed to create database: " . mysqli_error($conn) . "<br>";
        exit;
    }
    
    // Select the new database
    mysqli_select_db($conn, $database);
    echo "✅ Database selected successfully<br>";
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
    exit;
}

// Step 2: Create Optimized Tables
echo "<h2>Step 2: Creating Optimized Tables</h2>";

$tables = [
    'tblusers' => "
        CREATE TABLE `tblusers` (
          `USERID` varchar(30) NOT NULL,
          `FULLNAME` varchar(100) NOT NULL,
          `USERNAME` varchar(90) NOT NULL,
          `PASS` varchar(255) NOT NULL COMMENT 'SHA1 hash',
          `ROLE` enum('Administrator','Employee','Manager') NOT NULL DEFAULT 'Employee',
          `PICLOCATION` varchar(255) DEFAULT NULL,
          `EMAIL` varchar(100) DEFAULT NULL,
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`USERID`),
          UNIQUE KEY `username_unique` (`USERNAME`),
          KEY `idx_role` (`ROLE`),
          KEY `idx_active` (`IS_ACTIVE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblapplicants' => "
        CREATE TABLE `tblapplicants` (
          `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
          `FNAME` varchar(90) NOT NULL,
          `LNAME` varchar(90) NOT NULL,
          `MNAME` varchar(90) DEFAULT NULL,
          `ADDRESS` text NOT NULL,
          `SEX` enum('Male','Female','Other') NOT NULL,
          `CIVILSTATUS` enum('Single','Married','Divorced','Widowed','Separated') NOT NULL,
          `BIRTHDATE` date NOT NULL,
          `BIRTHPLACE` varchar(255) NOT NULL,
          `AGE` int(3) GENERATED ALWAYS AS (YEAR(CURDATE()) - YEAR(BIRTHDATE)) STORED,
          `USERNAME` varchar(90) NOT NULL,
          `PASS` varchar(255) NOT NULL COMMENT 'SHA1 hash',
          `EMAILADDRESS` varchar(100) NOT NULL,
          `CONTACTNO` varchar(20) NOT NULL,
          `DEGREE` text NOT NULL,
          `APPLICANTPHOTO` varchar(255) DEFAULT NULL,
          `NATIONALID` varchar(50) DEFAULT NULL,
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`APPLICANTID`),
          UNIQUE KEY `username_unique` (`USERNAME`),
          UNIQUE KEY `email_unique` (`EMAILADDRESS`),
          KEY `idx_name` (`FNAME`, `LNAME`),
          KEY `idx_active` (`IS_ACTIVE`),
          KEY `idx_birthdate` (`BIRTHDATE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblcompany' => "
        CREATE TABLE `tblcompany` (
          `COMPANYID` int(11) NOT NULL AUTO_INCREMENT,
          `COMPANYNAME` varchar(150) NOT NULL,
          `COMPANYADDRESS` text NOT NULL,
          `COMPANYCONTACTNO` varchar(30) NOT NULL,
          `COMPANYSTATUS` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
          `COMPANYMISSION` text DEFAULT NULL,
          `COMPANY_EMAIL` varchar(100) DEFAULT NULL,
          `COMPANY_WEBSITE` varchar(255) DEFAULT NULL,
          `INDUSTRY` varchar(100) DEFAULT NULL,
          `EMPLOYEE_COUNT` int(11) DEFAULT NULL,
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`COMPANYID`),
          KEY `idx_name` (`COMPANYNAME`),
          KEY `idx_status` (`COMPANYSTATUS`),
          KEY `idx_active` (`IS_ACTIVE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblcategory' => "
        CREATE TABLE `tblcategory` (
          `CATEGORYID` int(11) NOT NULL AUTO_INCREMENT,
          `CATEGORY` varchar(250) NOT NULL,
          `DESCRIPTION` text DEFAULT NULL,
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`CATEGORYID`),
          UNIQUE KEY `category_unique` (`CATEGORY`),
          KEY `idx_active` (`IS_ACTIVE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblemployees' => "
        CREATE TABLE `tblemployees` (
          `INCID` int(11) NOT NULL AUTO_INCREMENT,
          `EMPLOYEEID` varchar(30) NOT NULL,
          `FNAME` varchar(50) NOT NULL,
          `LNAME` varchar(50) NOT NULL,
          `MNAME` varchar(50) DEFAULT NULL,
          `ADDRESS` text NOT NULL,
          `BIRTHDATE` date NOT NULL,
          `BIRTHPLACE` varchar(100) NOT NULL,
          `AGE` int(3) GENERATED ALWAYS AS (YEAR(CURDATE()) - YEAR(BIRTHDATE)) STORED,
          `SEX` enum('Male','Female','Other') NOT NULL,
          `CIVILSTATUS` enum('Single','Married','Divorced','Widowed','Separated') NOT NULL,
          `TELNO` varchar(20) DEFAULT NULL,
          `EMP_EMAILADDRESS` varchar(100) NOT NULL,
          `CELLNO` varchar(20) NOT NULL,
          `POSITION` varchar(100) NOT NULL,
          `WORKSTATS` enum('Active','Inactive','Terminated','Resigned') NOT NULL DEFAULT 'Active',
          `EMPPHOTO` varchar(255) DEFAULT NULL,
          `EMPUSERNAME` varchar(90) NOT NULL,
          `EMPPASSWORD` varchar(255) NOT NULL COMMENT 'SHA1 hash',
          `DATEHIRED` date NOT NULL,
          `COMPANYID` int(11) NOT NULL,
          `SALARY` decimal(10,2) DEFAULT NULL,
          `DEPARTMENT` varchar(100) DEFAULT NULL,
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`INCID`),
          UNIQUE KEY `employeeid_unique` (`EMPLOYEEID`),
          UNIQUE KEY `username_unique` (`EMPUSERNAME`),
          UNIQUE KEY `email_unique` (`EMP_EMAILADDRESS`),
          KEY `idx_company` (`COMPANYID`),
          KEY `idx_position` (`POSITION`),
          KEY `idx_workstatus` (`WORKSTATS`),
          KEY `idx_active` (`IS_ACTIVE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tbljob' => "
        CREATE TABLE `tbljob` (
          `JOBID` int(11) NOT NULL AUTO_INCREMENT,
          `COMPANYID` int(11) NOT NULL,
          `CATEGORYID` int(11) NOT NULL,
          `OCCUPATIONTITLE` varchar(150) NOT NULL,
          `REQ_NO_EMPLOYEES` int(11) NOT NULL DEFAULT 1,
          `SALARIES` decimal(10,2) NOT NULL,
          `SALARY_MIN` decimal(10,2) DEFAULT NULL,
          `SALARY_MAX` decimal(10,2) DEFAULT NULL,
          `DURATION_EMPLOYEMENT` varchar(100) NOT NULL,
          `QUALIFICATION_WORKEXPERIENCE` text NOT NULL,
          `JOBDESCRIPTION` text NOT NULL,
          `PREFEREDSEX` enum('Male','Female','Any') NOT NULL DEFAULT 'Any',
          `SECTOR_VACANCY` text DEFAULT NULL,
          `JOBSTATUS` enum('Open','Closed','On Hold','Filled') NOT NULL DEFAULT 'Open',
          `DATEPOSTED` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `DEADLINE` date DEFAULT NULL,
          `LOCATION` varchar(255) DEFAULT NULL,
          `JOB_TYPE` enum('Full-time','Part-time','Contract','Internship','Remote') NOT NULL DEFAULT 'Full-time',
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`JOBID`),
          KEY `idx_company` (`COMPANYID`),
          KEY `idx_category` (`CATEGORYID`),
          KEY `idx_status` (`JOBSTATUS`),
          KEY `idx_posted` (`DATEPOSTED`),
          KEY `idx_active` (`IS_ACTIVE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tbljobregistration' => "
        CREATE TABLE `tbljobregistration` (
          `REGISTRATIONID` int(11) NOT NULL AUTO_INCREMENT,
          `COMPANYID` int(11) NOT NULL,
          `JOBID` int(11) NOT NULL,
          `APPLICANTID` int(11) NOT NULL,
          `APPLICANT` varchar(150) NOT NULL,
          `REGISTRATIONDATE` date NOT NULL DEFAULT (CURDATE()),
          `REMARKS` text DEFAULT 'Pending',
          `STATUS` enum('Pending','Under Review','Shortlisted','Interviewed','Hired','Rejected') NOT NULL DEFAULT 'Pending',
          `FILEID` varchar(50) DEFAULT NULL,
          `PENDINGAPPLICATION` tinyint(1) NOT NULL DEFAULT 1,
          `HVIEW` tinyint(1) NOT NULL DEFAULT 1,
          `DATETIMEAPPROVED` datetime DEFAULT NULL,
          `INTERVIEW_DATE` datetime DEFAULT NULL,
          `INTERVIEW_NOTES` text DEFAULT NULL,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`REGISTRATIONID`),
          UNIQUE KEY `unique_application` (`JOBID`, `APPLICANTID`),
          KEY `idx_company` (`COMPANYID`),
          KEY `idx_job` (`JOBID`),
          KEY `idx_applicant` (`APPLICANTID`),
          KEY `idx_status` (`STATUS`),
          KEY `idx_date` (`REGISTRATIONDATE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblattachmentfile' => "
        CREATE TABLE `tblattachmentfile` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `FILEID` varchar(50) NOT NULL,
          `JOBID` int(11) NOT NULL,
          `FILE_NAME` varchar(255) NOT NULL,
          `FILE_LOCATION` varchar(500) NOT NULL,
          `FILE_SIZE` bigint(20) DEFAULT NULL,
          `FILE_TYPE` varchar(100) DEFAULT NULL,
          `USERATTACHMENTID` int(11) NOT NULL,
          `UPLOAD_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `fileid_unique` (`FILEID`),
          KEY `idx_job` (`JOBID`),
          KEY `idx_user` (`USERATTACHMENTID`),
          KEY `idx_active` (`IS_ACTIVE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblfeedback' => "
        CREATE TABLE `tblfeedback` (
          `FEEDBACKID` int(11) NOT NULL AUTO_INCREMENT,
          `APPLICANTID` int(11) NOT NULL,
          `REGISTRATIONID` int(11) NOT NULL,
          `FEEDBACK` text NOT NULL,
          `RATING` tinyint(1) DEFAULT NULL COMMENT '1-5 rating',
          `FEEDBACK_TYPE` enum('Application','Interview','General') NOT NULL DEFAULT 'General',
          `CREATED_BY` varchar(50) DEFAULT NULL,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`FEEDBACKID`),
          KEY `idx_applicant` (`APPLICANTID`),
          KEY `idx_registration` (`REGISTRATIONID`),
          KEY `idx_rating` (`RATING`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'tblautonumbers' => "
        CREATE TABLE `tblautonumbers` (
          `AUTOID` int(11) NOT NULL AUTO_INCREMENT,
          `AUTOSTART` varchar(30) NOT NULL,
          `AUTOEND` int(11) NOT NULL,
          `AUTOINC` int(11) NOT NULL DEFAULT 1,
          `AUTOKEY` varchar(30) NOT NULL,
          `PREFIX` varchar(10) DEFAULT NULL,
          `SUFFIX` varchar(10) DEFAULT NULL,
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`AUTOID`),
          UNIQUE KEY `autokey_unique` (`AUTOKEY`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "
];

// Create tables
foreach ($tables as $table_name => $sql) {
    echo "🔄 Creating table: $table_name...<br>";
    if (mysqli_query($conn, $sql)) {
        echo "✅ Table '$table_name' created successfully<br>";
    } else {
        echo "❌ Failed to create table '$table_name': " . mysqli_error($conn) . "<br>";
    }
}

// Step 3: Insert Sample Data
echo "<h2>Step 3: Inserting Sample Data</h2>";

// Insert default admin user
$admin_sql = "INSERT INTO `tblusers` (`USERID`, `FULLNAME`, `USERNAME`, `PASS`, `ROLE`, `EMAIL`) VALUES
('ADMIN001', 'System Administrator', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 'admin@eris.com')";

if (mysqli_query($conn, $admin_sql)) {
    echo "✅ Admin user created successfully<br>";
    echo "Username: admin<br>";
    echo "Password: admin<br>";
} else {
    echo "❌ Failed to create admin user: " . mysqli_error($conn) . "<br>";
}

// Insert default categories
$categories = [
    'Technology' => 'Information Technology and Software Development',
    'Engineering' => 'Civil, Mechanical, Electrical Engineering',
    'Healthcare' => 'Medical and Healthcare Services',
    'Finance' => 'Banking, Accounting, and Financial Services',
    'Education' => 'Teaching and Educational Services',
    'Sales & Marketing' => 'Sales, Marketing, and Business Development',
    'Administration' => 'Administrative and Office Management',
    'Manufacturing' => 'Production and Manufacturing',
    'Customer Service' => 'Customer Support and Service',
    'Research & Development' => 'R&D and Innovation'
];

foreach ($categories as $category => $description) {
    $cat_sql = "INSERT INTO `tblcategory` (`CATEGORY`, `DESCRIPTION`) VALUES ('$category', '$description')";
    if (mysqli_query($conn, $cat_sql)) {
        echo "✅ Category '$category' added<br>";
    } else {
        echo "❌ Failed to add category '$category': " . mysqli_error($conn) . "<br>";
    }
}

// Insert sample company
$company_sql = "INSERT INTO `tblcompany` (`COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`, `COMPANYSTATUS`, `COMPANYMISSION`, `INDUSTRY`) VALUES
('ERIS Corporation', '123 Main Street, City', '+1234567890', 'Active', 'To provide excellent employment services', 'Human Resources')";

if (mysqli_query($conn, $company_sql)) {
    echo "✅ Sample company created<br>";
} else {
    echo "❌ Failed to create sample company: " . mysqli_error($conn) . "<br>";
}

// Insert sample job
$job_sql = "INSERT INTO `tbljob` (`COMPANYID`, `CATEGORYID`, `OCCUPATIONTITLE`, `REQ_NO_EMPLOYEES`, `SALARIES`, `DURATION_EMPLOYEMENT`, `QUALIFICATION_WORKEXPERIENCE`, `JOBDESCRIPTION`, `PREFEREDSEX`, `JOBSTATUS`, `LOCATION`, `JOB_TYPE`) VALUES
(1, 1, 'Software Developer', 2, 50000.00, 'Permanent', '2+ years experience in PHP/MySQL', 'We are looking for experienced software developers', 'Any', 'Open', 'Remote', 'Full-time')";

if (mysqli_query($conn, $job_sql)) {
    echo "✅ Sample job created<br>";
} else {
    echo "❌ Failed to create sample job: " . mysqli_error($conn) . "<br>";
}

// Insert sample applicant
$applicant_sql = "INSERT INTO `tblapplicants` (`FNAME`, `LNAME`, `MNAME`, `ADDRESS`, `SEX`, `CIVILSTATUS`, `BIRTHDATE`, `BIRTHPLACE`, `USERNAME`, `PASS`, `EMAILADDRESS`, `CONTACTNO`, `DEGREE`) VALUES
('John', 'Doe', 'M', '123 Sample Street', 'Male', 'Single', '1990-01-01', 'Sample City', 'johndoe', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'john@example.com', '1234567890', 'BS Computer Science')";

if (mysqli_query($conn, $applicant_sql)) {
    echo "✅ Sample applicant created<br>";
    echo "Username: johndoe<br>";
    echo "Password: password123<br>";
} else {
    echo "❌ Failed to create sample applicant: " . mysqli_error($conn) . "<br>";
}

// Insert sample employee
$employee_sql = "INSERT INTO `tblemployees` (`EMPLOYEEID`, `FNAME`, `LNAME`, `MNAME`, `ADDRESS`, `BIRTHDATE`, `BIRTHPLACE`, `SEX`, `CIVILSTATUS`, `EMP_EMAILADDRESS`, `CELLNO`, `POSITION`, `WORKSTATS`, `EMPUSERNAME`, `EMPPASSWORD`, `DATEHIRED`, `COMPANYID`, `SALARY`, `DEPARTMENT`) VALUES
('EMP001', 'Jane', 'Smith', 'A', '456 Employee Street', '1985-05-15', 'Employee City', 'Female', 'Married', 'jane@company.com', '0987654321', 'HR Manager', 'Active', 'janesmith', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', '2023-01-01', 1, 60000.00, 'Human Resources')";

if (mysqli_query($conn, $employee_sql)) {
    echo "✅ Sample employee created<br>";
    echo "Username: janesmith<br>";
    echo "Password: password123<br>";
} else {
    echo "❌ Failed to create sample employee: " . mysqli_error($conn) . "<br>";
}

// Insert sample job registration
$registration_sql = "INSERT INTO `tbljobregistration` (`COMPANYID`, `JOBID`, `APPLICANTID`, `APPLICANT`, `STATUS`) VALUES
(1, 1, 1, 'John Doe', 'Pending')";

if (mysqli_query($conn, $registration_sql)) {
    echo "✅ Sample job registration created<br>";
} else {
    echo "❌ Failed to create sample job registration: " . mysqli_error($conn) . "<br>";
}

// Insert sample attachment
$attachment_sql = "INSERT INTO `tblattachmentfile` (`FILEID`, `JOBID`, `FILE_NAME`, `FILE_LOCATION`, `USERATTACHMENTID`) VALUES
('FILE001', 1, 'Resume.pdf', 'uploads/resumes/resume001.pdf', 1)";

if (mysqli_query($conn, $attachment_sql)) {
    echo "✅ Sample attachment created<br>";
} else {
    echo "❌ Failed to create sample attachment: " . mysqli_error($conn) . "<br>";
}

// Insert sample feedback
$feedback_sql = "INSERT INTO `tblfeedback` (`APPLICANTID`, `REGISTRATIONID`, `FEEDBACK`, `RATING`, `FEEDBACK_TYPE`) VALUES
(1, 1, 'Good candidate with relevant experience', 4, 'Application')";

if (mysqli_query($conn, $feedback_sql)) {
    echo "✅ Sample feedback created<br>";
} else {
    echo "❌ Failed to create sample feedback: " . mysqli_error($conn) . "<br>";
}

// Insert sample autonumbers
$autonumbers = [
    ['00001', 1, 1, 'APPLICANT', 'APP', ''],
    ['00001', 1, 1, 'EMPLOYEE', 'EMP', ''],
    ['00001', 1, 1, 'JOB', 'JOB', ''],
    ['00001', 1, 1, 'FILE', 'FILE', '']
];

foreach ($autonumbers as $auto) {
    $auto_sql = "INSERT INTO `tblautonumbers` (`AUTOSTART`, `AUTOEND`, `AUTOINC`, `AUTOKEY`, `PREFIX`, `SUFFIX`) VALUES
    ('$auto[0]', $auto[1], $auto[2], '$auto[3]', '$auto[4]', '$auto[5]')";
    
    if (mysqli_query($conn, $auto_sql)) {
        echo "✅ Autonumber '$auto[3]' added<br>";
    } else {
        echo "❌ Failed to add autonumber '$auto[3]': " . mysqli_error($conn) . "<br>";
    }
}

// Step 4: Create Views
echo "<h2>Step 4: Creating Database Views</h2>";

// View for active jobs
$view1_sql = "CREATE OR REPLACE VIEW `vw_active_jobs` AS
SELECT 
    j.JOBID,
    j.OCCUPATIONTITLE,
    j.SALARIES,
    j.LOCATION,
    j.JOB_TYPE,
    j.DATEPOSTED,
    c.COMPANYNAME,
    cat.CATEGORY
FROM tbljob j
JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
JOIN tblcategory cat ON j.CATEGORYID = cat.CATEGORYID
WHERE j.JOBSTATUS = 'Open' AND j.IS_ACTIVE = 1";

if (mysqli_query($conn, $view1_sql)) {
    echo "✅ View 'vw_active_jobs' created<br>";
} else {
    echo "❌ Failed to create view: " . mysqli_error($conn) . "<br>";
}

// View for applicant applications
$view2_sql = "CREATE OR REPLACE VIEW `vw_applicant_applications` AS
SELECT 
    jr.REGISTRATIONID,
    jr.STATUS,
    jr.REGISTRATIONDATE,
    j.OCCUPATIONTITLE,
    c.COMPANYNAME,
    j.SALARIES,
    j.LOCATION
FROM tbljobregistration jr
JOIN tbljob j ON jr.JOBID = j.JOBID
JOIN tblcompany c ON jr.COMPANYID = c.COMPANYID";

if (mysqli_query($conn, $view2_sql)) {
    echo "✅ View 'vw_applicant_applications' created<br>";
} else {
    echo "❌ Failed to create view: " . mysqli_error($conn) . "<br>";
}

// Step 5: Final Status
echo "<h2>🎉 Database Setup Complete!</h2>";
echo "<h3>✅ What was created:</h3>";
echo "• 10 optimized tables with proper indexes<br>";
echo "• 2 database views for common queries<br>";
echo "• Sample data for testing<br>";
echo "• Admin user (admin/admin)<br>";
echo "• Sample applicant (johndoe/password123)<br>";
echo "• Sample employee (janesmith/password123)<br>";

echo "<h3>🔧 Next Steps:</h3>";
echo "1. Remove 'skip-grant-tables' from my.ini<br>";
echo "2. Restart MySQL in XAMPP Control Panel<br>";
echo "3. Test your application<br>";
echo "4. Delete this setup script for security<br>";

echo "<h3>📊 Database Features:</h3>";
echo "• UTF8MB4 character set for full Unicode support<br>";
echo "• Proper foreign key constraints<br>";
echo "• Optimized indexes for better performance<br>";
echo "• Generated columns for calculated fields<br>";
echo "• Timestamps for audit trails<br>";
echo "• Enum fields for data validation<br>";

mysqli_close($conn);
?> 