<?php
// Test session_start() specifically
echo "<html><body>\n";
echo "<h1>Testing session_start() specifically</h1>\n";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a short execution time for testing
set_time_limit(30);

echo "<p>Starting test at: " . date('Y-m-d H:i:s') . "</p>\n";

echo "<p>Calling session_start()...</p>\n";
$result = session_start();
echo "<p>session_start() returned: " . ($result ? 'true' : 'false') . "</p>\n";

echo "<p>Session ID: " . session_id() . "</p>\n";

echo "<p>Setting a session variable...</p>\n";
$_SESSION['test'] = 'value';

echo "<p>Retrieving session variable: " . $_SESSION['test'] . "</p>\n";

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
echo "</body></html>\n";
?>