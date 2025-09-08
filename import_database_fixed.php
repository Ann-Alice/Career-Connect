<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔧 Fixed Database Import</h2>";

require_once 'include/config.php';

try {
    $conn = mysqli_connect(server, user, pass, null, mysql_port);
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Connected to MySQL on port " . mysql_port . "</p>";
        
        // Create database
        mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . database_name . "`");
        mysqli_select_db($conn, database_name);
        echo "<p style='color: green;'>✅ Database selected</p>";
        
        // Read and execute SQL file
        $sql_file = 'erisdb.sql';
        $sql_content = file_get_contents($sql_file);
        
        // Remove comments and split into queries
        $sql_content = preg_replace('/--.*$/m', '', $sql_content);
        $sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content);
        
        $queries = explode(';', $sql_content);
        $success = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && strlen($query) > 10) {
                if (mysqli_query($conn, $query)) {
                    $success++;
                }
            }
        }
        
        echo "<p style='color: green;'>✅ Imported $success queries successfully</p>";
        
        // Verify tables
        $result = mysqli_query($conn, "SHOW TABLES");
        $tables = [];
        while ($row = mysqli_fetch_array($result)) {
            $tables[] = $row[0];
        }
        
        echo "<p>Tables created: " . implode(', ', $tables) . "</p>";
        
        mysqli_close($conn);
        
        echo "<h3>🎉 Database Import Complete!</h3>";
        echo "<p><a href='index.php'>Test your application now</a></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 