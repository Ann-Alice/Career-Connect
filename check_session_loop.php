<?php
// Check for session-related infinite loops
echo "<html><body>\n";
echo "<h1>Checking for session-related infinite loops</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a reasonable execution time for testing
set_time_limit(30);

echo "<p>Starting session loop check at: " . date('Y-m-d H:i:s') . "</p>\n";

// Create a log file to track execution
$log_file = 'session_loop_check.log';
file_put_contents($log_file, "=== Session Loop Check Started ===\n", FILE_APPEND);

function log_check($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
    echo "<p>[{$timestamp}] {$message}</p>\n";
}

log_check("Starting session loop check");

// Check if session is already started
log_check("Session status: " . session_status());
if (session_status() == PHP_SESSION_NONE) {
    log_check("Starting session");
    session_start();
    log_check("Session started, ID: " . session_id());
} else {
    log_check("Session already started, ID: " . session_id());
}

// Test each session function individually
log_check("Testing logged_in function");
$start_time = microtime(true);
$is_logged_in = logged_in();
$end_time = microtime(true);
log_check("logged_in result: " . ($is_logged_in ? 'true' : 'false') . " (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Testing studlogged_in function");
$start_time = microtime(true);
$is_stud_logged_in = studlogged_in();
$end_time = microtime(true);
log_check("studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false') . " (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Testing message function");
$start_time = microtime(true);
message("Test message", "info");
$end_time = microtime(true);
log_check("message function completed (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Testing check_message function");
$start_time = microtime(true);
check_message();
$end_time = microtime(true);
log_check("check_message function completed (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Testing keyactive function");
$start_time = microtime(true);
keyactive("test");
$end_time = microtime(true);
log_check("keyactive function completed (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Testing check_active function");
$start_time = microtime(true);
check_active();
$end_time = microtime(true);
log_check("check_active function completed (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Testing header_subheader function");
$start_time = microtime(true);
header_subheader("product", "test");
$end_time = microtime(true);
log_check("header_subheader function completed (took " . round(($end_time - $start_time) * 1000, 2) . "ms)");

log_check("Session loop check completed successfully");
echo "<p>Session loop check completed successfully. Check {$log_file} for details.</p>\n";
echo "</body></html>\n";
?>