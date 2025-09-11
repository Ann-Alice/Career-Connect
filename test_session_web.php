<?php
// Web-based test of session functions
echo "<html><body>\n";
echo "<h1>Testing session functions in web context</h1>\n";

// Include the session file
require_once("include/session.php");

echo "<p>Session.php included successfully.</p>\n";

// Test message function
echo "<p>Testing message function...</p>\n";
message("Test message", "info");

// Test check_message function
echo "<p>Testing check_message function...</p>\n";
check_message();

// Test keyactive function
echo "<p>Testing keyactive function...</p>\n";
keyactive("test");

// Test check_active function
echo "<p>Testing check_active function...</p>\n";
check_active();

// Test header_subheader function
echo "<p>Testing header_subheader function...</p>\n";
header_subheader("product", "test");

echo "<p>All tests completed successfully.</p>\n";
echo "</body></html>\n";
?>