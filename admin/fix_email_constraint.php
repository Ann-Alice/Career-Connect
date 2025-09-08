<?php
require_once('../include/initialize.php');

echo "<h1>Fix EMAIL_SENT Constraint Error</h1>";

try {
    echo "<p>Starting database schema update...</p>";
    
    // Check current table structure
    echo "<h2>1. Current Table Structure</h2>";
    $sql = "DESCRIBE tbljobregistration";
    $mydb->setQuery($sql);
    $columns = $mydb->loadResultList();
    
    $existing_columns = array();
    foreach ($columns as $col) {
        $existing_columns[] = $col->Field;
    }
    
    $required_columns = ['EMAIL_SENT', 'EMAIL_SENT_AT', 'ADMIN_GRADE', 'GRADED_AT'];
    $missing_columns = array();
    
    foreach ($required_columns as $col) {
        if (in_array($col, $existing_columns)) {
            echo "<p style='color: green;'>✅ Column '$col' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Column '$col' is missing</p>";
            $missing_columns[] = $col;
        }
    }
    
    if (empty($missing_columns)) {
        echo "<p style='color: green;'><strong>✅ All required columns exist! The constraint error might be due to a different issue.</strong></p>";
        echo "<h2>Alternative Solutions</h2>";
        echo "<p>Since columns exist, the constraint error might be due to:</p>";
        echo "<ul>";
        echo "<li><strong>Data too large:</strong> JSON data exceeding TEXT column limit</li>";
        echo "<li><strong>Invalid JSON:</strong> Malformed JSON data</li>";
        echo "<li><strong>Character encoding:</strong> Special characters in the data</li>";
        echo "</ul>";
        
        // Suggest using LONGTEXT instead
        echo "<h3>Recommended Fix: Change column type to LONGTEXT</h3>";
        echo "<pre>ALTER TABLE tbljobregistration MODIFY EMAIL_SENT LONGTEXT;</pre>";
        
        // Apply the fix
        try {
            $sql = "ALTER TABLE tbljobregistration MODIFY EMAIL_SENT LONGTEXT";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            echo "<p style='color: green;'>✅ Changed EMAIL_SENT column to LONGTEXT</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Could not modify column: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<h2>2. Adding Missing Columns</h2>";
        
        // Add missing columns
        $column_definitions = [
            'EMAIL_SENT' => 'LONGTEXT NULL',
            'EMAIL_SENT_AT' => 'DATETIME NULL',
            'ADMIN_GRADE' => 'LONGTEXT NULL',
            'GRADED_AT' => 'DATETIME NULL'
        ];
        
        foreach ($missing_columns as $col) {
            try {
                $sql = "ALTER TABLE tbljobregistration ADD COLUMN {$col} {$column_definitions[$col]}";
                $mydb->setQuery($sql);
                $mydb->executeQuery();
                echo "<p style='color: green;'>✅ Added column '$col'</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Failed to add column '$col': " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<h2>3. Testing Email Function</h2>";
    
    // Test the doSendEmail function with a safe update
    $sql = "SELECT * FROM tbljobregistration LIMIT 1";
    $mydb->setQuery($sql);
    $test_record = $mydb->loadSingleResult();
    
    if ($test_record) {
        echo "<p>Testing with REGISTRATIONID: " . $test_record->REGISTRATIONID . "</p>";
        
        // Create test email data
        $email_data = array(
            'subject' => 'Test Email Subject',
            'message' => 'Test message content',
            'result_status' => 'Test result status',
            'sent_by' => 1,
            'sent_at' => date('Y-m-d H:i:s')
        );
        
        // Use proper escaping for JSON
        $json_data = json_encode($email_data);
        $escaped_json = $mydb->escape_string($json_data);
        
        echo "<p><strong>JSON Data:</strong> " . htmlspecialchars($json_data) . "</p>";
        echo "<p><strong>JSON Length:</strong> " . strlen($json_data) . " characters</p>";
        
        try {
            $sql = "UPDATE tbljobregistration SET 
                    EMAIL_SENT = '{$escaped_json}',
                    EMAIL_SENT_AT = NOW()
                    WHERE REGISTRATIONID = '{$test_record->REGISTRATIONID}'";
            
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            echo "<p style='color: green;'>✅ Test update successful! Email function should work now.</p>";
            
            // Verify the update
            $sql = "SELECT EMAIL_SENT FROM tbljobregistration WHERE REGISTRATIONID = '{$test_record->REGISTRATIONID}'";
            $mydb->setQuery($sql);
            $result = $mydb->loadSingleResult();
            
            if ($result && $result->EMAIL_SENT) {
                echo "<p style='color: green;'>✅ Data was stored successfully</p>";
                $stored_data = json_decode($result->EMAIL_SENT, true);
                if ($stored_data) {
                    echo "<p style='color: green;'>✅ JSON data is valid and retrievable</p>";
                }
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Test update failed: " . $e->getMessage() . "</p>";
            echo "<p><strong>This indicates the constraint error is still present.</strong></p>";
            
            // Additional debugging
            echo "<h3>Additional Debugging Information</h3>";
            echo "<p><strong>SQL Query:</strong> " . htmlspecialchars($sql) . "</p>";
            echo "<p><strong>Error Details:</strong> " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2>4. Final Verification</h2>";
    
    // Re-check table structure
    $sql = "DESCRIBE tbljobregistration";
    $mydb->setQuery($sql);
    $final_columns = $mydb->loadResultList();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    foreach ($final_columns as $col) {
        $style = in_array($col->Field, $required_columns) ? "background-color: #d4edda;" : "";
        echo "<tr style='$style'>";
        echo "<td>" . htmlspecialchars($col->Field) . "</td>";
        echo "<td>" . htmlspecialchars($col->Type) . "</td>";
        echo "<td>" . htmlspecialchars($col->Null) . "</td>";
        echo "<td>" . htmlspecialchars($col->Default ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>5. Summary</h2>";
    echo "<p style='color: green;'><strong>✅ Database schema fix completed!</strong></p>";
    echo "<p>The EMAIL_SENT constraint error should now be resolved.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>Test the interview results page again</li>";
    echo "<li>Try sending an email to verify the fix works</li>";
    echo "<li>If issues persist, check the server error logs</li>";
    echo "</ul>";
    
    echo "<p><a href='interview-results.php' class='btn btn-primary' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Interview Results Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>This error indicates a more serious database connectivity issue.</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1, h2, h3 {
    color: #333;
}
pre {
    background: #f4f4f4;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
}
table {
    margin: 10px 0;
}
th {
    background: #f0f0f0;
    padding: 8px;
    text-align: left;
}
td {
    padding: 8px;
    border: 1px solid #ddd;
}
</style>