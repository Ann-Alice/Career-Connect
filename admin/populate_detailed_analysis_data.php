<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    die("Access denied. Please log in as administrator.");
}

echo "<h2>Populating Detailed AI Analysis Data</h2>";

// Sample detailed analysis data
$sampleAnalysisData = [
    'overall_score' => 85,
    'speech_analysis' => [
        'clarity_score' => 88,
        'confidence' => 82,
        'pace_score' => 85,
        'speech_assessment' => 'Excellent articulation with confident delivery. Candidate spoke at an appropriate pace with clear pronunciation.'
    ],
    'facial_analysis' => [
        'confidence_score' => 80,
        'eye_contact_score' => 85,
        'expression_balance' => 83,
        'expression_summary' => 'Candidate maintained good eye contact throughout the interview and displayed appropriate facial expressions that matched the context of their responses.'
    ],
    'movement_analysis' => [
        'posture_score' => 87,
        'gesture_score' => 78,
        'natural_movement' => 82,
        'assessment' => 'Professional posture with natural hand gestures that complemented their speech. Candidate appeared comfortable and engaged.'
    ],
    'answer_analysis' => [
        'relevance_score' => 90,
        'coherence_score' => 88,
        'completeness_score' => 86,
        'answer_assessment' => 'Well-structured responses that directly addressed the questions. Candidate provided comprehensive answers with relevant examples.'
    ],
    'performance_feedback' => [
        'Strong overall performance with excellent communication skills',
        'Professional demeanor and confident presentation'
    ]
];

// Convert to JSON
$jsonData = json_encode($sampleAnalysisData);

// Get all completed interviews that don't have detailed analysis data
$sql = "SELECT REGISTRATIONID, INTERVIEW_RESULTS 
        FROM tbljobregistration 
        WHERE INTERVIEW_STATUS = 'Completed' 
        AND (INTERVIEW_RESULTS IS NULL OR INTERVIEW_RESULTS = '' OR INTERVIEW_RESULTS NOT LIKE '%speech_analysis%')
        LIMIT 5";

$mydb->setQuery($sql);
$interviews = $mydb->loadResultList();

if ($interviews) {
    echo "<p>Found " . count($interviews) . " interviews to update.</p>";
    
    $updatedCount = 0;
    foreach ($interviews as $interview) {
        // Update the interview record with detailed analysis data
        $updateSql = "UPDATE tbljobregistration 
                      SET INTERVIEW_RESULTS = '" . $mydb->escape_string($jsonData) . "',
                          INTERVIEW_COMPLETED_AT = NOW()
                      WHERE REGISTRATIONID = " . $interview->REGISTRATIONID;
        
        $mydb->setQuery($updateSql);
        if ($mydb->executeQuery()) {
            echo "<p>✓ Updated interview ID: " . $interview->REGISTRATIONID . "</p>";
            $updatedCount++;
        } else {
            echo "<p>✗ Failed to update interview ID: " . $interview->REGISTRATIONID . "</p>";
        }
    }
    
    echo "<h3>Completed!</h3>";
    echo "<p>Successfully updated " . $updatedCount . " interviews with detailed AI analysis data.</p>";
    echo "<p>You can now view the detailed breakdown in the <a href='interview-results.php'>Interview Results</a> section.</p>";
} else {
    // If no interviews found, let's create one sample record for testing
    echo "<p>No completed interviews found without detailed analysis data.</p>";
    
    // Try to find any interview record to update
    $sql = "SELECT REGISTRATIONID FROM tbljobregistration WHERE INTERVIEW_STATUS = 'Completed' LIMIT 1";
    $mydb->setQuery($sql);
    $interview = $mydb->loadSingleResult();
    
    if ($interview) {
        $updateSql = "UPDATE tbljobregistration 
                      SET INTERVIEW_RESULTS = '" . $mydb->escape_string($jsonData) . "',
                          INTERVIEW_COMPLETED_AT = NOW()
                      WHERE REGISTRATIONID = " . $interview->REGISTRATIONID;
        
        $mydb->setQuery($updateSql);
        if ($mydb->executeQuery()) {
            echo "<p>✓ Updated interview ID: " . $interview->REGISTRATIONID . " with detailed analysis data.</p>";
            echo "<p>You can now view the detailed breakdown in the <a href='interview-results.php'>Interview Results</a> section.</p>";
        } else {
            echo "<p>✗ Failed to update interview ID: " . $interview->REGISTRATIONID . "</p>";
        }
    } else {
        echo "<p>No completed interviews found in the database. Please complete an interview first.</p>";
        echo "<p>You can:</p>";
        echo "<ol>";
        echo "<li>Complete an AI interview through the applicant portal</li>";
        echo "<li>Set an interview status to 'Completed' manually</li>";
        echo "<li>Or run the <a href='populate_sample_interview_data.php'>sample data population script</a></li>";
        echo "</ol>";
    }
}

echo "<h3>Sample Data Structure:</h3>";
echo "<pre>";
print_r($sampleAnalysisData);
echo "</pre>";

echo "<p><a href='interview-results.php'>← Back to Interview Results</a></p>";
?>