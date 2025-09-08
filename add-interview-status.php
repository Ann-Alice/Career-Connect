<?php
// Add interview status columns to tbljobregistration table
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Adding Interview Status Columns</h1>";

try {
    // Database connection
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'erisdb';
    $port = 4306;
    
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    if (!$conn) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }
    echo "✅ Database connection successful<br>";
    
    // Check if INTERVIEW_STATUS column exists
    echo "<h2>Checking for INTERVIEW_STATUS column...</h2>";
    $sql = "SHOW COLUMNS FROM `tbljobregistration` LIKE 'INTERVIEW_STATUS'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "Adding INTERVIEW_STATUS column...<br>";
        $sql = "ALTER TABLE `tbljobregistration` ADD COLUMN `INTERVIEW_STATUS` enum('Pending','Completed','In Progress','Cancelled') DEFAULT 'Pending'";
        if (mysqli_query($conn, $sql)) {
            echo "✅ INTERVIEW_STATUS column added successfully<br>";
        } else {
            echo "❌ Error adding INTERVIEW_STATUS column: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "✅ INTERVIEW_STATUS column already exists<br>";
    }
    
    // Check if INTERVIEW_COMPLETED_AT column exists
    echo "<h2>Checking for INTERVIEW_COMPLETED_AT column...</h2>";
    $sql = "SHOW COLUMNS FROM `tbljobregistration` LIKE 'INTERVIEW_COMPLETED_AT'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "Adding INTERVIEW_COMPLETED_AT column...<br>";
        $sql = "ALTER TABLE `tbljobregistration` ADD COLUMN `INTERVIEW_COMPLETED_AT` timestamp NULL DEFAULT NULL";
        if (mysqli_query($conn, $sql)) {
            echo "✅ INTERVIEW_COMPLETED_AT column added successfully<br>";
        } else {
            echo "❌ Error adding INTERVIEW_COMPLETED_AT column: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "✅ INTERVIEW_COMPLETED_AT column already exists<br>";
    }
    
    // Update existing records that have INTERVIEW_RESULTS to have status 'Completed'
    echo "<h2>Updating existing interview records...</h2>";
    $sql = "UPDATE `tbljobregistration` SET `INTERVIEW_STATUS` = 'Completed' WHERE `INTERVIEW_RESULTS` IS NOT NULL";
    if (mysqli_query($conn, $sql)) {
        $affected = mysqli_affected_rows($conn);
        echo "✅ Updated $affected existing interview records to 'Completed' status<br>";
    } else {
        echo "❌ Error updating existing records: " . mysqli_error($conn) . "<br>";
    }
    
    // Show table structure
    echo "<h2>Current table structure:</h2>";
    $sql = "DESCRIBE `tbljobregistration`";
    $result = mysqli_query($conn, $sql);
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p>✅ Interview status columns have been added successfully.</p>";
    echo "<p>You can now use the interview results dashboard.</p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

mysqli_close($conn);
?> 