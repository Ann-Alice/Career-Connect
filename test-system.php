<?php
// Test script to check if the interview system is working
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Interview System Status Check</h1>";

// Test 1: Check if required files exist
echo "<h2>1. File System Check</h2>";
$required_files = [
    'interview.php',
    'upload-interview.php',
    'theme/js/ai-interview.js',
    'theme/css/ai-interview.css',
    'include/database.php',
    'include/config.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 2: Check database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    require_once('include/config.php');
    require_once('include/database.php');
    
    if (isset($mydb) && $mydb) {
        echo "✅ Database connection successful<br>";
        
        // Test if interview tables exist
        $tables = ['tblinterviewinvitations', 'tblinterviewrecordings', 'tbljobregistration'];
        foreach ($tables as $table) {
            $sql = "SHOW TABLES LIKE '$table'";
            $mydb->setQuery($sql);
            $result = $mydb->loadSingleResult();
            if ($result) {
                echo "✅ Table $table exists<br>";
            } else {
                echo "❌ Table $table missing<br>";
            }
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Check upload directory
echo "<h2>3. Upload Directory Test</h2>";
$upload_dir = 'uploads/interviews';
if (file_exists($upload_dir)) {
    echo "✅ Upload directory exists<br>";
    if (is_writable($upload_dir)) {
        echo "✅ Upload directory is writable<br>";
    } else {
        echo "❌ Upload directory is not writable<br>";
    }
} else {
    echo "❌ Upload directory missing<br>";
}

// Test 4: Check if there are any active interview invitations
echo "<h2>4. Active Interview Invitations</h2>";
try {
    if (isset($mydb)) {
        $sql = "SELECT COUNT(*) as count FROM tblinterviewinvitations WHERE EXPIRY_DATE > NOW()";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        if ($result) {
            echo "✅ Found " . $result->count . " active interview invitations<br>";
            
            if ($result->count > 0) {
                // Show sample invitation
                $sql = "SELECT i.*, r.APPLICANT, j.OCCUPATIONTITLE 
                        FROM tblinterviewinvitations i 
                        JOIN tbljobregistration r ON i.REGISTRATIONID = r.REGISTRATIONID 
                        JOIN tbljob j ON i.JOBID = j.JOBID 
                        WHERE i.EXPIRY_DATE > NOW() 
                        LIMIT 1";
                $mydb->setQuery($sql);
                $invitation = $mydb->loadSingleResult();
                if ($invitation) {
                    echo "Sample invitation: " . $invitation->APPLICANT . " for " . $invitation->OCCUPATIONTITLE . "<br>";
                    echo "Token: " . substr($invitation->TOKEN, 0, 10) . "...<br>";
                }
            }
        } else {
            echo "❌ No active interview invitations found<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking invitations: " . $e->getMessage() . "<br>";
}

// Test 5: Check recent recordings
echo "<h2>5. Recent Recordings</h2>";
try {
    if (isset($mydb)) {
        $sql = "SELECT COUNT(*) as count FROM tblinterviewrecordings";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        if ($result) {
            echo "✅ Found " . $result->count . " total recordings<br>";
            
            if ($result->count > 0) {
                // Show recent recordings
                $sql = "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5";
                $mydb->setQuery($sql);
                $recordings = $mydb->loadResultList();
                foreach ($recordings as $recording) {
                    echo "Recording: " . $recording->FILE_PATH . " (Duration: " . $recording->DURATION . "s)<br>";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking recordings: " . $e->getMessage() . "<br>";
}

// Test 6: Test upload endpoint
echo "<h2>6. Upload Endpoint Test</h2>";
$test_url = "http://localhost/eris/upload-interview.php?test=1";
$response = @file_get_contents($test_url);
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "✅ Upload endpoint is responding correctly<br>";
    } else {
        echo "❌ Upload endpoint returned error: " . $response . "<br>";
    }
} else {
    echo "❌ Upload endpoint not accessible<br>";
}

// Test 7: Check Apache and PHP
echo "<h2>7. Server Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Directory: " . getcwd() . "<br>";

// Test 8: Check file permissions
echo "<h2>8. File Permissions</h2>";
$test_files = ['interview.php', 'upload-interview.php'];
foreach ($test_files as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "✅ $file is readable<br>";
        } else {
            echo "❌ $file is not readable<br>";
        }
    }
}

echo "<h2>System Status Summary</h2>";
echo "<p>If you see mostly ✅ marks above, your interview system should be working properly.</p>";
echo "<p>To test the full interview flow:</p>";
echo "<ol>";
echo "<li>Create an interview invitation in the admin panel</li>";
echo "<li>Use the interview link with the token</li>";
echo "<li>Allow camera and microphone permissions</li>";
echo "<li>Start the interview and test recording</li>";
echo "</ol>";
?> 