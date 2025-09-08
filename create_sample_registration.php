<?php
require_once("include/initialize.php");

// Check if admin is logged in
if(!isset($_SESSION['ADMIN_USERID'])){
    echo "<p><a href='admin/login.php'>Please log in as admin first</a></p>";
    exit;
}

echo "<h2>Create Sample Job Registration</h2>";
echo "<p>This will create a sample job registration that you can use to test the view functionality.</p>";

if (isset($_POST['create_sample'])) {
    try {
        // Check if we have the required tables and data
        $mydb->setQuery("SELECT COUNT(*) as count FROM tblcompany");
        $company_count = $mydb->loadSingleResult();
        
        $mydb->setQuery("SELECT COUNT(*) as count FROM tbljob");
        $job_count = $mydb->loadSingleResult();
        
        $mydb->setQuery("SELECT COUNT(*) as count FROM tblapplicants");
        $applicant_count = $mydb->loadSingleResult();
        
        if ($company_count->count == 0 || $job_count->count == 0 || $applicant_count->count == 0) {
            echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
            echo "<h4>❌ Missing Required Data</h4>";
            echo "<p>Companies: {$company_count->count}, Jobs: {$job_count->count}, Applicants: {$applicant_count->count}</p>";
            echo "<p>Please run the <a href='setup-optimized-database.php'>database setup</a> first to create the required sample data.</p>";
            echo "</div>";
        } else {
            // Get first available company, job, and applicant
            $mydb->setQuery("SELECT * FROM tblcompany LIMIT 1");
            $company = $mydb->loadSingleResult();
            
            $mydb->setQuery("SELECT * FROM tbljob LIMIT 1");
            $job = $mydb->loadSingleResult();
            
            $mydb->setQuery("SELECT * FROM tblapplicants LIMIT 1");
            $applicant = $mydb->loadSingleResult();
            
            // Create job registration
            $jobreg = new JobRegistration();
            $jobreg->COMPANYID = $company->COMPANYID;
            $jobreg->JOBID = $job->JOBID;
            $jobreg->APPLICANTID = $applicant->APPLICANTID;
            $jobreg->APPLICANT = $applicant->FNAME . ' ' . $applicant->LNAME;
            $jobreg->REGISTRATIONDATE = date('Y-m-d');
            $jobreg->REMARKS = 'Pending';
            $jobreg->FILEID = '2024001';
            $jobreg->PENDINGAPPLICATION = 1;
            $jobreg->HVIEW = 1;
            $jobreg->DATETIMEAPPROVED = null;
            
            $result = $jobreg->create();
            
            if ($result) {
                // Get the created registration ID
                $mydb->setQuery("SELECT LAST_INSERT_ID() as id");
                $last_id = $mydb->loadSingleResult();
                
                echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
                echo "<h4>✅ Sample Job Registration Created!</h4>";
                echo "<p><strong>Registration ID:</strong> {$last_id->id}</p>";
                echo "<p><strong>Applicant:</strong> {$applicant->FNAME} {$applicant->LNAME}</p>";
                echo "<p><strong>Job:</strong> {$job->OCCUPATIONTITLE}</p>";
                echo "<p><strong>Company:</strong> {$company->COMPANYNAME}</p>";
                echo "<p><a href='admin/applicants/view.php?id={$last_id->id}' target='_blank' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Test View Page</a></p>";
                echo "</div>";
            } else {
                echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
                echo "<h4>❌ Failed to Create Sample Data</h4>";
                echo "<p>There was an error creating the sample job registration.</p>";
                echo "</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<h4>❌ Error</h4>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
} else {
    // Show form
    echo "<form method='POST'>";
    echo "<div style='background: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
    echo "<h4>Create Sample Job Registration</h4>";
    echo "<p>This will create a test job registration using existing data in your database.</p>";
    echo "<button type='submit' name='create_sample' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Create Sample Registration</button>";
    echo "</div>";
    echo "</form>";
}

echo "<hr>";
echo "<h3>Navigation</h3>";
echo "<p><a href='debug_job_registrations.php'>← Back to Debug Tool</a></p>";
echo "<p><a href='admin/applicants/'>Go to Applicants List</a></p>";
echo "<p><a href='admin/'>Admin Dashboard</a></p>";
?>