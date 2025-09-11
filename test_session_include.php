<?php
// Test including session.php
echo "Testing session.php inclusion...\n";

// Include the session file
require_once("include/session.php");

echo "session.php included successfully.\n";

// Test calling a function from session.php
if (function_exists('logged_in')) {
    echo "logged_in function exists.\n";
} else {
    echo "logged_in function does not exist.\n";
}

// Test calling another function from session.php
if (function_exists('check_message')) {
    echo "check_message function exists.\n";
} else {
    echo "check_message function does not exist.\n";
}

echo "Test completed.\n";
?>