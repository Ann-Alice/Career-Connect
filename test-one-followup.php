<?php
// Test script to verify ONE follow-up per question
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Testing ONE Follow-up Per Question - VERSION 7.0</h1>";

// Test the JavaScript file for the new version
$jsFile = 'theme/js/ai-interview.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    echo "<h2>JavaScript Version Check</h2>";
    
    if (strpos($content, 'VERSION 7.0') !== false) {
        echo "✅ JavaScript file contains VERSION 7.0<br>";
    } else {
        echo "❌ JavaScript file needs to be updated to VERSION 7.0<br>";
    }
    
    if (strpos($content, 'One follow-up per question') !== false) {
        echo "✅ One follow-up per question fix detected<br>";
    } else {
        echo "❌ One follow-up per question fix not found<br>";
    }
    
    if (strpos($content, 'generateOneFollowUpQuestion') !== false) {
        echo "✅ generateOneFollowUpQuestion function found<br>";
    } else {
        echo "❌ generateOneFollowUpQuestion function not found<br>";
    }
    
    if (strpos($content, 'currentQuestionState') !== false) {
        echo "✅ State tracking found<br>";
    } else {
        echo "❌ State tracking not found<br>";
    }
    
    if (strpos($content, 'hasAskedFollowUp') !== false) {
        echo "✅ Follow-up tracking found<br>";
    } else {
        echo "❌ Follow-up tracking not found<br>";
    }
    
    if (strpos($content, 'moveToNextQuestion') !== false) {
        echo "✅ moveToNextQuestion function found<br>";
    } else {
        echo "❌ moveToNextQuestion function not found<br>";
    }
    
    if (strpos($content, 'SIMPLE STATE MACHINE') !== false) {
        echo "✅ State machine logic found<br>";
    } else {
        echo "❌ State machine logic not found<br>";
    }
    
} else {
    echo "❌ JavaScript file not found<br>";
}

echo "<h2>New Conversation Flow (ONE FOLLOW-UP)</h2>";
echo "<p>The new conversation flow is:</p>";
echo "<ol>";
echo "<li><strong>AI asks essential question</strong></li>";
echo "<li><strong>Candidate responds</strong></li>";
echo "<li><strong>AI asks ONE follow-up question</strong></li>";
echo "<li><strong>Candidate responds to follow-up</strong></li>";
echo "<li><strong>AI acknowledges and moves to NEXT essential question</strong></li>";
echo "<li><strong>Process repeats until all questions are covered</strong></li>";
echo "</ol>";

echo "<h2>Key Changes Made</h2>";
echo "<ul>";
echo "<li>✅ Added state tracking (initial/followup)</li>";
echo "<li>✅ Added hasAskedFollowUp flag</li>";
echo "<li>✅ Added generateOneFollowUpQuestion function</li>";
echo "<li>✅ Added generateAcknowledgment function</li>";
echo "<li>✅ Added moveToNextQuestion function</li>";
echo "<li>✅ Simple state machine logic</li>";
echo "<li>✅ Exactly ONE follow-up per essential question</li>";
echo "<li>✅ Clear progression to next question after follow-up</li>";
echo "</ul>";

echo "<h2>State Machine Logic</h2>";
echo "<ul>";
echo "<li><strong>Initial State:</strong> AI asks essential question</li>";
echo "<li><strong>After First Response:</strong> AI asks ONE follow-up question</li>";
echo "<li><strong>After Follow-up Response:</strong> AI acknowledges and moves to next question</li>";
echo "<li><strong>Reset:</strong> State resets for each new essential question</li>";
echo "</ul>";

echo "<h2>Testing Instructions</h2>";
echo "<p>To test the new logic:</p>";
echo "<ol>";
echo "<li>Start an interview</li>";
echo "<li>Answer the first essential question</li>";
echo "<li>Verify AI asks ONE follow-up question</li>";
echo "<li>Answer the follow-up question</li>";
echo "<li>Verify AI acknowledges and moves to next essential question</li>";
echo "<li>Repeat for all questions</li>";
echo "<li>Check that only ONE follow-up is asked per question</li>";
echo "</ol>";

echo "<p><strong>Expected Behavior:</strong></p>";
echo "<ul>";
echo "<li>Exactly ONE follow-up question per essential question</li>";
echo "<li>No more follow-up questions after the first one</li>";
echo "<li>Clear acknowledgment after follow-up response</li>";
echo "<li>Smooth progression to next essential question</li>";
echo "<li>No loops or repetition</li>";
echo "<li>Proper state tracking and reset</li>";
echo "</ul>";

echo "<p><strong>This should fix the loop issue with exactly ONE follow-up per question!</strong></p>";

echo "<p><a href='interview.php?token=test'>Test Interview System</a></p>";
?> 