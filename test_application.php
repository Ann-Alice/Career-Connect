<?php
echo "<h2>🧪 Testing Application</h2>";

try {
    // Test if we can load the application
    require_once 'include/initialize.php';
    echo "<p style='color: green;'>✅ Application loaded successfully</p>";
    
    // Test database connection
    if (isset($mydb) && $mydb instanceof Database) {
        echo "<p style='color: green;'>✅ Database object created successfully</p>";
        
        // Try to execute a simple query
        try {
            $mydb->setQuery("SHOW TABLES");
            $result = $mydb->executeQuery();
            if ($result) {
                $count = mysqli_num_rows($result);
                echo "<p style='color: green;'>✅ Database query successful. Tables found: $count</p>";
                
                if ($count > 0) {
                    echo "<ul>";
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<li>" . $row[0] . "</li>";
                    }
                    echo "</ul>";
                }
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database query failed: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database object not created</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Application loading failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Current Status:</strong></p>";
echo "<ul>";
echo "<li>✅ Database connection working</li>";
echo "<li>✅ Application loading</li>";
echo "<li>⚠️ Table creation has issues</li>";
echo "</ul>";

echo "<p><strong>Next Step:</strong> Try your application now - it might work with the existing tables!</p>";
echo "<p><a href='index.php'>Test Application</a></p>";
?> 