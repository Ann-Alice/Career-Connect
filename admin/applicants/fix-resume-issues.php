<?php
require_once("../../include/initialize.php");

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

$fix_mode = isset($_GET['fix']) && $_GET['fix'] == '1';
$clean_mode = isset($_GET['clean']) && $_GET['clean'] == '1';

echo "<h2>Resume Issues Diagnosis & Fix</h2>";

if (!$fix_mode && !$clean_mode) {
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>Available Actions:</h3>";
    echo "<p><a href='?fix=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🔧 Fix Issues</a></p>";
    echo "<p><a href='?clean=1' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🗑️ Clean Orphaned Records</a></p>";
    echo "</div>";
}

echo "<h3>1. Analyzing Database Issues</h3>";

// Get all attachment files with applicant info
$sql = "SELECT af.*, a.FNAME, a.LNAME, a.APPLICANTID as REAL_APPLICANT_ID
        FROM tblattachmentfile af 
        LEFT JOIN tblapplicants a ON af.USERATTACHMENTID = a.APPLICANTID
        WHERE af.FILE_NAME = 'Resume'
        ORDER BY af.ID DESC";

$mydb->setQuery($sql);
$attachments = $mydb->loadResultList();

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>File ID</th><th>USERATTACHMENTID</th><th>Applicant Found</th><th>File Location</th><th>File Exists</th><th>Issue</th><th>Action</th>";
echo "</tr>";

$issues_found = 0;
$files_missing = 0;
$orphaned_records = 0;

foreach ($attachments as $att) {
    $has_issue = false;
    $issue_description = "";
    $action_taken = "";
    
    // Check if applicant exists
    $applicant_exists = !empty($att->FNAME);
    
    // Check if file exists
    $file_location = $att->FILE_LOCATION;
    $file_path = "../../applicant/" . $file_location;
    
    // Handle photos/ prefix in path
    if (strpos($file_location, 'photos/') === 0) {
        $file_path = "../../applicant/" . $file_location;
    } else {
        // Try applicant-specific directory
        $file_path = "../../applicant/photos/" . $att->USERATTACHMENTID . "/" . basename($file_location);
        
        // If file not found, try common variations
        if (!file_exists($file_path)) {
            $file_variations = [
                basename($file_location),
                $att->FILE_NAME,
                "Resume" . pathinfo($file_location, PATHINFO_EXTENSION)
            ];
            
            foreach ($file_variations as $variation) {
                $variation_path = "../../applicant/photos/" . $att->USERATTACHMENTID . "/" . $variation;
                if (file_exists($variation_path)) {
                    $file_path = $variation_path;
                    // Update database with correct path
                    $update_sql = "UPDATE tblattachmentfile SET FILE_LOCATION = 'photos/" . $att->USERATTACHMENTID . "/" . $variation . "' WHERE ID = " . $att->ID;
                    $mydb->setQuery($update_sql);
                    $mydb->executeQuery();
                    break;
                }
            }
        }
    }
    
    $file_exists = file_exists($file_path);
    
    echo "<tr>";
    echo "<td>{$att->ID}</td>";
    echo "<td>{$att->USERATTACHMENTID}</td>";
    echo "<td>" . ($applicant_exists ? "✓ {$att->FNAME} {$att->LNAME}" : "✗ Not found") . "</td>";
    echo "<td>{$att->FILE_LOCATION}</td>";
    echo "<td>" . ($file_exists ? "✓ Found" : "✗ Missing") . "</td>";
    
    // Identify issues
    if (!$applicant_exists) {
        $has_issue = true;
        $issue_description = "Orphaned record - applicant doesn't exist";
        $orphaned_records++;
        
        if ($clean_mode) {
            // Delete orphaned record
            $delete_sql = "DELETE FROM tblattachmentfile WHERE ID = " . $att->ID;
            $mydb->setQuery($delete_sql);
            $result = $mydb->executeQuery();
            $action_taken = $result ? "🗑️ Deleted orphaned record" : "❌ Failed to delete";
        }
    } elseif (!$file_exists) {
        $has_issue = true;
        $issue_description = "File missing from server";
        $files_missing++;
        
        if ($fix_mode) {
            // For missing files, we'll create a placeholder or remove the record
            $action_taken = "⚠️ File missing - manual intervention needed";
        }
    } else {
        $issue_description = "✓ OK";
    }
    
    if ($has_issue) {
        $issues_found++;
    }
    
    echo "<td>" . ($has_issue ? "<span style='color: red;'>$issue_description</span>" : "<span style='color: green;'>$issue_description</span>") . "</td>";
    echo "<td>$action_taken</td>";
    echo "</tr>";
}

echo "</table>";

// Check for duplicate USERATTACHMENTID entries
$duplicate_sql = "SELECT USERATTACHMENTID, COUNT(*) as count, GROUP_CONCAT(ID) as file_ids
                  FROM tblattachmentfile 
                  WHERE FILE_NAME = 'Resume'
                  GROUP BY USERATTACHMENTID 
                  HAVING COUNT(*) > 1";

$mydb->setQuery($duplicate_sql);
$duplicates = $mydb->loadResultList();

if ($duplicates) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>⚠️ Found applicants with multiple resume files:</h4>";
    foreach ($duplicates as $dup) {
        echo "<p>Applicant ID {$dup->USERATTACHMENTID}: {$dup->count} files (IDs: {$dup->file_ids})</p>";
        
        if ($fix_mode) {
            // Keep only the most recent file for each applicant
            $file_ids = explode(',', $dup->file_ids);
            array_pop($file_ids); // Remove the last (most recent) ID
            
            if (!empty($file_ids)) {
                $delete_ids = implode(',', $file_ids);
                $cleanup_sql = "DELETE FROM tblattachmentfile WHERE ID IN ($delete_ids)";
                $mydb->setQuery($cleanup_sql);
                $cleanup_result = $mydb->executeQuery();
                
                if ($cleanup_result) {
                    echo "<span style='color: green;'> → Cleaned up duplicate files</span>";
                }
            }
        }
    }
    echo "</div>";
}

// Check for the specific problematic file
echo "<h3>2. Specific Issue Analysis: 27052018124027PLATENO FE95483.docx</h3>";

$specific_sql = "SELECT af.*, a.FNAME, a.LNAME FROM tblattachmentfile af 
                 LEFT JOIN tblapplicants a ON af.USERATTACHMENTID = a.APPLICANTID
                 WHERE af.FILE_LOCATION LIKE '%27052018124027PLATENO FE95483.docx%'";

$mydb->setQuery($specific_sql);
$specific_file = $mydb->loadSingleResult();

if ($specific_file) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>Found the problematic file record:</h4>";
    echo "<p><strong>File ID:</strong> {$specific_file->ID}</p>";
    echo "<p><strong>USERATTACHMENTID:</strong> {$specific_file->USERATTACHMENTID}</p>";
    echo "<p><strong>Applicant:</strong> " . ($specific_file->FNAME ? "{$specific_file->FNAME} {$specific_file->LNAME}" : "Not found - ID {$specific_file->USERATTACHMENTID}") . "</p>";
    echo "<p><strong>File Location:</strong> {$specific_file->FILE_LOCATION}</p>";
    
    $specific_file_path = "../../applicant/" . $specific_file->FILE_LOCATION;
    $specific_exists = file_exists($specific_file_path);
    echo "<p><strong>File exists:</strong> " . ($specific_exists ? "✓ Yes" : "✗ No") . "</p>";
    
    if ($fix_mode && !$specific_exists) {
        // Option 1: Delete the problematic record
        echo "<p><strong>Fix Action:</strong></p>";
        echo "<p><a href='?delete_specific={$specific_file->ID}' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Delete This Record</a></p>";
    }
    echo "</div>";
} else {
    echo "<p style='color: green;'>✓ The specific problematic file record was not found (may have been cleaned up)</p>";
}

// Handle specific deletion
if (isset($_GET['delete_specific'])) {
    $delete_id = intval($_GET['delete_specific']);
    $delete_sql = "DELETE FROM tblattachmentfile WHERE ID = $delete_id";
    $mydb->setQuery($delete_sql);
    $result = $mydb->executeQuery();
    
    if ($result) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>✅ Successfully deleted the problematic record</h4>";
        echo "<p>The file record causing the issue has been removed from the database.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>❌ Failed to delete the record</h4>";
        echo "<p>There was an error removing the file record.</p>";
        echo "</div>";
    }
}

// Check for applicant directories
echo "<h3>3. Applicant Directory Check</h3>";
$applicant_dir = "../../applicant/photos/";

// Get all applicants
$applicant_sql = "SELECT APPLICANTID, FNAME, LNAME FROM tblapplicants";
$mydb->setQuery($applicant_sql);
$applicants = $mydb->loadResultList();

// Check each applicant's directory
foreach ($applicants as $applicant) {
    $dir = $applicant_dir . $applicant->APPLICANTID;
    $dir_exists = is_dir($dir);
    $file_count = 0;
    
    if ($dir_exists) {
        $files = scandir($dir);
        $file_count = count($files) - 2; // Subtract . and ..
    }
    
    echo "<div style='margin: 10px 0; padding: 10px; background: " . ($file_count > 0 ? "#e8f5e9" : "#fff3cd") . "; border-radius: 6px;'>";
    echo "<strong>Applicant:</strong> {$applicant->FNAME} {$applicant->LNAME} (ID: {$applicant->APPLICANTID})<br>";
    echo "<strong>Directory:</strong> $dir<br>";
    echo "<strong>Files:</strong> $file_count<br>";
    echo "</div>";
}

// Check for files in root photos directory that should be moved
if (!$clean_mode) {
    echo "<h3>4. Files to Move to Applicant Directories</h3>";
    $root_dir = "../../applicant/photos/";
    $files = scandir($root_dir);
    $moved_files = 0;
    
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        
        $file_path = $root_dir . $file;
        if (is_file($file_path) && !is_numeric($file)) {
            // This file is not in an applicant directory
            $applicant_id = get_applicant_id_for_file($file);
            
            if ($applicant_id) {
                // Move the file to the applicant's directory
                if ($fix_mode) {
                    // Create applicant directory if needed
                    $applicant_dir = $root_dir . $applicant_id . "/";
                    if (!is_dir($applicant_dir)) {
                        mkdir($applicant_dir, 0777, true);
                    }
                    
                    $new_path = $applicant_dir . $file;
                    if (rename($file_path, $new_path)) {
                        // Update database with new path
                        $update_sql = "UPDATE tblattachmentfile SET FILE_LOCATION = 'photos/{$applicant_id}/{$file}' WHERE FILE_LOCATION = 'photos/{$file}'";
                        $mydb->setQuery($update_sql);
                        $mydb->executeQuery();
                        $moved_files++;
                    }
                }
            }
        }
    }
    
    if ($moved_files > 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>✅ Moved $moved_files files to applicant directories</h4>";
        echo "<p>Resume files are now properly organized in applicant-specific directories.</p>";
        echo "</div>";
    } else {
        echo "<p style='color: green;'>✓ All resume files are properly organized in applicant directories</p>";
    }
}

/**
 * Helper function to find which applicant owns a file
 * This would need to be implemented based on how file naming works in your system
 */
function get_applicant_id_for_file($filename) {
    global $mydb;
    
    // Try to find applicant by filename
    // This is a simple implementation - adjust based on your actual file naming
    $sql = "SELECT a.APPLICANTID 
            FROM tblapplicants a
            INNER JOIN tblattachmentfile af ON a.APPLICANTID = af.USERATTACHMENTID
            WHERE af.FILE_LOCATION LIKE '%$filename%'
            LIMIT 1";
    
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    return $result ? $result->APPLICANTID : null;
}

// Check for files in root photos directory that should be moved
$root_dir = "../../applicant/photos/";
$files = scandir($root_dir);
$non_applicant_files = 0;

foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    
    $file_path = $root_dir . $file;
    if (is_file($file_path) && !is_numeric($file)) {
        // This file is in the root photos directory but is not in an applicant directory
        $non_applicant_files++;
    }
}

// Show summary
$summary_color = $issues_found > 0 || $files_missing > 0 || $orphaned_records > 0 || $non_applicant_files > 0 ? "#fff3cd" : "#d4edda";
echo "<h3>5. Summary</h3>";
echo "<div style='background: $summary_color; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
echo "<p><strong>Total records analyzed:</strong> " . count($attachments) . "</p>";
echo "<p><strong>Issues found:</strong> $issues_found</p>";
echo "<p><strong>Missing files:</strong> $files_missing</p>";
echo "<p><strong>Orphaned records:</strong> $orphaned_records</p>";
echo "<p><strong>Files in root directory:</strong> $non_applicant_files</p>";
echo "</div>";

// Show fix button
if ($issues_found > 0 || $non_applicant_files > 0) {
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='?fix=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🔧 Fix Issues</a>";
    echo "<a href='?clean=1' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ Clean Orphaned Records</a>";
    echo "</div>";
}

// Show link to view resume
if (isset($_GET['id'])) {
    $view_id = intval($_GET['id']);
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='view-resume.php?id=$view_id' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>→ View Resume</a>";
    echo "</div>";
}

?>