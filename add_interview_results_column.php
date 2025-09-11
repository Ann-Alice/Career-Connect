<?php
echo "<h1>Adding INTERVIEW_RESULTS column to tbljobregistration</h1>";

// Include the configuration
require_once('include/config.php');

try {
    // Create a new database connection
    $conn = mysqli_connect(server, user, pass, database_name, mysql_port);
    
    if (!$conn) {
        die("<p style='color: red;'>Connection failed: " . mysqli_connect_error() . "</p>");
    }
    
    echo "<p style='color: green;'>Connected successfully to database</p>";
    
    // Check if the column already exists
    $checkSql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_RESULTS'";
    $result = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: orange;'>Column INTERVIEW_RESULTS already exists</p>";
    } else {
        // Add the INTERVIEW_RESULTS column
        $alterSql = "ALTER TABLE tbljobregistration ADD COLUMN INTERVIEW_RESULTS TEXT NULL";
        
        if (mysqli_query($conn, $alterSql)) {
            echo "<p style='color: green;'>Column INTERVIEW_RESULTS added successfully</p>";
        } else {
            echo "<p style='color: red;'>Error adding column: " . mysqli_error($conn) . "</p>";
        }
    }
    
    // Also add an index for better performance
    $indexSql = "SHOW INDEX FROM tbljobregistration WHERE Column_name = 'INTERVIEW_RESULTS'";
    $indexResult = mysqli_query($conn, $indexSql);
    
    if (mysqli_num_rows($indexResult) == 0) {
        $createIndexSql = "CREATE INDEX idx_interview_results ON tbljobregistration (INTERVIEW_RESULTS(255))";
        if (mysqli_query($conn, $createIndexSql)) {
            echo "<p style='color: green;'>Index for INTERVIEW_RESULTS created successfully</p>";
        } else {
            echo "<p style='color: orange;'>Note: Could not create index (might not be supported on this MySQL version): " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>Index for INTERVIEW_RESULTS already exists</p>";
    }
    
    mysqli_close($conn);
    
    echo "<h2>Next Steps</h2>";
    echo "<p>You can now access the interview invitation page without errors.</p>";
    echo "<p><a href='admin/interview-invitation.php'>Go to Interview Invitation Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>