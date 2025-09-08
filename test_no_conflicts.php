<?php
echo "<h2>🔧 Testing for Function Conflicts</h2>";

// Test 1: Try to load initialize.php
echo "<h3>Test 1: Loading initialize.php</h3>";
try {
    require_once("include/initialize.php");
    echo "<p style='color: green;'>✅ initialize.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error loading initialize.php: " . $e->getMessage() . "</p>";
}

// Test 2: Check if functions are available
echo "<h3>Test 2: Checking function availability</h3>";
$functions_to_check = [
    'redirect',
    'redirect_to', 
    'output_message',
    'strip_zeros_from_date',
    'date_toText',
    'msgBox',
    'generateRandomString'
];

foreach ($functions_to_check as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ $func() function is available</p>";
    } else {
        echo "<p style='color: red;'>❌ $func() function not found</p>";
    }
}

// Test 3: Check if database object is available
echo "<h3>Test 3: Checking database object</h3>";
if (isset($mydb) && $mydb instanceof Database) {
    echo "<p style='color: green;'>✅ Database object available</p>";
} else {
    echo "<p style='color: red;'>❌ Database object not available</p>";
}

// Test 4: Check constants
echo "<h3>Test 4: Checking constants</h3>";
if (defined('web_root')) {
    echo "<p style='color: green;'>✅ web_root constant defined: " . web_root . "</p>";
} else {
    echo "<p style='color: red;'>❌ web_root constant not defined</p>";
}

if (defined('SITE_ROOT')) {
    echo "<p style='color: green;'>✅ SITE_ROOT constant defined: " . SITE_ROOT . "</p>";
} else {
    echo "<p style='color: red;'>❌ SITE_ROOT constant not defined</p>";
}

echo "<h3>🎯 Function Conflict Test Complete</h3>";
echo "<p>If you see all green checkmarks above, the function conflicts have been resolved!</p>";
echo "<p><a href='index.php'>Try accessing the main dashboard now</a></p>";
?> 