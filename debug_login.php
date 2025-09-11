<?php
// Debug the login process specifically
echo "<html><body>\n";
echo "<h1>Debugging login process</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a reasonable execution time for debugging
set_time_limit(60);

echo "<p>Starting debug at: " . date('Y-m-d H:i:s') . "</p>\n";

// Log each step
$log_file = 'login_debug.log';
file_put_contents($log_file, "=== Login Debug Started ===\n", FILE_APPEND);

function log_debug($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
    echo "<p>[{$timestamp}] {$message}</p>\n";
}

log_debug("Starting login debug");

// Step 1: Include initialize.php (which includes session.php)
log_debug("Including initialize.php");
require_once("include/initialize.php");

log_debug("initialize.php included successfully");

// Step 2: Check if we can access session variables
log_debug("Checking session variables");
log_debug("USERID set: " . (isset($_SESSION['USERID']) ? 'Yes' : 'No'));
log_debug("ADMIN_USERID set: " . (isset($_SESSION['ADMIN_USERID']) ? 'Yes' : 'No'));

// Step 3: Test session functions
log_debug("Testing logged_in function");
$is_logged_in = logged_in();
log_debug("logged_in result: " . ($is_logged_in ? 'true' : 'false'));

log_debug("Testing studlogged_in function");
$is_stud_logged_in = studlogged_in();
log_debug("studlogged_in result: " . ($is_stud_logged_in ? 'true' : 'false'));

// Step 4: Test database connection
log_debug("Testing database connection");
if (isset($mydb)) {
    log_debug("Database instance exists");
    // Try a simple query
    try {
        $mydb->setQuery("SELECT 1 as test");
        $result = $mydb->loadSingleResult();
        log_debug("Database query successful: " . $result->test);
    } catch (Exception $e) {
        log_debug("Database query failed: " . $e->getMessage());
    }
} else {
    log_debug("Database instance does not exist");
}

log_debug("Login debug completed successfully");
echo "<p>Login debug completed successfully. Check {$log_file} for details.</p>\n";
echo "</body></html>\n";
?>