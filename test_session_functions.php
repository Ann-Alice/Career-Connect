<?php
// Test session functions performance
echo "Testing session functions...\n";

// Include the session file
require_once("include/session.php");

// Start session for testing
session_start();

echo "Session started.\n";

// Test message function
echo "Testing message function...\n";
message("Test message", "info");

// Test check_message function
echo "Testing check_message function...\n";
check_message();

// Test keyactive function
echo "Testing keyactive function...\n";
keyactive("test");

// Test check_active function
echo "Testing check_active function...\n";
check_active();

// Test header_subheader function
echo "Testing header_subheader function...\n";
header_subheader("product", "test");

echo "All tests completed successfully.\n";
?>