<?php
require_once('include/initialize.php');

echo "<h1>Database Structure and Constraint Analysis</h1>";

try {
    // Check table structure
    echo "<h2>1. tbljobregistration Table Structure</h2>";
    $sql = "DESCRIBE tbljobregistration";
    $mydb->setQuery($sql);
    $columns = $mydb->loadResultList();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($col->Field) . "</td>";
        echo "<td>" . htmlspecialchars($col->Type) . "</td>";
        echo "<td>" . htmlspecialchars($col->Null) . "</td>";
        echo "<td>" . htmlspecialchars($col->Key) . "</td>";
        echo "<td>" . htmlspecialchars($col->Default) . "</td>";
        echo "<td>" . htmlspecialchars($col->Extra) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check for EMAIL_SENT column
    echo "<h2>2. EMAIL_SENT Column Check</h2>";
    $email_sent_exists = false;
    foreach ($columns as $col) {
        if ($col->Field === 'EMAIL_SENT') {
            $email_sent_exists = true;
            echo "<p style='color: green;'>✅ EMAIL_SENT column exists</p>";
            echo "<p><strong>Type:</strong> " . $col->Type . "</p>";
            echo "<p><strong>Null:</strong> " . $col->Null . "</p>";
            echo "<p><strong>Default:</strong> " . ($col->Default ?: 'NULL') . "</p>";
            break;
        }
    }
    
    if (!$email_sent_exists) {
        echo "<p style='color: red;'>❌ EMAIL_SENT column does not exist</p>";
    }
    
    // Check constraints
    echo "<h2>3. Table Constraints</h2>";
    $sql = "SELECT 
                CONSTRAINT_NAME,
                CONSTRAINT_TYPE,
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'erisdb' 
            AND TABLE_NAME = 'tbljobregistration'
            AND CONSTRAINT_NAME LIKE '%EMAIL_SENT%'";
    
    $mydb->setQuery($sql);
    $constraints = $mydb->loadResultList();
    
    if ($constraints && count($constraints) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Constraint Name</th><th>Type</th><th>Column</th><th>Referenced Table</th><th>Referenced Column</th></tr>";
        foreach ($constraints as $constraint) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($constraint->CONSTRAINT_NAME) . "</td>";
            echo "<td>" . htmlspecialchars($constraint->CONSTRAINT_TYPE) . "</td>";
            echo "<td>" . htmlspecialchars($constraint->COLUMN_NAME) . "</td>";
            echo "<td>" . htmlspecialchars($constraint->REFERENCED_TABLE_NAME ?: 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($constraint->REFERENCED_COLUMN_NAME ?: 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No EMAIL_SENT specific constraints found.</p>";
    }
    
    // Check all constraints
    echo "<h2>4. All Table Constraints</h2>";
    $sql = "SELECT 
                CONSTRAINT_NAME,
                CONSTRAINT_TYPE,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'erisdb' 
            AND TABLE_NAME = 'tbljobregistration'";
    
    $mydb->setQuery($sql);
    $all_constraints = $mydb->loadResultList();
    
    if ($all_constraints && count($all_constraints) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Constraint Name</th><th>Type</th><th>Column</th><th>Referenced Table</th><th>Referenced Column</th></tr>";
        foreach ($all_constraints as $constraint) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($constraint->CONSTRAINT_NAME) . "</td>";
            echo "<td>" . htmlspecialchars($constraint->CONSTRAINT_TYPE) . "</td>";
            echo "<td>" . htmlspecialchars($constraint->COLUMN_NAME) . "</td>";
            echo "<td>" . htmlspecialchars($constraint->REFERENCED_TABLE_NAME ?: 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($constraint->REFERENCED_COLUMN_NAME ?: 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test a simple update query
    echo "<h2>5. Test Query Analysis</h2>";
    $sql = "SELECT * FROM tbljobregistration LIMIT 1";
    $mydb->setQuery($sql);
    $test_record = $mydb->loadSingleResult();
    
    if ($test_record) {
        echo "<p>✅ Found test record with REGISTRATIONID: " . $test_record->REGISTRATIONID . "</p>";
        
        // Try to understand what's causing the constraint error
        $test_data = array(
            'subject' => 'Test Subject',
            'message' => 'Test Message',
            'result_status' => 'Test Status',
            'sent_by' => 1,
            'sent_at' => date('Y-m-d H:i:s')
        );
        
        $escaped_json = $mydb->escape_string(json_encode($test_data));
        echo "<p><strong>Test JSON data:</strong> " . htmlspecialchars(json_encode($test_data)) . "</p>";
        echo "<p><strong>Escaped JSON data:</strong> " . htmlspecialchars($escaped_json) . "</p>";
        
        // Check if EMAIL_SENT column exists before testing
        if ($email_sent_exists) {
            echo "<p>✅ EMAIL_SENT column exists, constraint issue might be with data format or size</p>";
        } else {
            echo "<p style='color: red;'>❌ EMAIL_SENT column missing - this is the likely cause of the constraint error</p>";
        }
    } else {
        echo "<p>No test records found in tbljobregistration</p>";
    }
    
    echo "<h2>6. Recommendations</h2>";
    if (!$email_sent_exists) {
        echo "<p style='color: orange;'><strong>Action Required:</strong> Add EMAIL_SENT column to tbljobregistration table</p>";
        echo "<p><strong>SQL to fix:</strong></p>";
        echo "<pre>ALTER TABLE tbljobregistration ADD COLUMN EMAIL_SENT TEXT NULL;</pre>";
        echo "<pre>ALTER TABLE tbljobregistration ADD COLUMN EMAIL_SENT_AT DATETIME NULL;</pre>";
    } else {
        echo "<p style='color: green;'>✅ Column exists, issue might be with data format or size</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>