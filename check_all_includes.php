<?php
// Check all includes for potential issues
echo "<html><body>\n";
echo "<h1>Checking all includes for potential issues</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a reasonable execution time for testing
set_time_limit(30);

echo "<p>Starting check at: " . date('Y-m-d H:i:s') . "</p>\n";

// Log each step
$log_file = 'include_check.log';
file_put_contents($log_file, "=== Include Check Started ===\n", FILE_APPEND);

function log_check($step) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$step}\n", FILE_APPEND);
    echo "<p>[{$timestamp}] {$step}</p>\n";
}

log_check("Starting include check");

// Check each file for syntax errors
$files = [
    "include/config.php",
    "include/functions.php",
    "include/session.php",
    "include/database.php",
    "include/accounts.php",
    "include/autonumbers.php",
    "include/companies.php",
    "include/job.php",
    "include/employees.php",
    "include/categories.php",
    "include/applicant.php",
    "include/jobregistration.php"
];

foreach ($files as $file) {
    log_check("Checking {$file}");
    
    // Check if file exists
    if (!file_exists($file)) {
        log_check("ERROR: {$file} does not exist");
        continue;
    }
    
    // Try to include the file
    try {
        include_once($file);
        log_check("{$file} included successfully");
    } catch (Exception $e) {
        log_check("ERROR including {$file}: " . $e->getMessage());
    }
}

log_check("Include check completed");
echo "<p>Include check completed. Check {$log_file} for details.</p>\n";
echo "</body></html>\n";
?>