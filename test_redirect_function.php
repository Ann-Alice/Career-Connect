<?php
// Simple test for redirect function
echo "<h2>🧪 Testing Redirect Function</h2>";

// Include functions.php directly
require_once("include/functions.php");

echo "<p>Checking if redirect function exists...</p>";

if (function_exists('redirect')) {
    echo "<p style='color: green;'>✅ redirect() function is available</p>";
    
    // Test the function (but don't actually redirect)
    echo "<p>Testing redirect function (will not actually redirect):</p>";
    echo "<p>Function definition: ";
    $reflection = new ReflectionFunction('redirect');
    echo $reflection->getFileName() . ":" . $reflection->getStartLine();
    echo "</p>";
    
} else {
    echo "<p style='color: red;'>❌ redirect() function not found</p>";
}

if (function_exists('redirect_to')) {
    echo "<p style='color: green;'>✅ redirect_to() function is available</p>";
} else {
    echo "<p style='color: red;'>❌ redirect_to() function not found</p>";
}

echo "<h3>🎯 Test Complete</h3>";
echo "<p><a href='admin/employee/index.php'>Try accessing admin employee section now</a></p>";
?> 