<?php
echo "<h2>🎥 Testing Admin Interview Functionality</h2>";

// Test 1: Load initialize.php
echo "<h3>Test 1: Loading initialize.php</h3>";
try {
    require_once("include/initialize.php");
    echo "<p style='color: green;'>✅ initialize.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error loading initialize.php: " . $e->getMessage() . "</p>";
}

// Test 2: Check if Database class has escape_string method
echo "<h3>Test 2: Checking Database methods</h3>";
if (isset($mydb) && $mydb instanceof Database) {
    echo "<p style='color: green;'>✅ Database object available</p>";
    
    // Check if escape_string method exists
    if (method_exists($mydb, 'escape_string')) {
        echo "<p style='color: green;'>✅ escape_string() method exists</p>";
        
        // Test the method
        try {
            $test_string = "test'string\"with\"quotes";
            $escaped = $mydb->escape_string($test_string);
            echo "<p style='color: green;'>✅ escape_string() method works: '$test_string' -> '$escaped'</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ escape_string() method failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ escape_string() method not found</p>";
    }
    
    // Check what methods are available
    $methods = get_class_methods($mydb);
    echo "<p><strong>Available Database methods:</strong></p>";
    echo "<ul>";
    foreach ($methods as $method) {
        echo "<li>$method()</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p style='color: red;'>❌ Database object not available</p>";
}

// Test 3: Check if admin interview-invitation.php can be loaded
echo "<h3>Test 3: Testing admin interview-invitation.php</h3>";
try {
    // Set a mock session for testing
    $_SESSION['ADMIN_USERID'] = 'test_admin';
    
    // Try to include the file
    ob_start();
    include('admin/interview-invitation.php');
    $content = ob_get_clean();
    
    echo "<p style='color: green;'>✅ admin/interview-invitation.php loaded successfully</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading interview-invitation.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error loading interview-invitation.php: " . $e->getMessage() . "</p>";
}

echo "<h3>🎯 Admin Interview Test Complete</h3>";
echo "<p><a href='admin/interview-invitation.php'>Try accessing admin interview-invitation now</a></p>";
?> 