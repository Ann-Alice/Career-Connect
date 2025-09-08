<?php
echo "<h1>🧪 Quick Test - Port 3306</h1>";

echo "<h2>Current Configuration</h2>";
echo "<ul>";
echo "<li><strong>Port:</strong> " . (defined('mysql_port') ? mysql_port : 'NOT SET') . "</li>";
echo "<li><strong>Server:</strong> " . (defined('server') ? server : 'NOT SET') . "</li>";
echo "<li><strong>Username:</strong> " . (defined('user') ? user : 'NOT SET') . "</li>";
echo "<li><strong>Password:</strong> " . (defined('pass') ? (pass ? 'YES' : 'NO') : 'NOT SET') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Testing Connection on Port 3306</h2>";

try {
    $conn = mysqli_connect('localhost', 'root', '', null, 3306);
    if ($conn) {
        echo "✅ SUCCESS: Connected on port 3306!<br>";
        echo "Server Info: " . mysqli_get_server_info($conn) . "<br>";
        mysqli_close($conn);
        
        echo "<hr>";
        echo "<h2>🎉 Great! Port 3306 is Working!</h2>";
        echo "<p>Now let's test the application connection:</p>";
        
        // Test application connection
        if (file_exists('include/database.php')) {
            echo "✅ Found database.php file<br>";
            
            ob_start();
            include_once('include/database.php');
            $output = ob_get_clean();
            
            if (strpos($output, 'Database Access Denied') !== false) {
                echo "❌ FAILED: Application still getting access denied<br>";
                echo "<p><strong>Next Step:</strong> Reset MySQL root password</p>";
                echo "<p><a href='reset-mysql-password-now.php'>Click here for password reset instructions</a></p>";
            } else {
                echo "✅ SUCCESS: Application database connection working!<br>";
                echo "<p><strong>Next Step:</strong> <a href='setup-optimized-database.php'>Set up the optimized database</a></p>";
            }
        }
        
    } else {
        echo "❌ FAILED: " . mysqli_connect_error() . "<br>";
        echo "<p><strong>Issue:</strong> MySQL is not running on port 3306</p>";
        echo "<p><strong>Solution:</strong> Start MySQL in XAMPP Control Panel</p>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🔧 What to Do Next:</h2>";

echo "<h3>If Connection Failed:</h3>";
echo "<ol>";
echo "<li><strong>Open XAMPP Control Panel</strong></li>";
echo "<li><strong>Make sure my.ini shows port=3306</strong></li>";
echo "<li><strong>Click START button next to MySQL</strong></li>";
echo "<li><strong>Wait until it turns GREEN</strong></li>";
echo "<li><strong>Refresh this page to test again</strong></li>";
echo "</ol>";

echo "<h3>If Connection Succeeded:</h3>";
echo "<ol>";
echo "<li><strong>Great! Port 3306 is working</strong></li>";
echo "<li><strong>If still access denied, reset password</strong></li>";
echo "<li><strong>If working, proceed with database setup</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 Current Status:</h2>";
echo "<p><strong>Port:</strong> 3306 (standard MySQL port)</p>";
echo "<p><strong>Status:</strong> " . (isset($conn) && $conn ? 'Connected' : 'Not Connected') . "</p>";
echo "<p><strong>Next Action:</strong> " . (isset($conn) && $conn ? 'Test application or reset password' : 'Start MySQL') . "</p>";
?> 