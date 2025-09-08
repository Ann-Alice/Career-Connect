<?php
echo "<h2>🔍 Direct Database Check</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Show all tables
    $result = mysqli_query($conn, "SHOW TABLES");
    if ($result) {
        $count = mysqli_num_rows($result);
        echo "<p><strong>Tables found:</strong> $count</p>";
        
        if ($count > 0) {
            echo "<ul>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        }
    }
    
    // Try to create a simple table to test permissions
    $test_sql = "CREATE TABLE IF NOT EXISTS permission_test (id INT)";
    if (mysqli_query($conn, $test_sql)) {
        echo "<p style='color: green;'>✅ Can create tables (permissions OK)</p>";
        
        // Drop the test table
        mysqli_query($conn, "DROP TABLE permission_test");
    } else {
        echo "<p style='color: red;'>❌ Cannot create tables: " . mysqli_error($conn) . "</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 