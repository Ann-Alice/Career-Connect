<?php
require_once 'include/config.php';

echo "<h2>📊 Database Status Check</h2>";

try {
    $conn = mysqli_connect(server, user, pass, database_name, mysql_port);
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Connected to database: " . database_name . "</p>";
        
        $result = mysqli_query($conn, "SHOW TABLES");
        $tables = [];
        while ($row = mysqli_fetch_array($result)) {
            $tables[] = $row[0];
        }
        
        echo "<p><strong>Tables found:</strong> " . count($tables) . "</p>";
        if (count($tables) > 0) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        }
        
        mysqli_close($conn);
        
        if (count($tables) > 0) {
            echo "<h3>🎉 Database is ready!</h3>";
            echo "<p><a href='index.php'>Test your application now</a></p>";
        } else {
            echo "<h3>⚠️ Database is empty</h3>";
            echo "<p>No tables found. Run the import script again.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 