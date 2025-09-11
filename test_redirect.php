<?php
// Test redirect function
require_once("include/initialize.php");

echo "Testing redirect function...\n";

// Test redirect to login page
echo "Redirecting to login page...\n";
redirect(web_root."admin/login.php");

echo "Redirect test completed.\n";
?>