<?php
// Test session handling in web context
echo "<html><body>\n";
echo "<h1>Testing session handling in web context</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

echo "<p>Session status before including session.php: " . session_status() . "</p>\n";

echo "<p>Including session.php...</p>\n";
require_once("include/session.php");

echo "<p>Session status after including session.php: " . session_status() . "</p>\n";
echo "<p>Session ID after including session.php: " . session_id() . "</p>\n";

echo "<p>Testing session functions...</p>\n";

echo "<p>Testing logged_in function...</p>\n";
$is_logged_in = logged_in();
echo "<p>logged_in result: " . ($is_logged_in ? 'true' : 'false') . "</p>\n";

echo "<p>Testing studlogged_in function...</p>\n";
$is_stud_logged_in = studlogged_in();
echo "<p>studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false') . "</p>\n";

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
echo "</body></html>\n";
?>