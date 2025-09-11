<?php
require_once('../include/initialize.php');

// Sample interview analysis data
$sampleData = [
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
$jsonData = json_encode($sampleData);

// Update a sample interview record with this data
// Note: You'll need to replace REGISTRATIONID with an actual ID from your database
$sql = "UPDATE tbljobregistration 
        SET INTERVIEW_RESULTS = '" . $mydb->escape_string($jsonData) . "',
            INTERVIEW_STATUS = 'Completed',
            INTERVIEW_COMPLETED_AT = NOW()
        WHERE REGISTRATIONID = 1"; // Replace with actual registration ID

$mydb->setQuery($sql);

if ($mydb->executeQuery()) {
    echo "<h2>Sample Data Added Successfully</h2>";
    echo "<p>Sample interview analysis data has been added to registration ID 1.</p>";
    echo "<p>You can now view the detailed breakdown in the Interview Results section.</p>";
} else {
    echo "<h2>Error Adding Sample Data</h2>";
    echo "<p>There was an error updating the database. Please check the registration ID.</p>";
    echo "<p>Error: " . $mydb->getErrorMsg() . "</p>";
}

echo "<h3>Sample Data Structure:</h3>";
echo "<pre>";
print_r($sampleData);
echo "</pre>";
?>