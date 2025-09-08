<?php
require_once('include/initialize.php');

global $mydb;
$mydb->setQuery('DESCRIBE tblcompany');
$result = $mydb->loadResultList();

echo "Current tblcompany structure:\n";
foreach($result as $row) {
    echo $row->Field . " | " . $row->Type . " | " . $row->Null . " | " . ($row->Default === null ? 'NULL' : $row->Default) . "\n";
}
?>
<?php
echo "<h2>🔍 Checking tblusers Table Structure</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database</p>";
    
    // Check table structure
    $result = mysqli_query($conn, "DESCRIBE tblusers");
    if ($result) {
        echo "<h3>📋 tblusers Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check actual data
        echo "<h3>📊 Sample Data from tblusers:</h3>";
        $result = mysqli_query($conn, "SELECT * FROM tblusers LIMIT 5");
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            
            // Get column names
            $columns = [];
            $first_row = mysqli_fetch_assoc($result);
            if ($first_row) {
                $columns = array_keys($first_row);
                echo "<tr>";
                foreach ($columns as $col) {
                    echo "<th>$col</th>";
                }
                echo "</tr>";
                
                // Display first row
                echo "<tr>";
                foreach ($first_row as $value) {
                    echo "<td>" . (strlen($value) > 20 ? substr($value, 0, 20) . "..." : $value) . "</td>";
                }
                echo "</tr>";
                
                // Display remaining rows
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . (strlen($value) > 20 ? substr($value, 0, 20) . "..." : $value) . "</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Could not describe table: " . mysqli_error($conn) . "</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 