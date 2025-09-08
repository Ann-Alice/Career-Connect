<?php
// Test database tables and structure
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Structure Test</h1>";

try {
    require_once('include/config.php');
    require_once('include/database.php');
    
    if (!isset($mydb) || !$mydb) {
        throw new Exception('Database connection failed');
    }
    
    echo "<h2>Database Connection</h2>";
    echo "✅ Database connected successfully<br>";
    
    // Test interview invitations table
    echo "<h2>Interview Invitations Table</h2>";
    $sql = "SHOW TABLES LIKE 'tblinterviewinvitations'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "✅ tblinterviewinvitations table exists<br>";
        
        // Check table structure
        $sql = "DESCRIBE tblinterviewinvitations";
        $mydb->setQuery($sql);
        $columns = $mydb->loadResultList();
        
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>{$column->Key}</td>";
            echo "<td>{$column->Default}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for sample data
        $sql = "SELECT COUNT(*) as count FROM tblinterviewinvitations";
        $mydb->setQuery($sql);
        $count = $mydb->loadSingleResult();
        echo "<br>Total invitations: {$count->count}<br>";
        
    } else {
        echo "❌ tblinterviewinvitations table missing<br>";
    }
    
    // Test interview recordings table
    echo "<h2>Interview Recordings Table</h2>";
    $sql = "SHOW TABLES LIKE 'tblinterviewrecordings'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "✅ tblinterviewrecordings table exists<br>";
        
        // Check table structure
        $sql = "DESCRIBE tblinterviewrecordings";
        $mydb->setQuery($sql);
        $columns = $mydb->loadResultList();
        
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>{$column->Key}</td>";
            echo "<td>{$column->Default}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for sample data
        $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
        $mydb->setQuery($sql);
        $count = $mydb->loadSingleResult();
        echo "<br>Total recordings: {$count->count}<br>";
        
    } else {
        echo "❌ tblinterviewrecordings table missing<br>";
    }
    
    // Test job registration table
    echo "<h2>Job Registration Table</h2>";
    $sql = "SHOW TABLES LIKE 'tbljobregistration'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "✅ tbljobregistration table exists<br>";
        
        // Check for sample data
        $sql = "SELECT COUNT(*) as count FROM tbljobregistration";
        $mydb->setQuery($sql);
        $count = $mydb->loadSingleResult();
        echo "<br>Total registrations: {$count->count}<br>";
        
    } else {
        echo "❌ tbljobregistration table missing<br>";
    }
    
    // Test a sample query
    echo "<h2>Sample Query Test</h2>";
    $sql = "SELECT i.*, r.APPLICANT, j.OCCUPATIONTITLE 
            FROM tblinterviewinvitations i 
            JOIN tbljobregistration r ON i.REGISTRATIONID = r.REGISTRATIONID 
            JOIN tbljob j ON i.JOBID = j.JOBID 
            WHERE i.EXPIRY_DATE > NOW() 
            LIMIT 1";
    $mydb->setQuery($sql);
    $invitation = $mydb->loadSingleResult();
    
    if ($invitation) {
        echo "✅ Sample query successful<br>";
        echo "Sample invitation: {$invitation->APPLICANT} for {$invitation->OCCUPATIONTITLE}<br>";
    } else {
        echo "⚠️ Sample query returned no results (this might be normal if no active invitations)<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Test Complete</h2>";
echo "<p>If you see mostly ✅ marks, the database structure is correct.</p>";
?> 