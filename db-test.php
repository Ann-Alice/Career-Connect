<?php
/**
 * Database Connection Test
 * Verifies that the database connection is working and the table exists
 */

header('Content-Type: application/json');

try {
    // Database connection
    require_once('include/initialize.php');
    
    // Test query to check if table exists and get count
    $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'recordings_count' => $result->count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
<?php
// Simple database connection test using mysqli
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erisdb";
$port = 4306; // Use the port specified in config.php

echo "<h1>Database Connection Test</h1>";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("<p style='color: red;'>Connection failed: " . mysqli_connect_error() . "</p>");
}

echo "<p style='color: green;'>✓ Connected successfully to database: $dbname on port $port</p>";

// Test if interview tables exist
$tables = ['tbljobregistration', 'tblinterviewrecordings', 'tblinterviewvideos', 'tblinterviewinvitations'];

echo "<h2>Table Status</h2>";
echo "<ul>";

foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<li style='color: green;'>✓ $table exists</li>";
    } else {
        echo "<li style='color: red;'>✗ $table does not exist</li>";
    }
}

echo "</ul>";

// Test if required columns exist in tbljobregistration
$required_columns = [
    'INTERVIEW_RESULTS',
    'INTERVIEW_STATUS',
    'INTERVIEW_COMPLETED_AT',
    'ADMIN_GRADE',
    'GRADED_AT',
    'EMAIL_SENT',
    'EMAIL_SENT_AT'
];

echo "<h2>Column Status in tbljobregistration</h2>";
echo "<ul>";

foreach ($required_columns as $column) {
    $sql = "SHOW COLUMNS FROM tbljobregistration LIKE '$column'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<li style='color: green;'>✓ $column exists</li>";
    } else {
        echo "<li style='color: red;'>✗ $column does not exist</li>";
    }
}

echo "</ul>";

// Close connection
mysqli_close($conn);

echo "<p>Test completed.</p>";
?>