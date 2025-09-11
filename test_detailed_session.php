<?php
// Test session handling in web context with detailed logging
echo "<html><body>\n";
echo "<h1>Testing session handling in web context with detailed logging</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

// Log to a file for more detailed debugging
$log_file = 'debug_log.txt';
file_put_contents($log_file, "Test started at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

echo "<p>Session status before including session.php: " . session_status() . "</p>\n";
file_put_contents($log_file, "Session status before including session.php: " . session_status() . "\n", FILE_APPEND);

echo "<p>Including session.php...</p>\n";
file_put_contents($log_file, "Including session.php...\n", FILE_APPEND);

require_once("include/session.php");

echo "<p>Session status after including session.php: " . session_status() . "</p>\n";
file_put_contents($log_file, "Session status after including session.php: " . session_status() . "\n", FILE_APPEND);

echo "<p>Session ID after including session.php: " . session_id() . "</p>\n";
file_put_contents($log_file, "Session ID after including session.php: " . session_id() . "\n", FILE_APPEND);

echo "<p>Testing session functions...</p>\n";
file_put_contents($log_file, "Testing session functions...\n", FILE_APPEND);

echo "<p>Testing logged_in function...</p>\n";
file_put_contents($log_file, "Testing logged_in function...\n", FILE_APPEND);

$is_logged_in = logged_in();
echo "<p>logged_in result: " . ($is_logged_in ? 'true' : 'false') . "</p>\n";
file_put_contents($log_file, "logged_in result: " . ($is_logged_in ? 'true' : 'false') . "\n", FILE_APPEND);

echo "<p>Testing studlogged_in function...</p>\n";
file_put_contents($log_file, "Testing studlogged_in function...\n", FILE_APPEND);

$is_stud_logged_in = studlogged_in();
echo "<p>studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false') . "</p>\n";
file_put_contents($log_file, "studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false') . "\n", FILE_APPEND);

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
file_put_contents($log_file, "Test completed at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

echo "</body></html>\n";
?>