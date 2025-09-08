<?php
echo "<h2>🎥 Importing Interview System Tables</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database: erisdb</p>";
    
    // Read the interview system SQL file
    $sql_file = 'interview-system/sql/update_schema.sql';
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
        
        echo "<h3>📋 Executing Interview System Queries:</h3>";
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query) || strlen($query) < 10) {
                continue;
            }
            
            // Skip SET statements
            if (stripos($query, 'SET ') !== false) {
                $skipped_count++;
                continue;
            }
            
            if (mysqli_query($conn, $query)) {
                $success_count++;
                echo "<p style='color: green;'>✅ Query executed successfully</p>";
            } else {
                $error = mysqli_error($conn);
                if (strpos($error, 'already exists') !== false) {
                    $skipped_count++;
                    echo "<p style='color: blue;'>ℹ️ Table/column already exists</p>";
                } else {
                    $error_count++;
                    echo "<p style='color: red;'>❌ Query failed: $error</p>";
                }
            }
        }
        
        echo "<h3>📊 Import Summary:</h3>";
        echo "<ul>";
        echo "<li>✅ Successful queries: $success_count</li>";
        echo "<li>ℹ️ Skipped (already exist): $skipped_count</li>";
        if ($error_count > 0) {
            echo "<li>❌ Failed queries: $error_count</li>";
        }
        echo "</ul>";
        
        // Verify final table count
        $result = mysqli_query($conn, "SHOW TABLES");
        $table_count = mysqli_num_rows($result);
        
        echo "<h3>🗂️ Final Database Tables:</h3>";
        echo "<p><strong>Total tables found:</strong> $table_count</p>";
        
        if ($table_count > 0) {
            echo "<ul>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
            
            echo "<h3>🎉 Interview System Ready!</h3>";
            echo "<p>Your ERIS application with interview system is now complete!</p>";
            echo "<p><a href='index.php'>🚀 Test your application now</a></p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ SQL file not found: $sql_file</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 