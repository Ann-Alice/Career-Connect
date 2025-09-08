<?php
require_once('include/initialize.php');
require_once('include/config.php');
require_once('include/database.php');

echo "<h2>Fixing Applicants Table Structure</h2>";

try {
    // Check if the table exists and get its current structure
    $sql = "DESCRIBE tblapplicants";
    $mydb->setQuery($sql);
    $result = $mydb->executeQuery();
    
    if (!$result) {
        echo "<p style='color: red;'>Error: Could not describe table tblapplicants</p>";
        exit;
    }
    
    $fields = [];
    while ($row = $mydb->fetch_object($result)) {
        $fields[] = $row;
    }
    
    echo "<h3>Current Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($fields as $field) {
        echo "<tr>";
        echo "<td>{$field->Field}</td>";
        echo "<td>{$field->Type}</td>";
        echo "<td>{$field->Null}</td>";
        echo "<td>{$field->Key}</td>";
        echo "<td>{$field->Default}</td>";
        echo "<td>{$field->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if APPLICANTPHOTO field exists and has proper default
    $photoFieldExists = false;
    $photoFieldNullable = false;
    
    foreach ($fields as $field) {
        if ($field->Field === 'APPLICANTPHOTO') {
            $photoFieldExists = true;
            $photoFieldNullable = ($field->Null === 'YES');
            break;
        }
    }
    
    if (!$photoFieldExists) {
        echo "<p style='color: orange;'>APPLICANTPHOTO field does not exist. Adding it...</p>";
        
        $sql = "ALTER TABLE tblapplicants ADD COLUMN APPLICANTPHOTO VARCHAR(255) DEFAULT NULL";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>Successfully added APPLICANTPHOTO field</p>";
        } else {
            echo "<p style='color: red;'>Failed to add APPLICANTPHOTO field</p>";
        }
    } elseif (!$photoFieldNullable) {
        echo "<p style='color: orange;'>APPLICANTPHOTO field exists but is not nullable. Making it nullable...</p>";
        
        $sql = "ALTER TABLE tblapplicants MODIFY COLUMN APPLICANTPHOTO VARCHAR(255) DEFAULT NULL";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>Successfully made APPLICANTPHOTO field nullable</p>";
        } else {
            echo "<p style='color: red;'>Failed to make APPLICANTPHOTO field nullable</p>";
        }
    } else {
        echo "<p style='color: green;'>APPLICANTPHOTO field exists and is properly configured</p>";
    }
    
    // Check if NATIONALID field exists and has proper default
    $nationalIdFieldExists = false;
    $nationalIdFieldNullable = false;
    
    foreach ($fields as $field) {
        if ($field->Field === 'NATIONALID') {
            $nationalIdFieldExists = true;
            $nationalIdFieldNullable = ($field->Null === 'YES');
            break;
        }
    }
    
    if (!$nationalIdFieldExists) {
        echo "<p style='color: orange;'>NATIONALID field does not exist. Adding it...</p>";
        
        $sql = "ALTER TABLE tblapplicants ADD COLUMN NATIONALID VARCHAR(50) DEFAULT NULL";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>Successfully added NATIONALID field</p>";
        } else {
            echo "<p style='color: red;'>Failed to add NATIONALID field</p>";
        }
    } elseif (!$nationalIdFieldNullable) {
        echo "<p style='color: orange;'>NATIONALID field exists but is not nullable. Making it nullable...</p>";
        
        $sql = "ALTER TABLE tblapplicants MODIFY COLUMN NATIONALID VARCHAR(50) DEFAULT NULL";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>Successfully made NATIONALID field nullable</p>";
        } else {
            echo "<p style='color: red;'>Failed to make NATIONALID field nullable</p>";
        }
    } else {
        echo "<p style='color: green;'>NATIONALID field exists and is properly configured</p>";
    }
    
    // Add IS_ACTIVE field if it doesn't exist
    $isActiveFieldExists = false;
    
    foreach ($fields as $field) {
        if ($field->Field === 'IS_ACTIVE') {
            $isActiveFieldExists = true;
            break;
        }
    }
    
    if (!$isActiveFieldExists) {
        echo "<p style='color: orange;'>IS_ACTIVE field does not exist. Adding it...</p>";
        
        $sql = "ALTER TABLE tblapplicants ADD COLUMN IS_ACTIVE TINYINT(1) NOT NULL DEFAULT 1";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>Successfully added IS_ACTIVE field</p>";
        } else {
            echo "<p style='color: red;'>Failed to add IS_ACTIVE field</p>";
        }
    } else {
        echo "<p style='color: green;'>IS_ACTIVE field exists</p>";
    }
    
    // Add CREATED_AT field if it doesn't exist
    $createdAtFieldExists = false;
    
    foreach ($fields as $field) {
        if ($field->Field === 'CREATED_AT') {
            $createdAtFieldExists = true;
            break;
        }
    }
    
    if (!$createdAtFieldExists) {
        echo "<p style='color: orange;'>CREATED_AT field does not exist. Adding it...</p>";
        
        $sql = "ALTER TABLE tblapplicants ADD COLUMN CREATED_AT TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
        $mydb->setQuery($sql);
        if ($mydb->executeQuery()) {
            echo "<p style='color: green;'>Successfully added CREATED_AT field</p>";
        } else {
            echo "<p style='color: red;'>Failed to add CREATED_AT field</p>";
        }
    } else {
        echo "<p style='color: green;'>CREATED_AT field exists</p>";
    }
    
    echo "<h3>Table structure has been updated!</h3>";
    echo "<p>You can now try registering an applicant again.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php?q=register'>Go back to registration</a></p>";
?> 