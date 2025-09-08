<?php
echo "<h2>🔧 Testing Admin Functions</h2>";

// Test 1: Load initialize.php
echo "<h3>Test 1: Loading initialize.php</h3>";
try {
    require_once("include/initialize.php");
    echo "<p style='color: green;'>✅ initialize.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</p>";
}

// Test 2: Check if redirect function exists
echo "<h3>Test 2: Checking redirect function</h3>";
if (function_exists('redirect')) {
    echo "<p style='color: green;'>✅ redirect() function is available</p>";
} else {
    echo "<p style='color: red;'>❌ redirect() function not found</p>";
}

// Test 3: Check if redirect_to function exists
echo "<h3>Test 3: Checking redirect_to function</h3>";
if (function_exists('redirect_to')) {
    echo "<p style='color: green;'>✅ redirect_to() function is available</p>";
} else {
    echo "<p style='color: red;'>❌ redirect_to() function not found</p>";
}

// Test 4: Check if other essential functions exist
echo "<h3>Test 4: Checking other essential functions</h3>";
$essential_functions = ['output_message', 'strip_zeros_from_date', 'date_toText', 'msgBox', 'generateRandomString'];
foreach ($essential_functions as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ $func() function is available</p>";
    } else {
        echo "<p style='color: red;'>❌ $func() function not found</p>";
    }
}

// Test 5: Check if admin classes load
echo "<h3>Test 5: Checking admin classes</h3>";
try {
    if (class_exists('User')) {
        echo "<p style='color: green;'>✅ User class loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ User class not found</p>";
    }
    
    if (class_exists('Database')) {
        echo "<p style='color: green;'>✅ Database class loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ Database class not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking classes: " . $e->getMessage() . "</p>";
}

// Test 6: Test database connection
echo "<h3>Test 6: Testing database connection</h3>";
if (isset($mydb) && $mydb instanceof Database) {
    echo "<p style='color: green;'>✅ Database object available</p>";
    
    try {
        $result = $mydb->setQuery("SHOW TABLES");
        $result->execute();
        echo "<p style='color: green;'>✅ Database query successful - " . $result->num_rows() . " tables</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database query failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Database object not available</p>";
}

echo "<h3>🎯 Admin Functions Test Complete</h3>";
echo "<p><a href='admin/employee/index.php'>Try accessing admin employee section now</a></p>";
?> 