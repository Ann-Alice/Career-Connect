<?php
// Test script to identify the issue with interview completion
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Interview Completion Diagnostics</h1>";

// Test 1: Check database connection
echo "<h2>1. Database Connection Test</h2>";
try {
    require_once('include/initialize.php');
    echo "✅ Successfully loaded initialize.php<br>";
    
    if (isset($mydb)) {
        echo "✅ Database object exists<br>";
        
        // Test a simple query
        $sql = "SELECT COUNT(*) as count FROM tbljobregistration";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        echo "✅ Database connection working - found " . $result->count . " registrations<br>";
    } else {
        echo "❌ Database object not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
}

// Test 2: Check email functions
echo "<h2>2. Email Functions Test</h2>";
try {
    require_once('include/email_functions.php');
    echo "✅ Successfully loaded email_functions.php<br>";
    
    // Check if email constants are defined
    $constants = ['ADMIN_EMAIL', 'SMTP_HOST', 'SMTP_USER', 'SMTP_PASS', 'SMTP_PORT'];
    foreach ($constants as $const) {
        if (defined($const)) {
            echo "✅ $const is defined<br>";
        } else {
            echo "❌ $const is not defined<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Email functions error: " . $e->getMessage() . "<br>";
}

// Test 3: Simulate complete-interview.php logic with debugging
echo "<h2>3. Complete Interview Logic Test</h2>";

// Check if we have any registrations to work with
$sql = "SELECT REGISTRATIONID FROM tbljobregistration LIMIT 1";
$mydb->setQuery($sql);
$test_registration = $mydb->loadSingleResult();

if ($test_registration) {
    $registrationId = $test_registration->REGISTRATIONID;
    echo "✅ Found test registration ID: $registrationId<br>";
    
    // Test the UPDATE query that's causing issues
    echo "<h3>Testing UPDATE Query</h3>";
    try {
        $sql = "UPDATE tbljobregistration 
                SET INTERVIEW_STATUS = 'Completed', 
                    INTERVIEW_COMPLETED_AT = NOW() 
                WHERE REGISTRATIONID = '{$registrationId}'";
        echo "SQL Query: <code>$sql</code><br>";
        
        $mydb->setQuery($sql);
        $updateResult = $mydb->executeQuery();
        
        if ($updateResult) {
            echo "✅ UPDATE query executed successfully<br>";
        } else {
            echo "❌ UPDATE query failed<br>";
        }
    } catch (Exception $e) {
        echo "❌ UPDATE query error: " . $e->getMessage() . "<br>";
    }
    
    // Test JSON encoding
    echo "<h3>Testing JSON Encoding</h3>";
    try {
        $interviewResults = array(
            'overall_score' => 85,
            'total_questions_answered' => 5,
            'interview_duration' => 300,
            'candidate_feedback' => array(
                'rating' => 5,
                'experience_rating' => 4,
                'difficulty_rating' => 3,
                'comments' => 'Test feedback'
            ),
            'selected_voice' => 'female',
            'completed_at' => date('Y-m-d H:i:s')
        );
        
        $resultsJson = json_encode($interviewResults, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        if ($resultsJson === false) {
            echo "❌ JSON encoding failed: " . json_last_error_msg() . "<br>";
        } else {
            echo "✅ JSON encoding successful<br>";
            echo "JSON Length: " . strlen($resultsJson) . " characters<br>";
        }
    } catch (Exception $e) {
        echo "❌ JSON encoding error: " . $e->getMessage() . "<br>";
    }
    
} else {
    echo "❌ No registrations found for testing<br>";
}

// Test 4: Check table structure
echo "<h2>4. Table Structure Test</h2>";
try {
    // Check if INTERVIEW_STATUS and INTERVIEW_COMPLETED_AT columns exist
    $sql = "DESCRIBE tbljobregistration";
    $mydb->setQuery($sql);
    $columns = $mydb->loadResultList();
    
    $required_columns = ['INTERVIEW_STATUS', 'INTERVIEW_COMPLETED_AT', 'INTERVIEW_RESULTS'];
    $existing_columns = array_map(function($col) { return $col->Field; }, $columns);
    
    foreach ($required_columns as $col) {
        if (in_array($col, $existing_columns)) {
            echo "✅ Column '$col' exists<br>";
        } else {
            echo "❌ Column '$col' is missing<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Table structure error: " . $e->getMessage() . "<br>";
}

// Test 5: Check for potential issues with escaping
echo "<h2>5. SQL Injection Prevention Test</h2>";
try {
    if (method_exists($mydb, 'escape_string')) {
        echo "✅ escape_string method exists<br>";
        $test_string = "Test's \"string\" with special chars";
        $escaped = $mydb->escape_string($test_string);
        echo "✅ String escaping works: '$escaped'<br>";
    } else {
        echo "❌ escape_string method not found<br>";
    }
} catch (Exception $e) {
    echo "❌ SQL escaping error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Summary & Next Steps</h2>";
echo "<p>This diagnostic will help identify the root cause of the HTTP 500 error.</p>";
echo "<p>Check the results above and look for any ❌ errors that need to be fixed.</p>";
?>