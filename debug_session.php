<?php
// Debug session handling to identify potential infinite loops
echo "Starting session debug...\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(10);

echo "Session start time: " . date('Y-m-d H:i:s') . "\n";

// Include the session file
require_once("include/session.php");

echo "Session.php included.\n";

// Test session start
session_start();
echo "Session started.\n";

// Test a few functions to see if any cause issues
echo "Testing logged_in function...\n";
$is_logged_in = logged_in();
echo "logged_in result: " . ($is_logged_in ? 'true' : 'false') . "\n";

echo "Testing studlogged_in function...\n";
$is_stud_logged_in = studlogged_in();
echo "studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false') . "\n";

echo "Session end time: " . date('Y-m-d H:i:s') . "\n";
echo "Debug completed successfully.\n";
?>