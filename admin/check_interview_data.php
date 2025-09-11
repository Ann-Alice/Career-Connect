<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    die("Access denied. Please log in as administrator.");
}

echo "<h2>Current Interview Data Analysis</h2>";

// Get all interviews with results
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS, INTERVIEW_COMPLETED_AT 
        FROM tbljobregistration 
        WHERE INTERVIEW_RESULTS IS NOT NULL 
        AND INTERVIEW_RESULTS != '' 
        AND INTERVIEW_RESULTS != 'null'
        ORDER BY INTERVIEW_COMPLETED_AT DESC 
        LIMIT 10";

$mydb->setQuery($sql);
$interviews = $mydb->loadResultList();

if ($interviews) {
    echo "<p>Found " . count($interviews) . " interviews with results data:</p>";
    
    foreach ($interviews as $interview) {
        echo "<div style='background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 15px 0; border-left: 4px solid #667eea;'>";
        echo "<h4>Registration ID: " . $interview->REGISTRATIONID . "</h4>";
        echo "<p><strong>Status:</strong> " . $interview->INTERVIEW_STATUS . "</p>";
        echo "<p><strong>Completed:</strong> " . ($interview->INTERVIEW_COMPLETED_AT ? $interview->INTERVIEW_COMPLETED_AT : 'N/A') . "</p>";
        
        // Try to decode the JSON data
        $resultsData = json_decode($interview->INTERVIEW_RESULTS, true);
        
        if ($resultsData) {
            echo "<p><strong>Data Structure:</strong></p>";
            echo "<ul>";
            foreach ($resultsData as $key => $value) {
                if (is_array($value)) {
                    echo "<li>" . $key . ": [Array with " . count($value) . " items]</li>";
                } else {
                    echo "<li>" . $key . ": " . (is_numeric($value) ? $value : substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '')) . "</li>";
                }
            }
            echo "</ul>";
            
            // Check for detailed analysis data
            $hasSpeech = isset($resultsData['speech_analysis']);
            $hasFacial = isset($resultsData['facial_analysis']);
            $hasMovement = isset($resultsData['movement_analysis']);
            $hasAnswer = isset($resultsData['answer_analysis']);
            
            echo "<p><strong>Detailed Analysis Available:</strong></p>";
            echo "<ul>";
            echo "<li>Speech Analysis: " . ($hasSpeech ? "✅ Yes" : "❌ No") . "</li>";
            echo "<li>Facial Analysis: " . ($hasFacial ? "✅ Yes" : "❌ No") . "</li>";
            echo "<li>Movement Analysis: " . ($hasMovement ? "✅ Yes" : "❌ No") . "</li>";
            echo "<li>Answer Analysis: " . ($hasAnswer ? "✅ Yes" : "❌ No") . "</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: #e74c3c;'><strong>Invalid JSON Data:</strong> " . substr($interview->INTERVIEW_RESULTS, 0, 100) . "...</p>";
        }
        
        echo "</div>";
    }
} else {
    echo "<p style='color: #e74c3c;'>No interviews with results data found.</p>";
    echo "<p>Possible reasons:</p>";
    echo "<ol>";
    echo "<li>No interviews have been completed yet</li>";
    echo "<li>Interview results haven't been processed by the AI system</li>";
    echo "<li>Database connection issues</li>";
    echo "</ol>";
}

echo "<h3>Database Schema Check</h3>";

// Check if the INTERVIEW_RESULTS column exists
$checkSql = "SHOW COLUMNS FROM tbljobregistration LIKE 'INTERVIEW_RESULTS'";
$mydb->setQuery($checkSql);
$column = $mydb->loadSingleResult();

if ($column) {
    echo "<p>✅ INTERVIEW_RESULTS column exists</p>";
} else {
    echo "<p>❌ INTERVIEW_RESULTS column is missing</p>";
    echo "<p>You may need to run the add_interview_results_column.php script</p>";
}

echo "<p><a href='interview-results.php'>← Back to Interview Results</a> | <a href='populate_detailed_analysis_data.php'>Populate Detailed Data</a></p>";
?>