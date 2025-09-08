<?php
echo "<h2>🧹 Cleaning Corrupted Tablespace & Importing Database</h2>";

// Connect to MySQL without selecting a database
$conn = mysqli_connect('localhost', 'root', '', null, 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to MySQL server</p>";
    
    // Drop and recreate the database to clear corrupted tablespaces
    if (mysqli_query($conn, "DROP DATABASE IF EXISTS erisdb")) {
        echo "<p style='color: green;'>✅ Dropped corrupted database</p>";
        
        // Wait a moment for cleanup
        sleep(2);
        
        // Create fresh database
        if (mysqli_query($conn, "CREATE DATABASE erisdb")) {
            echo "<p style='color: green;'>✅ Created fresh database 'erisdb'</p>";
            
            // Select the database
            if (mysqli_select_db($conn, 'erisdb')) {
                echo "<p style='color: green;'>✅ Selected database 'erisdb'</p>";
                
                // Read the SQL file
                $sql_file = 'erisdb.sql';
                if (file_exists($sql_file)) {
                    echo "<p style='color: blue;'>📁 Found SQL file: $sql_file</p>";
                    
                    $sql_content = file_get_contents($sql_file);
                    
                    // Remove comments and clean up
                    $sql_content = preg_replace('/--.*$/m', '', $sql_content);
                    $sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content);
                    
                    // Split into individual queries
                    $queries = array_filter(array_map('trim', explode(';', $sql_content)));
                    
                    $success_count = 0;
                    $error_count = 0;
                    $skipped_count = 0;
                    
                    echo "<h3>📋 Executing SQL Queries:</h3>";
                    
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (empty($query) || strlen($query) < 10) {
                            continue;
                        }
                        
                        // Skip CREATE DATABASE and USE statements
                        if (stripos($query, 'CREATE DATABASE') !== false || 
                            stripos($query, 'USE ') !== false ||
                            stripos($query, 'SET ') !== false) {
                            $skipped_count++;
                            continue;
                        }
                        
                        if (mysqli_query($conn, $query)) {
                            $success_count++;
                            echo "<p style='color: green;'>✅ Query executed successfully</p>";
                        } else {
                            $error = mysqli_error($conn);
                            $error_count++;
                            echo "<p style='color: red;'>❌ Query failed: $error</p>";
                        }
                    }
                    
                    echo "<h3>📊 Import Summary:</h3>";
                    echo "<ul>";
                    echo "<li>✅ Successful queries: $success_count</li>";
                    echo "<li>ℹ️ Skipped (CREATE/USE/SET): $skipped_count</li>";
                    if ($error_count > 0) {
                        echo "<li>❌ Failed queries: $error_count</li>";
                    }
                    echo "</ul>";
                    
                    // Verify tables were created
                    $result = mysqli_query($conn, "SHOW TABLES");
                    $table_count = mysqli_num_rows($result);
                    
                    echo "<h3>🗂️ Database Tables:</h3>";
                    echo "<p><strong>Total tables found:</strong> $table_count</p>";
                    
                    if ($table_count > 0) {
                        echo "<ul>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<li>" . $row[0] . "</li>";
                        }
                        echo "</ul>";
                        
                        if ($table_count >= 5) {
                            echo "<h3>🎉 Database Import Complete!</h3>";
                            echo "<p>Your CAREER CONNECT application database is now ready with all tables and data.</p>";
                            echo "<p><a href='index.php'>🚀 Test your application now</a></p>";
                        }
                    }
                    
                } else {
                    echo "<p style='color: red;'>❌ SQL file not found: $sql_file</p>";
                }
                
            } else {
                echo "<p style='color: red;'>❌ Could not select database: " . mysqli_error($conn) . "</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Could not create database: " . mysqli_error($conn) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Could not drop database: " . mysqli_error($conn) . "</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 