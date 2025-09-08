<!DOCTYPE html>
<html>
<head>
    <title>Navigation Guide - Interview Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .step { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 10px; border-left: 4px solid #667eea; }
        .step h3 { color: #667eea; margin-top: 0; }
        .code { background: #e9ecef; padding: 10px; border-radius: 5px; font-family: monospace; }
        .link { color: #667eea; text-decoration: none; font-weight: bold; }
        .link:hover { text-decoration: underline; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🚀 How to Access the Interview Results Page</h1>
    
    <div class="step">
        <h3>Step 1: Check Database Setup</h3>
        <p>First, let's make sure your database has the required tables and data:</p>
        <a href="test-db.php" class="link">🔍 Click here to test database connection</a>
    </div>
    
    <div class="step">
        <h3>Step 2: Create Reviews Table (if needed)</h3>
        <p>If the reviews table doesn't exist, you'll need to create it:</p>
        <div class="code">
            -- Run this SQL in your database:<br>
            CREATE TABLE IF NOT EXISTS `tblinterviewreviews` (<br>
            &nbsp;&nbsp;`REVIEWID` int(11) NOT NULL AUTO_INCREMENT,<br>
            &nbsp;&nbsp;`REGISTRATIONID` int(11) NOT NULL,<br>
            &nbsp;&nbsp;`CANDIDATE_NAME` varchar(255) NOT NULL,<br>
            &nbsp;&nbsp;`RATING` int(1) NOT NULL DEFAULT 5,<br>
            &nbsp;&nbsp;`EXPERIENCE_RATING` int(1) NOT NULL DEFAULT 5,<br>
            &nbsp;&nbsp;`DIFFICULTY_RATING` int(1) NOT NULL DEFAULT 3,<br>
            &nbsp;&nbsp;`COMMENTS` text,<br>
            &nbsp;&nbsp;`CREATED_AT` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,<br>
            &nbsp;&nbsp;PRIMARY KEY (`REVIEWID`)<br>
            );
        </div>
    </div>
    
    <div class="step">
        <h3>Step 3: Access the Interview Results Page</h3>
        <p>Navigate to the interview results page using one of these methods:</p>
        <ul>
            <li><strong>Direct URL:</strong> <a href="interview-results.php" class="link">interview-results.php</a></li>
            <li><strong>From Admin Panel:</strong> Look for "Interview Results" in the navigation menu</li>
            <li><strong>Manual URL:</strong> <span class="code">http://localhost/eris/admin/interview-results.php</span></li>
        </ul>
    </div>
    
    <div class="step">
        <h3>Step 4: Test Individual Features</h3>
        <p>Test the individual components:</p>
        <ul>
            <li><a href="add-interview-review.php" class="link">➕ Add Interview Review</a></li>
            <li><a href="debug-videos.php" class="link">🎥 Debug Video Files</a></li>
            <li><a href="test-access.php" class="link">🔧 Test Admin Access</a></li>
        </ul>
    </div>
    
    <div class="warning">
        <h3>⚠️ Troubleshooting</h3>
        <p><strong>If you can't see the page:</strong></p>
        <ul>
            <li>Make sure you're logged in as an admin</li>
            <li>Check that XAMPP is running (Apache + MySQL)</li>
            <li>Verify the file path is correct</li>
            <li>Check browser console for JavaScript errors</li>
            <li>Look for PHP errors in the XAMPP error logs</li>
        </ul>
    </div>
    
    <div class="step">
        <h3>Step 5: Expected Features</h3>
        <p>Once you access the page, you should see:</p>
        <ul>
            <li>📊 Statistics overview (total candidates, completed interviews, etc.)</li>
            <li>🔍 Filter options (by grade, position, date)</li>
            <li>⭐ Candidate reviews section</li>
            <li>📋 Graded interview results with A-F grades</li>
            <li>🎥 Video watch/download buttons</li>
            <li>📤 Export and print functionality</li>
        </ul>
    </div>
    
    <div class="step">
        <h3>Step 6: Sample Data</h3>
        <p>If you don't see any results, it might be because:</p>
        <ul>
            <li>No interviews have been completed yet</li>
            <li>The interview status is not set to 'Completed'</li>
            <li>No interview results data exists</li>
        </ul>
        <p>You can add sample data by completing some AI interviews first.</p>
    </div>
    
    <hr>
    <p><strong>Need help?</strong> Check the database test results first, then try accessing the main page.</p>
</body>
</html> 