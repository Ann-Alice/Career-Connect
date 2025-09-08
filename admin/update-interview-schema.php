<?php
/**
 * Update Interview Schema Script
 * Adds missing columns to tbljobregistration table for interview results functionality
 */

require_once('../include/initialize.php');

// Check if user is admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    die('Access denied. Admin login required.');
}

echo "<h2>Updating Interview System Database Schema</h2>";

// Database connection
global $mydb;

// Columns to add
$columns_to_add = [
    'INTERVIEW_RESULTS' => 'TEXT NULL',
    'INTERVIEW_STATUS' => "VARCHAR(20) DEFAULT NULL",
    'INTERVIEW_COMPLETED_AT' => 'DATETIME NULL',
    'ADMIN_GRADE' => 'TEXT NULL',
    'GRADED_AT' => 'DATETIME NULL',
    'EMAIL_SENT' => 'TEXT NULL',
    'EMAIL_SENT_AT' => 'DATETIME NULL'
];

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
            echo "<p style='color: green;'>✅ Added column: {$column_name}</p>";
            $added_columns++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error adding column {$column_name}: " . $e->getMessage() . "</p>";
            $errors++;
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Column already exists: {$column_name}</p>";
        $existing_columns++;
    }
}

echo "<h3>Summary:</h3>";
echo "<ul>";
echo "<li>✅ Added columns: {$added_columns}</li>";
echo "<li>ℹ️ Existing columns: {$existing_columns}</li>";
if ($errors > 0) {
    echo "<li>❌ Errors: {$errors}</li>";
}
echo "</ul>";

if ($errors == 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 Database schema updated successfully!</p>";
    echo "<p><a href='interview-results.php'>Go to Interview Results</a></p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>⚠️ Some errors occurred during schema update.</p>";
    echo "<p>Please check the error messages above and try again.</p>";
}
?>