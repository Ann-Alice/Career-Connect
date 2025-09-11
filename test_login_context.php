<?php
// Test login context
echo "<html><body>\n";
echo "<h1>Testing login context</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

echo "<p>Including initialize.php...</p>\n";
require_once("include/initialize.php");

echo "<p>initialize.php included successfully.</p>\n";

echo "<p>Checking session variables...</p>\n";
echo "<ul>\n";
echo "<li>USERID: " . (isset($_SESSION['USERID']) ? 'Set' : 'Not set') . "</li>\n";
echo "<li>CUSID: " . (isset($_SESSION['CUSID']) ? 'Set' : 'Not set') . "</li>\n";
echo "<li>ADMIN_USERID: " . (isset($_SESSION['ADMIN_USERID']) ? 'Set' : 'Not set') . "</li>\n";
echo "</ul>\n";

echo "<p>Testing logged_in function...</p>\n";
$is_logged_in = logged_in();
echo "<p>logged_in result: " . ($is_logged_in ? 'true' : 'false') . "</p>\n";

echo "<p>Testing admin_confirm_logged_in function...</p>\n";
// We won't actually call this function as it would redirect

echo "<p>Testing studlogged_in function...</p>\n";
$is_stud_logged_in = studlogged_in();
echo "<p>studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false') . "</p>\n";

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
echo "</body></html>\n";
?>