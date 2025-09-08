<?php
echo "<h2>🔧 Simple Database Fix</h2>";

// Test connection
$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Create simple table
    $sql = "CREATE TABLE IF NOT EXISTS test_table (id INT, name VARCHAR(50))";
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>✅ Test table created</p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
    }
    
    // Show tables
    $result = mysqli_query($conn, "SHOW TABLES");
    $count = mysqli_num_rows($result);
    echo "<p>Tables found: $count</p>";
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 