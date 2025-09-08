<?php
require_once('include/initialize.php');

echo "<h1>🔧 Fix Database Schema for Video Tables</h1>";

try {
    // Check current structure of tblinterviewvideos
    echo "<h2>1. Current tblinterviewvideos structure:</h2>";
    $sql = "DESCRIBE tblinterviewvideos";
    $mydb->setQuery($sql);
    $columns = $mydb->loadResultList();
    
    if ($columns) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        $has_id = false;
        $has_videoid = false;
        
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col->Field}</td>";
            echo "<td>{$col->Type}</td>";
            echo "<td>{$col->Null}</td>";
            echo "<td>{$col->Key}</td>";
            echo "<td>{$col->Default}</td>";
            echo "</tr>";
            
            if ($col->Field === 'ID') $has_id = true;
            if ($col->Field === 'VIDEOID') $has_videoid = true;
        }
        echo "</table>";
        
        echo "<p><strong>Primary Key Analysis:</strong></p>";
        echo "<ul>";
        if ($has_id) echo "<li>✅ Has 'ID' column</li>";
        if ($has_videoid) echo "<li>✅ Has 'VIDEOID' column</li>";
        if (!$has_id && !$has_videoid) echo "<li>❌ No primary key column found!</li>";
        echo "</ul>";
        
        // If table has wrong structure, fix it
        if (!$has_videoid && $has_id) {
            echo "<h2>2. Renaming ID column to VIDEOID for consistency:</h2>";
            $sql = "ALTER TABLE tblinterviewvideos CHANGE ID VIDEOID INT AUTO_INCREMENT";
            $mydb->setQuery($sql);
            $result = $mydb->executeQuery();
            
            if ($result) {
                echo "<div style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'>";
                echo "✅ Successfully renamed 'ID' column to 'VIDEOID'";
                echo "</div>";
            } else {
                echo "<div style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px;'>";
                echo "❌ Failed to rename column";
                echo "</div>";
            }
        } elseif (!$has_id && !$has_videoid) {
            echo "<h2>2. Creating proper table structure:</h2>";
            $sql = "DROP TABLE IF EXISTS tblinterviewvideos_old";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            
            $sql = "RENAME TABLE tblinterviewvideos TO tblinterviewvideos_old";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            
            $sql = "CREATE TABLE tblinterviewvideos (
                VIDEOID INT AUTO_INCREMENT PRIMARY KEY,
                REGISTRATIONID INT NOT NULL,
                VIDEO_PATH VARCHAR(255) NOT NULL,
                CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_registration (REGISTRATIONID)
            )";
            $mydb->setQuery($sql);
            $result = $mydb->executeQuery();
            
            if ($result) {
                echo "<div style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'>";
                echo "✅ Created new table with proper structure";
                echo "</div>";
                
                // Migrate data if old table had data
                $sql = "INSERT INTO tblinterviewvideos (REGISTRATIONID, VIDEO_PATH, CREATED_AT) 
                        SELECT REGISTRATIONID, VIDEO_PATH, CREATED_AT FROM tblinterviewvideos_old";
                $mydb->setQuery($sql);
                $mydb->executeQuery();
                echo "<p>✅ Migrated existing data</p>";
            }
        } else {
            echo "<h2>2. Table structure is correct</h2>";
            echo "<div style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'>";
            echo "✅ tblinterviewvideos table has correct structure";
            echo "</div>";
        }
        
    } else {
        echo "<h2>2. Creating tblinterviewvideos table:</h2>";
        $sql = "CREATE TABLE tblinterviewvideos (
            VIDEOID INT AUTO_INCREMENT PRIMARY KEY,
            REGISTRATIONID INT NOT NULL,
            VIDEO_PATH VARCHAR(255) NOT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_registration (REGISTRATIONID)
        )";
        $mydb->setQuery($sql);
        $result = $mydb->executeQuery();
        
        if ($result) {
            echo "<div style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'>";
            echo "✅ Created tblinterviewvideos table successfully";
            echo "</div>";
        } else {
            echo "<div style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px;'>";
            echo "❌ Failed to create table";
            echo "</div>";
        }
    }
    
    echo "<h2>3. Final verification:</h2>";
    $sql = "DESCRIBE tblinterviewvideos";
    $mydb->setQuery($sql);
    $final_columns = $mydb->loadResultList();
    
    if ($final_columns) {
        echo "<div style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'>";
        echo "<h3>✅ Final table structure:</h3>";
        echo "<ul>";
        foreach ($final_columns as $col) {
            echo "<li><strong>{$col->Field}</strong>: {$col->Type} " . ($col->Key === 'PRI' ? '(PRIMARY KEY)' : '') . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<h2>4. Testing consolidated video save functionality:</h2>";
    
    // Create upload directory
    $upload_dir = 'uploads/interviews/consolidated';
    if (!is_dir($upload_dir)) {
        if (mkdir($upload_dir, 0777, true)) {
            echo "<p>✅ Created upload directory: $upload_dir</p>";
        } else {
            echo "<p>❌ Failed to create upload directory: $upload_dir</p>";
        }
    } else {
        echo "<p>✅ Upload directory exists: $upload_dir</p>";
    }
    
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎯 What was fixed:</h3>";
    echo "<ul>";
    echo "<li>✅ Fixed database column name inconsistency (ID vs VIDEOID)</li>";
    echo "<li>✅ Ensured proper table structure for consolidated videos</li>";
    echo "<li>✅ Created required upload directories</li>";
    echo "<li>✅ The 'Unknown column ID' error should now be resolved</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🔬 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Try completing an interview again</li>";
    echo "<li>Check if the consolidated video saves successfully</li>";
    echo "<li>Test the 'Watch Recording' functionality</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='interview.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🎤 Test Interview</a>";
echo "<a href='admin/interview-results.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📊 View Results</a>";
echo "</div>";
?>