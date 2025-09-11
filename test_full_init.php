<?php
// Test full initialization process
echo "<html><body>\n";
echo "<h1>Testing full initialization process</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a reasonable execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

// Log each step
$log_file = 'full_init_test.log';
file_put_contents($log_file, "=== Full Init Test Started ===\n", FILE_APPEND);

function log_step($step) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$step}\n", FILE_APPEND);
    echo "<p>[{$timestamp}] {$step}</p>\n";
}

log_step("Starting full initialization test");

// Test each include file individually
log_step("Including config.php");
require_once("include/config.php");

log_step("Including functions.php");
require_once("include/functions.php");

log_step("Including session.php");
require_once("include/session.php");

log_step("Including database.php");
require_once("include/database.php");

log_step("Creating Database instance");
$mydb = new Database();

log_step("Including accounts.php");
require_once("include/accounts.php");

log_step("Including autonumbers.php");
require_once("include/autonumbers.php");

log_step("Including companies.php");
require_once("include/companies.php");

log_step("Including job.php");
require_once("include/job.php");

log_step("Including employees.php");
require_once("include/employees.php");

log_step("Including categories.php");
require_once("include/categories.php");

log_step("Including applicant.php");
require_once("include/applicant.php");

log_step("Including jobregistration.php");
require_once("include/jobregistration.php");

log_step("Full initialization test completed successfully");
echo "<p>Full initialization test completed successfully. Check {$log_file} for details.</p>\n";
echo "</body></html>\n";
?>