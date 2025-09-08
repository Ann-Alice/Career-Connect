<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

echo "<h2>File Path Repair Tool</h2>";
echo "<p>This tool will check and fix file paths in the database.</p>";

$fix_mode = isset($_GET['fix']) && $_GET['fix'] == '1';

if ($fix_mode) {
    echo "<h3>FIXING MODE</h3>";
} else {
    echo "<h3>ANALYSIS MODE</h3>";
    echo "<p><a href='?fix=1' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Fix Mode</a></p>";
}

// Get all attachment files
$sql = "SELECT * FROM tblattachmentfile WHERE FILE_NAME = 'Resume' ORDER BY ID DESC";
$mydb->setQuery($sql);
$attachments = $mydb->loadResultList();

$fixed_count = 0;
$total_count = 0;

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>ID</th><th>Current Location</th><th>Direct Path</th><th>Photos Path</th><th>Status</th><th>Action</th>";
echo "</tr>";

if ($attachments) {
    foreach ($attachments as $att) {
        $total_count++;
        $current_location = $att->FILE_LOCATION;
        $direct_path = "../../applicant/" . $current_location;
        $photos_path = "../../applicant/photos/" . $current_location;
        
        $direct_exists = file_exists($direct_path);
        $photos_exists = file_exists($photos_path);
        
        echo "<tr>";
        echo "<td>{$att->ID}</td>";
        echo "<td>{$current_location}</td>";
        echo "<td>" . ($direct_exists ? "✓ Found" : "✗ Missing") . "</td>";
        echo "<td>" . ($photos_exists ? "✓ Found" : "✗ Missing") . "</td>";
        
        if ($direct_exists && $photos_exists) {
            echo "<td style='color: orange;'>⚠️ Duplicate</td>";
            echo "<td>File exists in both locations</td>";
        } elseif ($direct_exists) {
            echo "<td style='color: green;'>✓ OK</td>";
            echo "<td>File found at current location</td>";
        } elseif ($photos_exists && !strpos($current_location, 'photos/') === 0) {
            echo "<td style='color: red;'>❌ Needs Fix</td>";
            if ($fix_mode) {
                // Fix the database entry
                $new_location = 'photos/' . $current_location;
                $update_sql = "UPDATE tblattachmentfile SET FILE_LOCATION = '" . addslashes($new_location) . "' WHERE ID = " . $att->ID;
                $mydb->setQuery($update_sql);
                $result = $mydb->executeQuery();
                
                if ($result) {
                    echo "<td style='color: green;'>🔧 FIXED: Updated to photos/{$current_location}</td>";
                    $fixed_count++;
                } else {
                    echo "<td style='color: red;'>❌ Fix failed</td>";
                }
            } else {
                echo "<td>Would update to: photos/{$current_location}</td>";
            }
        } else {
            echo "<td style='color: red;'>❌ Missing</td>";
            echo "<td>File not found in either location</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No resume files found</td></tr>";
}

echo "</table>";

if ($fix_mode) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>Fix Results:</h4>";
    echo "<p>✅ Fixed {$fixed_count} out of {$total_count} records</p>";
    echo "</div>";
    
    echo "<p><a href='?' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Back to Analysis</a></p>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>Analysis Complete:</h4>";
    echo "<p>📊 Found {$total_count} resume records in database</p>";
    echo "<p>⚠️ Some files may need path correction</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='../dashboard.php'>← Back to Dashboard</a></p>";
?>