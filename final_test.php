<?php
// Final test to identify the issue
echo "<html><body>\n";
echo "<h1>Final test to identify the issue</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

// Log the exact sequence of includes
echo "<p>Including config.php...</p>\n";
require_once("include/config.php");

echo "<p>Including functions.php...</p>\n";
require_once("include/functions.php");

echo "<p>Including session.php...</p>\n";
require_once("include/session.php");

echo "<p>Including database.php...</p>\n";
require_once("include/database.php");

echo "<p>Creating Database instance...</p>\n";
$mydb = new Database();

echo "<p>Test completed successfully at: " . date('Y-m-d H:i:s') . "</p>\n";
echo "</body></html>\n";
?>