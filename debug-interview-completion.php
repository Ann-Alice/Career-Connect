<?php
// Debug script for interview completion error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Interview Completion Debug</h1>";

try {
    require_once('include/initialize.php');
    echo "<div style='color: green;'>✅ Successfully loaded initialize.php</div>";
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</div>";
    exit;
}

echo "<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
.success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
.error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
.warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
.info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    if (isset($mydb)) {
        echo "<div class='success'>✅ Database object exists</div>";
        
        // Test a simple query
        $sql = "SELECT COUNT(*) as count FROM tbljobregistration";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        echo "<div class='success'>✅ Database connection working - found " . $result->count . " registrations</div>";
    } else {
        echo "<div class='error'>❌ Database object not found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Database connection error: " . $e->getMessage() . "</div>";
}

// Test 2: Check tbljobregistration table structure
echo "<h2>2. Table Structure Analysis</h2>";
try {
    $sql = "DESCRIBE tbljobregistration";
    $mydb->setQuery($sql);
    $columns = $mydb->loadResultList();
    
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $required_columns = ['INTERVIEW_STATUS', 'INTERVIEW_COMPLETED_AT', 'INTERVIEW_RESULTS'];
    $existing_columns = [];
    
    foreach ($columns as $column) {
        $existing_columns[] = $column->Field;
        $highlight = in_array($column->Field, $required_columns) ? " style='background-color: #d4edda;'" : "";
        echo "<tr$highlight>";
        echo "<td>" . htmlspecialchars($column->Field) . "</td>";
        echo "<td>" . htmlspecialchars($column->Type) . "</td>";
        echo "<td>" . htmlspecialchars($column->Null) . "</td>";
        echo "<td>" . htmlspecialchars($column->Key) . "</td>";
        echo "<td>" . htmlspecialchars($column->Default ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check for missing columns
    $missing_columns = array_diff($required_columns, $existing_columns);
    if (empty($missing_columns)) {
        echo "<div class='success'>✅ All required columns exist</div>";
    } else {
        echo "<div class='error'>❌ Missing columns: " . implode(', ', $missing_columns) . "</div>";
        echo "<div class='warning'>⚠️ These columns need to be added for interview completion to work</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Table structure error: " . $e->getMessage() . "</div>";
}

// Test 3: Check tblinterviewinvitations table
echo "<h2>3. Interview Invitations Table</h2>";
try {
    $sql = "SHOW TABLES LIKE 'tblinterviewinvitations'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "<div class='success'>✅ tblinterviewinvitations table exists</div>";
        
        // Check structure
        $sql = "DESCRIBE tblinterviewinvitations";
        $mydb->setQuery($sql);
        $columns = $mydb->loadResultList();
        
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column->Field) . "</td>";
            echo "<td>" . htmlspecialchars($column->Type) . "</td>";
            echo "<td>" . htmlspecialchars($column->Null) . "</td>";
            echo "<td>" . htmlspecialchars($column->Key) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for sample data
        $sql = "SELECT COUNT(*) as count FROM tblinterviewinvitations";
        $mydb->setQuery($sql);
        $count = $mydb->loadSingleResult();
        echo "<div class='info'>📊 Found " . $count->count . " interview invitations</div>";
        
    } else {
        echo "<div class='error'>❌ tblinterviewinvitations table does not exist</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error checking invitations table: " . $e->getMessage() . "</div>";
}

// Test 4: Check for PHP errors
echo "<h2>4. PHP Configuration Check</h2>";
echo "<div class='info'>PHP Version: " . phpversion() . "</div>";
echo "<div class='info'>Memory Limit: " . ini_get('memory_limit') . "</div>";
echo "<div class='info'>Max Execution Time: " . ini_get('max_execution_time') . "</div>";
echo "<div class='info'>Error Reporting: " . error_reporting() . "</div>";

// Test 5: Check log files
echo "<h2>5. Error Logs Check</h2>";
$log_files = [
    'C:\xampp\apache\logs\error.log',
    'C:\xampp\php\logs\php_error_log',
    dirname(__FILE__) . '/error_log'
];

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        echo "<div class='info'>📁 Found log file: " . $log_file . "</div>";
        
        // Read last few lines
        $lines = file($log_file);
        if ($lines && count($lines) > 0) {
            $last_lines = array_slice($lines, -10);
            echo "<div class='warning'>📝 Last 10 lines from " . basename($log_file) . ":</div>";
            echo "<pre>" . htmlspecialchars(implode('', $last_lines)) . "</pre>";
        }
    }
}

// Test 6: Simulate the problematic UPDATE query
echo "<h2>6. Test UPDATE Query</h2>";
try {
    // Get a test registration
    $sql = "SELECT REGISTRATIONID FROM tbljobregistration LIMIT 1";
    $mydb->setQuery($sql);
    $test_reg = $mydb->loadSingleResult();
    
    if ($test_reg) {
        echo "<div class='info'>Testing with Registration ID: " . $test_reg->REGISTRATIONID . "</div>";
        
        // Test the exact UPDATE query from complete-interview.php
        $sql = "UPDATE tbljobregistration 
                SET INTERVIEW_STATUS = 'Testing', 
                    INTERVIEW_COMPLETED_AT = NOW() 
                WHERE REGISTRATIONID = '{$test_reg->REGISTRATIONID}'";
        
        echo "<div class='info'>Test Query:</div>";
        echo "<pre>" . htmlspecialchars($sql) . "</pre>";
        
        $mydb->setQuery($sql);
        $result = $mydb->executeQuery();
        
        if ($result) {
            echo "<div class='success'>✅ UPDATE query executed successfully</div>";
            
            // Reset the status back
            $sql = "UPDATE tbljobregistration 
                    SET INTERVIEW_STATUS = NULL, 
                        INTERVIEW_COMPLETED_AT = NULL 
                    WHERE REGISTRATIONID = '{$test_reg->REGISTRATIONID}'";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            echo "<div class='info'>🔄 Reset test data</div>";
        } else {
            echo "<div class='error'>❌ UPDATE query failed</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ No test registration found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ UPDATE query error: " . $e->getMessage() . "</div>";
}

// Test 7: Email functions
echo "<h2>7. Email Functions Test</h2>";
try {
    require_once('include/email_functions.php');
    echo "<div class='success'>✅ Email functions loaded</div>";
    
    // Check required constants
    $email_constants = ['ADMIN_EMAIL', 'SMTP_HOST', 'SMTP_USER', 'SMTP_PASS'];
    foreach ($email_constants as $const) {
        if (defined($const)) {
            echo "<div class='success'>✅ {$const} is defined</div>";
        } else {
            echo "<div class='error'>❌ {$const} is not defined</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Email functions error: " . $e->getMessage() . "</div>";
}

echo "<h2>🎯 Summary and Recommendations</h2>";
echo "<div class='info'>";
echo "<h3>Based on the analysis above:</h3>";
echo "<ol>";
echo "<li>Check if all required database columns exist</li>";
echo "<li>Verify that complete-interview.php can access the database</li>";
echo "<li>Review PHP error logs for specific errors</li>";
echo "<li>Ensure email configuration is complete</li>";
echo "<li>Test the UPDATE query manually</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='complete-interview-fixed.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Use Fixed Version</a>";
echo "<a href='test-interview-completion.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Run Full Test</a>";
echo "<a href='admin/fix-transcript-column.php' style='background: #ffc107; color: #212529; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Fix Database Schema</a>";
echo "</div>";
?>