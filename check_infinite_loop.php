<?php
// Check for potential infinite loops in the application
echo "<html><body>\n";
echo "<h1>Checking for potential infinite loops</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

// Log the exact sequence of includes with timestamps
echo "<p>" . date('Y-m-d H:i:s') . " - Including config.php...</p>\n";
require_once("include/config.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including functions.php...</p>\n";
require_once("include/functions.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including session.php...</p>\n";
require_once("include/session.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including database.php...</p>\n";
require_once("include/database.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Creating Database instance...</p>\n";
$mydb = new Database();

echo "<p>" . date('Y-m-d H:i:s') . " - Including accounts.php...</p>\n";
require_once("include/accounts.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including autonumbers.php...</p>\n";
require_once("include/autonumbers.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including companies.php...</p>\n";
require_once("include/companies.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including job.php...</p>\n";
require_once("include/job.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including employees.php...</p>\n";
require_once("include/employees.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including categories.php...</p>\n";
require_once("include/categories.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including applicant.php...</p>\n";
require_once("include/applicant.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Including jobregistration.php...</p>\n";
require_once("include/jobregistration.php");

echo "<p>" . date('Y-m-d H:i:s') . " - Test completed successfully.</p>\n";
echo "</body></html>\n";
?>