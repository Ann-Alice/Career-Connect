<?php
// Fix database schema for interview completion
require_once('include/initialize.php');

echo "<h1>🔧 Database Schema Fix</h1>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
.success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
.error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
.info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
</style>";

try {
    // Check and add missing columns to tbljobregistration
    echo "<h2>Checking tbljobregistration table...</h2>";
    
    // Check if INTERVIEW_STATUS column exists
    $sql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_STATUS'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if (!$result) {
        echo "<div class='info'>Adding INTERVIEW_STATUS column...</div>";
        $sql = "ALTER TABLE tbljobregistration ADD COLUMN INTERVIEW_STATUS VARCHAR(50) DEFAULT NULL";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<div class='success'>✅ INTERVIEW_STATUS column added</div>";
    } else {
        echo "<div class='success'>✅ INTERVIEW_STATUS column already exists</div>";
    }
    
    // Check if INTERVIEW_COMPLETED_AT column exists
    $sql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_COMPLETED_AT'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if (!$result) {
        echo "<div class='info'>Adding INTERVIEW_COMPLETED_AT column...</div>";
        $sql = "ALTER TABLE tbljobregistration ADD COLUMN INTERVIEW_COMPLETED_AT DATETIME DEFAULT NULL";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<div class='success'>✅ INTERVIEW_COMPLETED_AT column added</div>";
    } else {
        echo "<div class='success'>✅ INTERVIEW_COMPLETED_AT column already exists</div>";
    }
    
    // Check if INTERVIEW_RESULTS column exists
    $sql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_RESULTS'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if (!$result) {
        echo "<div class='info'>Adding INTERVIEW_RESULTS column...</div>";
        $sql = "ALTER TABLE tbljobregistration ADD COLUMN INTERVIEW_RESULTS TEXT DEFAULT NULL";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<div class='success'>✅ INTERVIEW_RESULTS column added</div>";
    } else {
        echo "<div class='success'>✅ INTERVIEW_RESULTS column already exists</div>";
    }
    
    // Check tblinterviewinvitations table
    echo "<h2>Checking tblinterviewinvitations table...</h2>";
    
    $sql = "SHOW TABLES LIKE 'tblinterviewinvitations'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if (!$result) {
        echo "<div class='info'>Creating tblinterviewinvitations table...</div>";
        $sql = "CREATE TABLE tblinterviewinvitations (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            REGISTRATIONID INT NOT NULL,
            JOBID INT NOT NULL,
            APPLICANTID INT NOT NULL,
            TOKEN VARCHAR(255) NOT NULL UNIQUE,
            EXPIRY_DATE DATETIME NOT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            USED_AT DATETIME DEFAULT NULL,
            INDEX idx_token (TOKEN),
            INDEX idx_registration (REGISTRATIONID),
            INDEX idx_expiry (EXPIRY_DATE)
        )";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<div class='success'>✅ tblinterviewinvitations table created</div>";
    } else {
        echo "<div class='success'>✅ tblinterviewinvitations table already exists</div>";
    }
    
    // Check tblinterviewrecordings table
    echo "<h2>Checking tblinterviewrecordings table...</h2>";
    
    $sql = "SHOW TABLES LIKE 'tblinterviewrecordings'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if (!$result) {
        echo "<div class='info'>Creating tblinterviewrecordings table...</div>";
        $sql = "CREATE TABLE tblinterviewrecordings (
            RECORDINGID INT AUTO_INCREMENT PRIMARY KEY,
            REGISTRATIONID INT NOT NULL,
            QUESTION_NUMBER INT NOT NULL,
            CONVERSATION_TURN INT DEFAULT 1,
            FILE_PATH VARCHAR(500) NOT NULL,
            DURATION FLOAT DEFAULT 0,
            TRANSCRIPT TEXT,
            RECORDED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_registration (REGISTRATIONID),
            INDEX idx_question (QUESTION_NUMBER)
        )";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<div class='success'>✅ tblinterviewrecordings table created</div>";
    } else {
        echo "<div class='success'>✅ tblinterviewrecordings table already exists</div>";
        
        // Check if TRANSCRIPT column exists
        $sql = "SHOW COLUMNS FROM tblinterviewrecordings LIKE 'TRANSCRIPT'";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        
        if (!$result) {
            echo "<div class='info'>Adding TRANSCRIPT column to tblinterviewrecordings...</div>";
            $sql = "ALTER TABLE tblinterviewrecordings ADD COLUMN TRANSCRIPT TEXT DEFAULT NULL";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
            echo "<div class='success'>✅ TRANSCRIPT column added</div>";
        } else {
            echo "<div class='success'>✅ TRANSCRIPT column already exists</div>";
        }
    }
    
    // Check tblinterviewvideos table
    echo "<h2>Checking tblinterviewvideos table...</h2>";
    
    $sql = "SHOW TABLES LIKE 'tblinterviewvideos'";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if (!$result) {
        echo "<div class='info'>Creating tblinterviewvideos table...</div>";
        $sql = "CREATE TABLE tblinterviewvideos (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            REGISTRATIONID INT NOT NULL,
            VIDEO_PATH VARCHAR(500) NOT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_registration (REGISTRATIONID)
        )";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
        echo "<div class='success'>✅ tblinterviewvideos table created</div>";
    } else {
        echo "<div class='success'>✅ tblinterviewvideos table already exists</div>";
    }
    
    echo "<div class='success'>";
    echo "<h2>🎉 Database Schema Fix Complete!</h2>";
    echo "<p>All required tables and columns have been verified and created as needed.</p>";
    echo "<p>The HTTP 500 error in interview completion should now be resolved.</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin-top: 30px;'>";
    echo "<a href='interview.php?token=test' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Test Interview</a>";
    echo "<a href='admin/interview-results.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>View Results</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='error'>Please check your database connection and permissions.</div>";
}
?>