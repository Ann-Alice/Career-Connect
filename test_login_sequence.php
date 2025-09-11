<?php
// Test the exact sequence that happens when accessing the login page
echo "<html><body>\n";
echo "<h1>Testing login page sequence</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

echo "<p>Including initialize.php...</p>\n";
require_once("include/initialize.php");

echo "<p>initialize.php included successfully.</p>\n";

// This is what happens in login.php - it's just HTML, so no PHP code execution

echo "<p>Login page sequence completed at: " . date('Y-m-d H:i:s') . "</p>\n";
echo "</body></html>\n";
?>