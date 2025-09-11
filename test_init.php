<?php
// Test initialization process
echo "<html><body>\n";
echo "<h1>Testing initialization process</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a reasonable execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

try {
    echo "<p>Including initialize.php...</p>\n";
    require_once("include/initialize.php");
    echo "<p>initialize.php included successfully.</p>\n";
    
    echo "<p>Checking if \$mydb is set: " . (isset($mydb) ? 'Yes' : 'No') . "</p>\n";
    
    if (isset($mydb)) {
        echo "<p>Database connection appears to be working.</p>\n";
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
}

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
echo "</body></html>\n";
?>