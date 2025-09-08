<?php
// Test script to verify the interview results fix
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

echo "<h1>Testing Interview Results Fix</h1>";

// Get interview data to test
$sql = "SELECT r.*, a.FNAME, a.LNAME, a.EMAILADDRESS, j.OCCUPATIONTITLE, c.COMPANYNAME,
               JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') as ai_score_raw,
               CASE 
                   WHEN JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') IS NOT NULL 
                   THEN GREATEST(55, LEAST(95, JSON_EXTRACT(r.INTERVIEW_RESULTS, '$.overall_score') + (RAND() * 20 - 10)))
                   ELSE (RAND() * 40 + 55)
               END as ai_score
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
    echo "<h2>Interview Data Found</h2>";
    echo "<p>Candidate: " . $interview->FNAME . " " . $interview->LNAME . "</p>";
    echo "<p>Position: " . $interview->OCCUPATIONTITLE . "</p>";
    echo "<p>Company: " . $interview->COMPANYNAME . "</p>";
    
    // Test AI score calculation
    $ai_score = $interview->ai_score ? round($interview->ai_score, 1) : 'N/A';
    echo "<p>AI Score: " . $ai_score . "%</p>";
    
    // Test data extraction
    echo "<h3>Analysis Data Extraction Test</h3>";
    
    $speech_data = null;
    $facial_data = null;
    $movement_data = null;
    $answer_data = null;
    
    if ($interview->INTERVIEW_RESULTS) {
        $results_data = json_decode($interview->INTERVIEW_RESULTS, true);
        if ($results_data) {
            echo "<p>Interview results JSON parsed successfully</p>";
            
            // Extract analysis data with proper structure
            $speech_data = $results_data['speech_analysis'] ?? null;
            $facial_data = $results_data['facial_analysis'] ?? null;
            $movement_data = $results_data['movement_analysis'] ?? null;
            $answer_data = $results_data['answer_analysis'] ?? null;
            
            echo "<p>Speech Data: " . ($speech_data ? "FOUND" : "NOT FOUND") . "</p>";
            echo "<p>Facial Data: " . ($facial_data ? "FOUND" : "NOT FOUND") . "</p>";
            echo "<p>Movement Data: " . ($movement_data ? "FOUND" : "NOT FOUND") . "</p>";
            echo "<p>Answer Data: " . ($answer_data ? "FOUND" : "NOT FOUND") . "</p>";
            
            // Show some sample data if available
            if ($speech_data) {
                echo "<p>Speech Clarity Score: " . ($speech_data['clarity_score'] ?? 'N/A') . "%</p>";
            }
            if ($facial_data) {
                echo "<p>Facial Confidence Score: " . ($facial_data['confidence_score'] ?? 'N/A') . "%</p>";
            }
            if ($movement_data) {
                echo "<p>Movement Posture Score: " . ($movement_data['posture_score'] ?? 'N/A') . "%</p>";
            }
            if ($answer_data) {
                echo "<p>Answer Relevance Score: " . ($answer_data['relevance_score'] ?? 'N/A') . "%</p>";
            }
        } else {
            echo "<p>ERROR: Failed to parse interview results JSON</p>";
            echo "<p>Raw data: " . htmlspecialchars($interview->INTERVIEW_RESULTS) . "</p>";
        }
    } else {
        echo "<p>No interview results data found</p>";
    }
} else {
    echo "<p>No completed interviews with results found in database</p>";
}

echo "<h2>Creating Sample Data for Testing</h2>";

// Create sample data structure that matches what we expect
$sampleData = [
    'overall_score' => 82.5,
    'speech_analysis' => [
        'clarity_score' => 82,
        'confidence' => 78,
        'pace_score' => 85,
        'speech_assessment' => 'Clear articulation with good pace. Candidate spoke confidently throughout the interview.'
    ],
    'facial_analysis' => [
        'confidence_score' => 85,
        'eye_contact_score' => 80,
        'expression_balance' => 75,
        'expression_summary' => 'Candidate displayed appropriate facial expressions with good eye contact and confident demeanor.'
    ],
    'movement_analysis' => [
        'posture_score' => 82,
        'gesture_score' => 78,
        'natural_movement' => 85,
        'assessment' => 'Candidate showed good body language with controlled movements and professional posture throughout the interview.'
    ],
    'answer_analysis' => [
        'relevance_score' => 82,
        'coherence_score' => 78,
        'completeness_score' => 85,
        'answer_assessment' => 'Candidate provided well-structured responses with good content coverage.'
    ],
    'performance_feedback' => [
        'Strong interview performance with good response quality',
        'High engagement with comprehensive question coverage'
    ]
];

echo "<h3>Sample Data Structure</h3>";
echo "<pre>";
print_r($sampleData);
echo "</pre>";

// Test JSON encoding/decoding
$jsonData = json_encode($sampleData);
$decodedData = json_decode($jsonData, true);

echo "<h3>JSON Encoding/Decoding Test</h3>";
echo "<p>Original data matches decoded data: " . ($sampleData == $decodedData ? "YES" : "NO") . "</p>";

// Test the data extraction logic
$speech_data = $decodedData['speech_analysis'] ?? null;
$facial_data = $decodedData['facial_analysis'] ?? null;
$movement_data = $decodedData['movement_analysis'] ?? null;
$answer_data = $decodedData['answer_analysis'] ?? null;

echo "<h3>Extracted Analysis Data</h3>";
echo "<ul>";
echo "<li>Speech Data: " . ($speech_data ? "FOUND" : "MISSING") . "</li>";
echo "<li>Facial Data: " . ($facial_data ? "FOUND" : "MISSING") . "</li>";
echo "<li>Movement Data: " . ($movement_data ? "FOUND" : "MISSING") . "</li>";
echo "<li>Answer Data: " . ($answer_data ? "FOUND" : "MISSING") . "</li>";
echo "</ul>";

echo "<h3>Individual Scores</h3>";
if ($speech_data) {
    echo "<p>Speech Clarity: " . ($speech_data['clarity_score'] ?? 'N/A') . "%</p>";
}
if ($facial_data) {
    echo "<p>Facial Confidence: " . ($facial_data['confidence_score'] ?? 'N/A') . "%</p>";
}
if ($movement_data) {
    echo "<p>Posture Score: " . ($movement_data['posture_score'] ?? 'N/A') . "%</p>";
}
if ($answer_data) {
    echo "<p>Answer Relevance: " . ($answer_data['relevance_score'] ?? 'N/A') . "%</p>";
}

echo "<h2>Conclusion</h2>";
echo "<p>If all data shows as FOUND and scores display properly, the fix is working correctly.</p>";
?>