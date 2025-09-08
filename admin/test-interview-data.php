<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/login.php");
}

// Get interview data to see the actual structure
$sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME,
               JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as ai_score
        FROM tbljobregistration r 
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
        JOIN tbljob j ON r.JOBID = j.JOBID 
        JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
        WHERE (r.INTERVIEW_STATUS = 'Completed' OR r.INTERVIEW_STATUS = 'completed')
        AND r.INTERVIEW_RESULTS IS NOT NULL 
        AND r.INTERVIEW_RESULTS != ''
        AND r.INTERVIEW_RESULTS != 'null'
        ORDER BY r.INTERVIEW_COMPLETED_AT DESC, r.REGISTRATIONDATE DESC
        LIMIT 1";

$mydb->setQuery($sql);
$interview = $mydb->loadSingleResult();

if ($interview) {
    echo "<h2>Interview Data Structure</h2>";
    echo "<pre>";
    print_r($interview);
    echo "</pre>";
    
    echo "<h2>INTERVIEW_RESULTS JSON</h2>";
    if ($interview->INTERVIEW_RESULTS) {
        $results_data = json_decode($interview->INTERVIEW_RESULTS, true);
        echo "<pre>";
        print_r($results_data);
        echo "</pre>";
    } else {
        echo "No INTERVIEW_RESULTS data found";
    }
} else {
    echo "No completed interviews found";
}
?>
/**
 * Test Interview Data
 * Check if there are any interview results in the database
 */

require_once('../include/initialize.php');

// Check if user is admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    die('Access denied. Admin login required.');
}

global $mydb;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Interview Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
<div class='container'>
<h2>Interview Data Test</h2>";

// Check if required columns exist
$check_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'erisdb' 
              AND TABLE_NAME = 'tbljobregistration' 
              AND COLUMN_NAME IN ('INTERVIEW_RESULTS', 'INTERVIEW_STATUS', 'ADMIN_GRADE', 'EMAIL_SENT')";
$mydb->setQuery($check_sql);
$columns = $mydb->loadResultList();

echo "<h3>Required Columns Check:</h3>";
if (count($columns) >= 4) {
    echo "<p class='success'>✅ All required columns exist in tbljobregistration table</p>";
    
    // Check for interview data
    $sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME,
                   JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as ai_score
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
            WHERE (r.INTERVIEW_STATUS = 'Completed' OR r.INTERVIEW_STATUS = 'completed')
            AND r.INTERVIEW_RESULTS IS NOT NULL 
            AND r.INTERVIEW_RESULTS != ''
            AND r.INTERVIEW_RESULTS != 'null'
            ORDER BY r.INTERVIEW_COMPLETED_AT DESC, r.REGISTRATIONDATE DESC
            LIMIT 10";
    
    $mydb->setQuery($sql);
    $interviews = $mydb->loadResultList();
    
    if (!empty($interviews)) {
        echo "<p class='success'>✅ Found " . count($interviews) . " completed interviews</p>";
        echo "<table>
                <tr>
                    <th>Registration ID</th>
                    <th>Candidate</th>
                    <th>Position</th>
                    <th>Company</th>
                    <th>AI Score</th>
                    <th>Admin Grade</th>
                    <th>Email Sent</th>
                </tr>";
        
        foreach ($interviews as $interview) {
            $ai_score = $interview->ai_score ? round($interview->ai_score, 1) : 'N/A';
            $admin_grade = $interview->ADMIN_GRADE ? 'Yes' : 'No';
            $email_sent = $interview->EMAIL_SENT ? 'Yes' : 'No';
            
            echo "<tr>
                    <td>{$interview->REGISTRATIONID}</td>
                    <td>{$interview->FNAME} {$interview->LNAME}</td>
                    <td>{$interview->OCCUPATIONTITLE}</td>
                    <td>{$interview->COMPANYNAME}</td>
                    <td>{$ai_score}%</td>
                    <td>{$admin_grade}</td>
                    <td>{$email_sent}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='info'>ℹ️ No completed interviews found in the database</p>";
        echo "<p>You can:</p>
              <ul>
                <li>Send interview invitations to candidates</li>
                <li>Complete some AI interviews</li>
                <li>Then check back here for results</li>
              </ul>";
    }
} else {
    echo "<p class='error'>❌ Missing required columns in tbljobregistration table</p>";
    echo "<p>Please run the <a href='init-interview-db.php'>database initialization script</a> first.</p>";
}

echo "<p><a href='interview-results.php' class='btn'>View Interview Results</a></p>";
echo "</div></body></html>";

?>