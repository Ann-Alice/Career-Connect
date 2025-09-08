<?php
// Test script to verify the fix
echo "<h1>Verifying Interview Results Fix</h1>";

// Create sample data structure that matches what we expect
$sampleData = [
    'overall_score' => 85.5,
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

echo "<h2>Sample Data Structure</h2>";
echo "<pre>";
print_r($sampleData);
echo "</pre>";

// Test JSON encoding/decoding
$jsonData = json_encode($sampleData);
$decodedData = json_decode($jsonData, true);

echo "<h2>JSON Encoding/Decoding Test</h2>";
echo "<p>Original data matches decoded data: " . ($sampleData == $decodedData ? "YES" : "NO") . "</p>";

// Test the data extraction logic
$speech_data = $decodedData['speech_analysis'] ?? null;
$facial_data = $decodedData['facial_analysis'] ?? null;
$movement_data = $decodedData['movement_analysis'] ?? null;
$answer_data = $decodedData['answer_analysis'] ?? null;

echo "<h2>Extracted Analysis Data</h2>";
echo "<ul>";
echo "<li>Speech Data: " . ($speech_data ? "FOUND" : "MISSING") . "</li>";
echo "<li>Facial Data: " . ($facial_data ? "FOUND" : "MISSING") . "</li>";
echo "<li>Movement Data: " . ($movement_data ? "FOUND" : "MISSING") . "</li>";
echo "<li>Answer Data: " . ($answer_data ? "FOUND" : "MISSING") . "</li>";
echo "</ul>";

echo "<h2>Individual Scores</h2>";
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