<?php
echo "<h2>🔧 Testing Dashboard Loading</h2>";

// Test 1: Check if initialize.php can be loaded
echo "<h3>Test 1: Loading initialize.php</h3>";
try {
    require_once("../include/initialize.php");
    echo "<p style='color: green;'>✅ initialize.php loaded successfully</p>";
    
    // Test 2: Check if database connection works
    echo "<h3>Test 2: Database Connection</h3>";
    if (isset($mydb) && $mydb) {
        echo "<p style='color: green;'>✅ Database connection available</p>";
        
        // Test 3: Check if we can query the database
        echo "<h3>Test 3: Database Query Test</h3>";
        try {
            $mydb->setQuery("SELECT COUNT(*) as count FROM tblapplicants");
            $result = $mydb->loadSingleResult();
            echo "<p style='color: green;'>✅ Database query successful: " . ($result->count ?? 0) . " applicants found</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database query failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Database connection not available</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal error loading initialize.php: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Quick Links:</h3>";
echo "<p><a href='index.php'>🏠 Admin Dashboard</a></p>";
echo "<p><a href='applicants/'>👥 Applicants</a></p>";
echo "<p><a href='login.php'>�� Login</a></p>";
?> 