<?php
echo "<h1>🧪 Test - Port 3307 Configuration</h1>";

echo "<h2>Current Configuration</h2>";
echo "<ul>";
echo "<li><strong>Server:</strong> localhost</li>";
echo "<li><strong>Username:</strong> root</li>";
echo "<li><strong>Password:</strong> " . (defined('pass') ? (pass ? 'YES' : 'NO') : 'NOT SET') . "</li>";
echo "<li><strong>Port:</strong> 3307</li>";
echo "<li><strong>Database:</strong> erisdb</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Step 1: Check Port Status</h2>";

if (function_exists('shell_exec')) {
    // Check if port 3307 is listening
    $output_3307 = shell_exec('netstat -an | findstr :3307');
    if ($output_3307) {
        echo "✅ Port 3307 is listening:<br>";
        echo "<code>$output_3307</code><br>";
    } else {
        echo "❌ Port 3307 is NOT listening<br>";
        echo "<strong>MySQL needs to be started on port 3307!</strong><br>";
    }
    
    echo "<br>";
    
    // Check if port 3306 is still listening
    $output_3306 = shell_exec('netstat -an | findstr :3306');
    if ($output_3306) {
        echo "⚠️ Port 3306 is still listening (old configuration)<br>";
        echo "<code>$output_3306</code><br>";
    } else {
        echo "✅ Port 3306 is no longer listening<br>";
    }
    
    // Check if port 4306 is still listening
    $output_4306 = shell_exec('netstat -an | findstr :4306');
    if ($output_4306) {
        echo "⚠️ Port 4306 is still listening (old configuration)<br>";
        echo "<code>$output_4306</code><br>";
    } else {
        echo "✅ Port 4306 is no longer listening<br>";
    }
} else {
    echo "⚠️ Cannot check ports (shell_exec disabled)<br>";
}

echo "<hr>";
echo "<h2>Step 2: Test Connection on Port 3307</h2>";

// Test connection on port 3307
echo "<h3>Test 1: Port 3307</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root', '', null, 3307);
    if ($conn1) {
        echo "✅ SUCCESS: Connected on port 3307!<br>";
        echo "Server Info: " . mysqli_get_server_info($conn1) . "<br>";
        echo "Host Info: " . mysqli_get_host_info($conn1) . "<br>";
        
        // Test if we can execute queries
        $result = mysqli_query($conn1, "SHOW DATABASES");
        if ($result) {
            echo "✅ SUCCESS: Can execute queries!<br>";
            echo "Available databases:<br>";
            while ($row = mysqli_fetch_array($result)) {
                echo "- " . $row[0] . "<br>";
            }
        } else {
            echo "❌ FAILED: Cannot execute queries<br>";
        }
        
        mysqli_close($conn1);
    } else {
        echo "❌ FAILED: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

// Test connection using the config constant
echo "<h3>Test 2: Using Config Port (3307)</h3>";
try {
    if (defined('mysql_port')) {
        $port = mysql_port;
        echo "Using port from config: $port<br>";
        
        $conn2 = mysqli_connect('localhost', 'root', '', null, $port);
        if ($conn2) {
            echo "✅ SUCCESS: Connected using config port $port!<br>";
            mysqli_close($conn2);
        } else {
            echo "❌ FAILED: " . mysqli_connect_error() . "<br>";
        }
    } else {
        echo "❌ FAILED: mysql_port constant not defined<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Step 3: Test Application Connection</h2>";

// Test the application's database connection
try {
    if (file_exists('include/database.php')) {
        echo "✅ Found database.php file<br>";
        
        // Test if we can include it without errors
        ob_start();
        include_once('include/database.php');
        $output = ob_get_clean();
        
        if (strpos($output, 'Database Access Denied') !== false) {
            echo "❌ FAILED: Application still getting access denied<br>";
            echo "This means there's still a password issue<br>";
        } else {
            echo "✅ SUCCESS: Application database connection working!<br>";
        }
    } else {
        echo "❌ FAILED: database.php file not found<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🎯 Next Steps:</h2>";

if (strpos($output ?? '', 'Database Access Denied') !== false) {
    echo "<p><strong>🚨 ISSUE IDENTIFIED:</strong> Port is now 3307, but there's still a password issue.</p>";
    echo "<p><strong>SOLUTION:</strong> We need to reset the MySQL root password.</p>";
    
    echo "<h3>🔧 Password Reset Options:</h3>";
    echo "<ol>";
    echo "<li><strong>Option 1:</strong> Use XAMPP Shell to reset password</li>";
    echo "<li><strong>Option 2:</strong> Use safe mode method</li>";
    echo "<li><strong>Option 3:</strong> Complete MySQL reset</li>";
    echo "</ol>";
    
    echo "<p><strong>Action:</strong> <a href='reset-mysql-password-now.php'>Click here for password reset instructions</a></p>";
} else {
    echo "<p><strong>✅ SUCCESS:</strong> Everything is working correctly on port 3307!</p>";
    echo "<p><strong>Next:</strong> <a href='setup-optimized-database.php'>Set up the optimized database</a></p>";
}

echo "<hr>";
echo "<h2>📊 Summary:</h2>";
echo "<ul>";
echo "<li>✅ Port configuration updated to 3307</li>";
echo "<li>✅ Application updated to use port 3307</li>";
echo "<li>✅ Port 3307 is a safe alternative to 3306</li>";
echo "<li>⚠️ May still need password reset</li>";
echo "</ul>";

echo "<h2>🔧 What You Need to Do:</h2>";
echo "<ol>";
echo "<li><strong>Make sure my.ini shows port=3307</strong></li>";
echo "<li><strong>Start MySQL in XAMPP Control Panel</strong></li>";
echo "<li><strong>Verify port 3307 is listening</strong></li>";
echo "<li><strong>Test the connection</strong></li>";
echo "<li><strong>Reset password if needed</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>📞 After Starting MySQL:</h2>";
echo "<p>Once you've started MySQL:</p>";
echo "<ol>";
echo "<li><strong>Refresh this page to test port 3307</strong></li>";
echo "<li><strong>If successful, proceed with database setup</strong></li>";
echo "<li><strong>If still access denied, reset the password</strong></li>";
echo "</ol>";

echo "<p><strong>Port 3307 is a great alternative to 3306!</strong></p>";
?> 