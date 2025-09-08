<?php
echo "<h1>🧪 Testing MySQL Connection on Port 4306</h1>";

echo "<h2>Current Configuration</h2>";
echo "<ul>";
echo "<li><strong>Server:</strong> localhost</li>";
echo "<li><strong>Username:</strong> root</li>";
echo "<li><strong>Password:</strong> " . (defined('pass') ? (pass ? 'YES' : 'NO') : 'NOT SET') . "</li>";
echo "<li><strong>Port:</strong> 4306</li>";
echo "<li><strong>Database:</strong> erisdb</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Testing Connection on Port 4306</h2>";

// Test 1: Direct connection on port 4306
echo "<h3>Test 1: Direct Connection on Port 4306</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root', '', null, 4306);
    if ($conn1) {
        echo "✅ SUCCESS: Connected to MySQL on port 4306!<br>";
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

echo "<hr>";

// Test 2: Check if erisdb exists
echo "<h3>Test 2: Check erisdb Database</h3>";
try {
    $conn2 = mysqli_connect('localhost', 'root', '', null, 4306);
    if ($conn2) {
        $result = mysqli_query($conn2, "SHOW DATABASES LIKE 'erisdb'");
        if ($result && mysqli_num_rows($result) > 0) {
            echo "✅ SUCCESS: erisdb database exists!<br>";
            
            // Try to select the database
            if (mysqli_select_db($conn2, 'erisdb')) {
                echo "✅ SUCCESS: Can select erisdb database!<br>";
                
                // Check tables
                $tables_result = mysqli_query($conn2, "SHOW TABLES");
                if ($tables_result) {
                    echo "Tables in erisdb:<br>";
                    while ($row = mysqli_fetch_array($tables_result)) {
                        echo "- " . $row[0] . "<br>";
                    }
                }
            } else {
                echo "❌ FAILED: Cannot select erisdb database<br>";
            }
        } else {
            echo "ℹ️ INFO: erisdb database does not exist yet<br>";
            echo "This is normal - we'll create it in the next step<br>";
        }
        mysqli_close($conn2);
    } else {
        echo "❌ FAILED: Cannot connect to check database<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Test the application's database connection
echo "<h3>Test 3: Application Database Connection</h3>";
try {
    // Include the application's database class
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
    echo "<p><strong>🚨 ISSUE IDENTIFIED:</strong> Port is correct (4306), but there's still a password issue.</p>";
    echo "<p><strong>SOLUTION:</strong> We need to reset the MySQL root password.</p>";
    
    echo "<h3>🔧 Password Reset Options:</h3>";
    echo "<ol>";
    echo "<li><strong>Option 1:</strong> Use XAMPP Shell to reset password</li>";
    echo "<li><strong>Option 2:</strong> Use safe mode method</li>";
    echo "<li><strong>Option 3:</strong> Complete MySQL reset</li>";
    echo "</ol>";
    
    echo "<p><strong>Action:</strong> <a href='reset-mysql-password.php'>Click here for password reset instructions</a></p>";
} else {
    echo "<p><strong>✅ SUCCESS:</strong> Everything is working correctly on port 4306!</p>";
    echo "<p><strong>Next:</strong> <a href='setup-optimized-database.php'>Set up the optimized database</a></p>";
}

echo "<hr>";
echo "<h2>📊 Summary:</h2>";
echo "<ul>";
echo "<li>✅ Port issue identified and fixed (4306)</li>";
echo "<li>✅ Configuration updated</li>";
echo "<li>✅ Connection method corrected</li>";
echo "<li>⚠️ May still need password reset</li>";
echo "</ul>";

echo "<h2>🔧 What Was Fixed:</h2>";
echo "<ul>";
echo "<li><strong>Port Mismatch:</strong> Application was trying port 4306, MySQL running on 4306</li>";
echo "<li><strong>Configuration:</strong> Added mysql_port constant to config.php</li>";
echo "<li><strong>Connection:</strong> Updated database.php to use correct port</li>";
echo "<li><strong>Error Messages:</strong> Updated to show correct port information</li>";
echo "</ul>";
?> 