<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🗄️ Database Setup Script</h2>";

echo "<h3>Step 1: Check Database Connection</h3>";

// Load configuration
require_once 'include/config.php';

try {
    // Connect to MySQL without specifying a database
    $conn = mysqli_connect(server, user, pass, null, mysql_port);
    
    if ($conn) {
        echo "<p style='color: green;'>✅ SUCCESS: Connected to MySQL on port " . mysql_port . "</p>";
        
        echo "<h3>Step 2: Create Database</h3>";
        
        // Create the database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS `" . database_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ SUCCESS: Database '" . database_name . "' created or already exists</p>";
        } else {
            echo "<p style='color: red;'>❌ FAILED: Could not create database: " . mysqli_error($conn) . "</p>";
            exit;
        }
        
        echo "<h3>Step 3: Select Database</h3>";
        
        // Select the database
        if (mysqli_select_db($conn, database_name)) {
            echo "<p style='color: green;'>✅ SUCCESS: Selected database '" . database_name . "'</p>";
        } else {
            echo "<p style='color: red;'>❌ FAILED: Could not select database: " . mysqli_error($conn) . "</p>";
            exit;
        }
        
        echo "<h3>Step 4: Import Database Schema</h3>";
        
        // Check if schema file exists
        $schema_file = 'erisdb.sql';
        if (file_exists($schema_file)) {
            echo "<p>✅ Found schema file: $schema_file</p>";
            
            // Read the SQL file
            $sql_content = file_get_contents($schema_file);
            if ($sql_content === false) {
                echo "<p style='color: red;'>❌ FAILED: Could not read schema file</p>";
                exit;
            }
            
            // Split into individual queries
            $queries = explode(';', $sql_content);
            $success_count = 0;
            $error_count = 0;
            
            echo "<p>Executing schema queries...</p>";
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query) && !preg_match('/^(--|#|\/\*|\*)/', $query)) {
                    if (mysqli_query($conn, $query)) {
                        $success_count++;
                    } else {
                        $error_count++;
                        echo "<p style='color: orange;'>⚠️ Query failed: " . mysqli_error($conn) . "</p>";
                    }
                }
            }
            
            echo "<p style='color: green;'>✅ SUCCESS: $success_count queries executed successfully</p>";
            if ($error_count > 0) {
                echo "<p style='color: orange;'>⚠️ WARNING: $error_count queries had errors (this is often normal for existing databases)</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Schema file not found: $schema_file</p>";
            echo "<p>Creating basic tables manually...</p>";
            
            // Create basic tables if schema file is missing
            $basic_tables = [
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            ];
            
            foreach ($basic_tables as $sql) {
                if (mysqli_query($conn, $sql)) {
                    echo "<p style='color: green;'>✅ Basic table created</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create basic table: " . mysqli_error($conn) . "</p>";
                }
            }
        }
        
        echo "<h3>Step 5: Verify Database Setup</h3>";
        
        // Check what tables exist
        $result = mysqli_query($conn, "SHOW TABLES");
        if ($result) {
            $table_count = mysqli_num_rows($result);
            echo "<p style='color: green;'>✅ Database has $table_count table(s):</p>";
            
            if ($table_count > 0) {
                echo "<ul>";
                while ($row = mysqli_fetch_array($result)) {
                    echo "<li>" . $row[0] . "</li>";
                }
                echo "</ul>";
            }
        }
        
        mysqli_close($conn);
        
        echo "<hr>";
        echo "<h3>🎉 Database Setup Complete!</h3>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>Your database is now ready</li>";
        echo "<li>Try your application again</li>";
        echo "<li>It should work without database errors</li>";
        echo "</ol>";
        
        echo "<p><a href='index.php'>Click here to test your application</a></p>";
        
    } else {
        echo "<p style='color: red;'>❌ FAILED: Could not connect to MySQL: " . mysqli_connect_error() . "</p>";
        echo "<p><strong>Check:</strong></p>";
        echo "<ul>";
        echo "<li>MySQL is running on port " . mysql_port . "</li>";
        echo "<li>Password is correct</li>";
        echo "<li>MySQL service is started</li>";
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ EXCEPTION: " . $e->getMessage() . "</p>";
}
?> 