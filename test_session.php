<?php
// Test session handling
echo "Starting session test...\n";

// Start session
session_start();

echo "Session started successfully.\n";

// Set a simple session variable
$_SESSION['test'] = 'value';

echo "Session variable set.\n";

// Retrieve the session variable
echo "Session variable value: " . $_SESSION['test'] . "\n";

// End session
session_write_close();

echo "Session test completed successfully.\n";
?>