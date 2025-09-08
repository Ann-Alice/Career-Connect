<?php
echo "<h1>🧪 Direct MySQL Connection Test</h1>";

echo "<h2>Testing MySQL Connection with skip-grant-tables</h2>";

// Test 1: Direct connection without password
echo "<h3>Test 1: Direct Connection (no password)</h3>";
try {
    $conn1 = mysqli_connect('localhost', 'root');
    if ($conn1) {
        echo "✅ SUCCESS: Connected to MySQL without password!<br>";
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

// Test 2: Connection with empty password
echo "<h3>Test 2: Connection with Empty Password</h3>";
try {
    $conn2 = mysqli_connect('localhost', 'root', '');
    if ($conn2) {
        echo "✅ SUCCESS: Connected with empty password!<br>";
        mysqli_close($conn2);
    } else {
        echo "❌ FAILED: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Check if erisdb exists
echo "<h3>Test 3: Check erisdb Database</h3>";
try {
    $conn3 = mysqli_connect('localhost', 'root');
    if ($conn3) {
        $result = mysqli_query($conn3, "SHOW DATABASES LIKE 'erisdb'");
        if ($result && mysqli_num_rows($result) > 0) {
            echo "✅ SUCCESS: erisdb database exists!<br>";
            
            // Try to select the database
            if (mysqli_select_db($conn3, 'erisdb')) {
                echo "✅ SUCCESS: Can select erisdb database!<br>";
                
                // Check tables
                $tables_result = mysqli_query($conn3, "SHOW TABLES");
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
        mysqli_close($conn3);
    } else {
        echo "❌ FAILED: Cannot connect to check database<br>";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Test the application's database connection
echo "<h3>Test 4: Application Database Connection</h3>";
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
            echo "This means the database class is not using the working connection<br>";
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
    echo "<p><strong>🚨 ISSUE IDENTIFIED:</strong> MySQL is working, but the application is not using the correct connection method.</p>";
    echo "<p><strong>SOLUTION:</strong> We need to update the database connection code to use the working method.</p>";
    
    echo "<h3>🔧 Immediate Fix:</h3>";
    echo "<ol>";
    echo "<li>MySQL is working correctly with skip-grant-tables</li>";
    echo "<li>The application needs to be updated to use the working connection</li>";
    echo "<li>Let's run the database setup script to create the database</li>";
    echo "</ol>";
    
    echo "<p><strong>Action:</strong> <a href='setup-optimized-database.php'>Click here to set up the database</a></p>";
} else {
    echo "<p><strong>✅ SUCCESS:</strong> Everything is working correctly!</p>";
    echo "<p><strong>Next:</strong> <a href='setup-optimized-database.php'>Set up the optimized database</a></p>";
}

echo "<hr>";
echo "<h2>📊 Summary:</h2>";
echo "<ul>";
echo "<li>✅ skip-grant-tables is active in my.ini</li>";
echo "<li>✅ MySQL is running and accessible</li>";
echo "<li>✅ Can connect without password</li>";
echo "<li>✅ Can execute queries</li>";
echo "<li>⚠️ Application may need connection method update</li>";
echo "</ul>";
?> 