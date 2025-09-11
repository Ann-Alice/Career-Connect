<?php
// Trace execution to identify where the timeout occurs
echo "<html><body>\n";
echo "<h1>Tracing execution to identify timeout location</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting trace at: " . date('Y-m-d H:i:s') . "</p>\n";

// Create a log file to track execution
$log_file = 'execution_trace.log';
file_put_contents($log_file, "=== Execution Trace Started ===\n", FILE_APPEND);

// Function to log execution steps
function log_step($step) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$step}\n", FILE_APPEND);
    echo "<p>[{$timestamp}] {$step}</p>\n";
}

log_step("Starting session tracing");

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

log_step("Execution trace completed successfully");
echo "<p>Execution trace completed successfully. Check {$log_file} for details.</p>\n";
echo "</body></html>\n";
?>