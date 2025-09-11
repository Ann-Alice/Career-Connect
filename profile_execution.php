<?php
// Profile execution to identify performance bottlenecks
echo "<html><body>\n";
echo "<h1>Profiling execution to identify performance bottlenecks</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a reasonable execution time for profiling
set_time_limit(60);

echo "<p>Starting profiling at: " . date('Y-m-d H:i:s') . "</p>\n";

// Create a log file to track execution time
$log_file = 'execution_profile.log';
file_put_contents($log_file, "=== Execution Profile Started ===\n", FILE_APPEND);

// Function to log execution steps with timing
function log_step($step) {
    global $log_file;
    static $start_time = null;
    static $last_time = null;
    
    if ($start_time === null) {
        $start_time = microtime(true);
        $last_time = $start_time;
    }
    
    $current_time = microtime(true);
    $elapsed_total = round(($current_time - $start_time) * 1000, 2);
    $elapsed_step = round(($current_time - $last_time) * 1000, 2);
    $last_time = $current_time;
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$step} (Step: {$elapsed_step}ms, Total: {$elapsed_total}ms)\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    echo "<p>{$log_entry}</p>\n";
}

log_step("Starting execution profiling");

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

log_step("Execution profiling completed successfully");
echo "<p>Execution profiling completed successfully. Check {$log_file} for details.</p>\n";
echo "</body></html>\n";
?>