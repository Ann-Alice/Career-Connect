<?php
echo "<h1>Creating tblinterviewinvitations table</h1>";

// Include the configuration
require_once('include/config.php');

try {
    // Create a new database connection
    $conn = mysqli_connect(server, user, pass, database_name, mysql_port);
    
    if (!$conn) {
        die("<p style='color: red;'>Connection failed: " . mysqli_connect_error() . "</p>");
    }
    
    echo "<p style='color: green;'>Connected successfully to database</p>";
    
    // Check if the table already exists
    $checkSql = "SHOW TABLES LIKE 'tblinterviewinvitations'";
    $result = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: orange;'>Table tblinterviewinvitations already exists</p>";
    } else {
        // Create the tblinterviewinvitations table
        $createTableSql = "CREATE TABLE tblinterviewinvitations (
            INVITATION_ID INT(11) NOT NULL AUTO_INCREMENT,
            REGISTRATIONID INT(11) NOT NULL,
            APPLICANTID INT(11) NOT NULL,
            JOBID INT(11) NOT NULL,
            EXPIRY_DATE DATETIME NOT NULL,
            TOKEN VARCHAR(255) NOT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (INVITATION_ID),
            UNIQUE KEY unique_token (TOKEN),
            KEY idx_registration (REGISTRATIONID),
            KEY idx_applicant (APPLICANTID),
            KEY idx_job (JOBID),
            KEY idx_expiry (EXPIRY_DATE)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (mysqli_query($conn, $createTableSql)) {
            echo "<p style='color: green;'>Table tblinterviewinvitations created successfully</p>";
        } else {
            echo "<p style='color: red;'>Error creating table: " . mysqli_error($conn) . "</p>";
        }
    }
    
    mysqli_close($conn);
    
    echo "<h2>Next Steps</h2>";
    echo "<p>You can now send interview invitations without errors.</p>";
    echo "<p><a href='admin/interview-invitation.php'>Go to Interview Invitation Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>