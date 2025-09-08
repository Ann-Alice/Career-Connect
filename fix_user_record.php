<?php
require_once("include/config.php");

// Direct database connection
$conn = mysqli_connect(server, user, pass, database_name, mysql_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Get the current record
echo "Getting current record...\n";
$result = mysqli_query($conn, "SELECT * FROM tblusers WHERE USERID='2018001'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "Current record:\n";
    print_r($row);
    
    // Delete the current record
    echo "Deleting current record...\n";
    $delete_sql = "DELETE FROM tblusers WHERE USERID='2018001'";
    if (mysqli_query($conn, $delete_sql)) {
        echo "Record deleted successfully\n";
        
        // Re-insert the record
        echo "Re-inserting record...\n";
        $columns = array_keys($row);
        $values = array_values($row);
        
        // Escape values
        $escaped_values = array();
        foreach($values as $value) {
            $escaped_values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
        }
        
        $insert_sql = "INSERT INTO tblusers (" . implode(',', $columns) . ") VALUES (" . implode(',', $escaped_values) . ")";
        echo "Insert SQL: " . $insert_sql . "\n";
        
        if (mysqli_query($conn, $insert_sql)) {
            echo "Record re-inserted successfully\n";
        } else {
            echo "Error re-inserting record: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "Error deleting record: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Error getting record: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
echo "Fix attempt complete!\n";
?>