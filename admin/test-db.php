<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

echo "<h2>Database Connection Test</h2>";

// Test database connection
try {
    $sql = "SELECT COUNT(*) as count FROM tbljobregistration";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    echo "<p>✅ Database connection successful</p>";
    echo "<p>Total job registrations: " . $result->count . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Check if interview results table has data
try {
    $sql = "SELECT COUNT(*) as count FROM tbljobregistration WHERE INTERVIEW_STATUS = 'Completed'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    echo "<p>Completed interviews: " . $result->count . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Error checking completed interviews: " . $e->getMessage() . "</p>";
}

// Check if reviews table exists
try {
    $sql = "SELECT COUNT(*) as count FROM tblinterviewreviews";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    echo "<p>✅ Reviews table exists with " . $result->count . " reviews</p>";
} catch (Exception $e) {
    echo "<p>❌ Reviews table doesn't exist or has error: " . $e->getMessage() . "</p>";
    echo "<p><a href='create-reviews-table.sql'>Click here to create the reviews table</a></p>";
}

// Show sample data
try {
    $sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.INTERVIEW_STATUS = 'Completed' 
            LIMIT 5";
    $mydb->setQuery($sql);
    $applications = $mydb->loadResultList();
    
    if ($applications) {
        echo "<h3>Sample Interview Data:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Name</th><th>Position</th><th>Status</th><th>Completed Date</th></tr>";
        foreach ($applications as $app) {
            echo "<tr>";
            echo "<td>" . $app->FNAME . " " . $app->LNAME . "</td>";
            echo "<td>" . $app->OCCUPATIONTITLE . "</td>";
            echo "<td>" . $app->INTERVIEW_STATUS . "</td>";
            echo "<td>" . ($app->INTERVIEW_COMPLETED_AT ? date('Y-m-d', strtotime($app->INTERVIEW_COMPLETED_AT)) : 'Not set') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No completed interviews found in database.</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error fetching sample data: " . $e->getMessage() . "</p>";
}
?> 