<?php
/**
 * Initialize Interview Database
 * Creates required tables and adds missing columns
 */

require_once('../include/initialize.php');

// Check if user is admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    die('Access denied. Admin login required.');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Initialize Interview Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h2 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { padding: 5px 0; }
        .btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
<div class='container'>
<h2>Initialize Interview Database</h2>";

global $mydb;

// Create required tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS `tblinterviewrecordings` (
        `RECORDING_ID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` varchar(50) NOT NULL,
        `QUESTION_NUMBER` int(11) NOT NULL DEFAULT 0,
        `FILE_PATH` varchar(500) NOT NULL,
        `DURATION` decimal(10,3) NOT NULL DEFAULT 0.000,
        `RECORDED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `CONVERSATION_TURN` int(11) NOT NULL DEFAULT 0,
        `QUESTION_TYPE` varchar(50) NOT NULL DEFAULT 'initial_answer',
        `TRANSCRIPT` text,
        PRIMARY KEY (`RECORDING_ID`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`),
        KEY `RECORDED_AT` (`RECORDED_AT`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE IF NOT EXISTS `tblinterviewinvitations` (
        `INVITATION_ID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` varchar(50) NOT NULL,
        `JOBID` varchar(50) NOT NULL,
        `TOKEN` varchar(255) NOT NULL,
        `EXPIRY_DATE` timestamp NOT NULL,
        `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `STATUS` enum('pending','completed','expired') NOT NULL DEFAULT 'pending',
        PRIMARY KEY (`INVITATION_ID`),
        UNIQUE KEY `TOKEN` (`TOKEN`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`),
        KEY `EXPIRY_DATE` (`EXPIRY_DATE`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

echo "<h3>Creating Tables:</h3><ul>";
foreach ($tables as $sql) {
    try {
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<li class='success'>✅ Table created successfully</li>";
    } catch (Exception $e) {
        echo "<li class='error'>❌ Error creating table: " . $e->getMessage() . "</li>";
    }
}
echo "</ul>";

// Add missing columns to tbljobregistration
$columns_to_add = [
    'INTERVIEW_RESULTS' => 'TEXT NULL',
    'INTERVIEW_STATUS' => "VARCHAR(20) DEFAULT NULL",
    'INTERVIEW_COMPLETED_AT' => 'DATETIME NULL',
    'ADMIN_GRADE' => 'TEXT NULL',
    'GRADED_AT' => 'DATETIME NULL',
    'EMAIL_SENT' => 'TEXT NULL',
    'EMAIL_SENT_AT' => 'DATETIME NULL'
];

echo "<h3>Adding Columns to tbljobregistration:</h3><ul>";
$added_columns = 0;
$existing_columns = 0;
$errors = 0;

foreach ($columns_to_add as $column_name => $column_definition) {
    // Check if column exists
    $check_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_SCHEMA = 'erisdb' 
                  AND TABLE_NAME = 'tbljobregistration' 
                  AND COLUMN_NAME = '{$column_name}'";
    
    $mydb->setQuery($check_sql);
    $result = $mydb->loadResultList();
    
    if (empty($result)) {
        // Column doesn't exist, add it
        $add_sql = "ALTER TABLE tbljobregistration ADD COLUMN {$column_name} {$column_definition}";
        
        try {
            $mydb->setQuery($add_sql);
            $mydb->executeQuery();
            echo "<li class='success'>✅ Added column: {$column_name}</li>";
            $added_columns++;
        } catch (Exception $e) {
            echo "<li class='error'>❌ Error adding column {$column_name}: " . $e->getMessage() . "</li>";
            $errors++;
        }
    } else {
        echo "<li class='info'>ℹ️ Column already exists: {$column_name}</li>";
        $existing_columns++;
    }
}

echo "</ul>";

echo "<h3>Summary:</h3>
<ul>
    <li class='success'>✅ Added columns: {$added_columns}</li>
    <li class='info'>ℹ️ Existing columns: {$existing_columns}</li>";

if ($errors > 0) {
    echo "<li class='error'>❌ Errors: {$errors}</li>";
}

echo "</ul>";

if ($errors == 0) {
    echo "<p class='success'><strong>🎉 Database initialization completed successfully!</strong></p>";
    echo "<p><a href='interview-results.php' class='btn'>Go to Interview Results</a></p>";
} else {
    echo "<p class='error'><strong>⚠️ Some errors occurred during initialization.</strong></p>";
    echo "<p>Please check the error messages above and try again.</p>";
}

echo "</div></body></html>";
?>