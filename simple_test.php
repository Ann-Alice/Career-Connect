<?php
echo "<h2>🧪 Simple Function Test</h2>";

// Test 1: Check if functions.php can be loaded
echo "<h3>Test 1: Loading functions.php</h3>";
try {
    require_once("include/functions.php");
    echo "<p style='color: green;'>✅ functions.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
}

// Test 2: Check if specific functions exist
echo "<h3>Test 2: Checking functions</h3>";
if (function_exists('redirect')) {
    echo "<p style='color: green;'>✅ redirect() function exists</p>";
} else {
    echo "<p style='color: red;'>❌ redirect() function not found</p>";
}

if (function_exists('strip_zeros_from_date')) {
    echo "<p style='color: green;'>✅ strip_zeros_from_date() function exists</p>";
} else {
    echo "<p style='color: red;'>❌ strip_zeros_from_date() function not found</p>";
}

echo "<h3>🎯 Test Complete</h3>";
?> 