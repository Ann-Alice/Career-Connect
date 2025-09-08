<?php
require_once("include/initialize.php");

echo "<h2>Job Registration Debug Tool</h2>";

// Check if admin is logged in
if(!isset($_SESSION['ADMIN_USERID'])){
    echo "<p><a href='admin/login.php'>Please log in as admin first</a></p>";
    exit;
}

// Check database connection
if (!isset($mydb)) {
    echo "<p style='color: red;'>❌ Database connection not available</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection available</p>";
}

echo "<hr>";

// 1. Check if job registration table exists
echo "<h3>1. Check tbljobregistration table</h3>";
try {
    $mydb->setQuery("SHOW TABLES LIKE 'tbljobregistration'");
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "<p style='color: green;'>✅ tbljobregistration table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ tbljobregistration table does not exist</p>";
        echo "<p><a href='setup-optimized-database.php'>Setup database</a></p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking table: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Check table structure
echo "<h3>2. Table Structure</h3>";
try {
    $mydb->setQuery("DESCRIBE tbljobregistration");
    $columns = $mydb->loadResultList();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
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
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking table structure: " . $e->getMessage() . "</p>";
}

// 3. Count total job registrations
echo "<h3>3. Job Registration Count</h3>";
try {
    $mydb->setQuery("SELECT COUNT(*) as count FROM tbljobregistration");
    $count = $mydb->loadSingleResult();
    echo "<p>Total job registrations: <strong>" . $count->count . "</strong></p>";
    
    if ($count->count == 0) {
        echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
        echo "<h4>⚠️ No Job Registrations Found</h4>";
        echo "<p>The database has no job registrations. This is why you're getting the 'Job registration not found' error.</p>";
        echo "<p><strong>Solutions:</strong></p>";
        echo "<ul>";
        echo "<li><a href='setup-optimized-database.php'>Run database setup</a> to create sample data</li>";
        echo "<li>Or create a job application through the applicant portal</li>";
        echo "</ul>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error counting registrations: " . $e->getMessage() . "</p>";
}

// 4. Show existing job registrations (if any)
echo "<h3>4. Existing Job Registrations</h3>";
try {
    $mydb->setQuery("SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE, c.COMPANYNAME 
                     FROM tbljobregistration r 
                     LEFT JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                     LEFT JOIN tbljob j ON r.JOBID = j.JOBID 
                     LEFT JOIN tblcompany c ON r.COMPANYID = c.COMPANYID 
                     ORDER BY r.REGISTRATIONID DESC 
                     LIMIT 10");
    $registrations = $mydb->loadResultList();
    
    if ($registrations && count($registrations) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Applicant</th><th>Job Title</th><th>Company</th><th>Date</th><th>Status</th><th>Action</th></tr>";
        foreach ($registrations as $reg) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($reg->REGISTRATIONID) . "</td>";
            echo "<td>" . htmlspecialchars(($reg->FNAME ?? '') . ' ' . ($reg->LNAME ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars($reg->OCCUPATIONTITLE ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($reg->COMPANYNAME ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($reg->REGISTRATIONDATE ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($reg->REMARKS ?? 'Pending') . "</td>";
            echo "<td><a href='admin/applicants/view.php?id=" . $reg->REGISTRATIONID . "' target='_blank'>View</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No job registrations found in database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fetching registrations: " . $e->getMessage() . "</p>";
}

// 5. Check related tables
echo "<h3>5. Related Tables Status</h3>";
$tables_to_check = ['tblapplicants', 'tbljob', 'tblcompany', 'tblcategory'];

foreach ($tables_to_check as $table) {
    try {
        $mydb->setQuery("SELECT COUNT(*) as count FROM $table");
        $count = $mydb->loadSingleResult();
        echo "<p>$table: <strong>" . $count->count . "</strong> records</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error checking $table: " . $e->getMessage() . "</p>";
    }
}

// 6. Create sample data button
echo "<hr>";
echo "<h3>6. Quick Fix Options</h3>";
echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<h4>🔧 Quick Fixes</h4>";
echo "<p><a href='setup-optimized-database.php' class='btn' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Setup Complete Database</a> - Creates all tables with sample data</p>";
echo "<p><a href='admin/applicants/' class='btn' style='background: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Go to Applicants Page</a> - Check the applicants list</p>";
echo "<p><a href='admin/' class='btn' style='background: #6c757d; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Admin Dashboard</a> - Return to admin panel</p>";
echo "</div>";

echo "<hr>";
echo "<p><small>Debug completed at " . date('Y-m-d H:i:s') . "</small></p>";
?>