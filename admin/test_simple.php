<?php
echo "<h1>🔧 Simple Admin Test Page</h1>";
echo "<p>If you can see this, the admin directory is accessible.</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP version: " . PHP_VERSION . "</p>";

// Test database connection
if (file_exists('../include/initialize.php')) {
    echo "<p style='color: green;'>✅ initialize.php exists</p>";
    
    try {
        require_once('../include/initialize.php');
        echo "<p style='color: green;'>✅ initialize.php loaded successfully</p>";
        
        if (isset($mydb) && $mydb) {
            echo "<p style='color: green;'>✅ Database connection available</p>";
        } else {
            echo "<p style='color: red;'>❌ Database connection not available</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color: red;'>❌ Fatal error loading initialize.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ initialize.php not found</p>";
}

echo "<hr>";
echo "<h3>Quick Links:</h3>";
echo "<p><a href='../index.php'>🏠 Back to Home</a></p>";
echo "<p><a href='login.php'>🔐 Admin Login</a></p>";
echo "<p><a href='index.php'>📊 Admin Dashboard</a></p>";
?> 