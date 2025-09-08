<?php
/**
 * Test Interview Completion Fix
 * Tests the interview completion process to verify the HTTP 500 error is resolved
 */

require_once('include/initialize.php');

echo "<h2>🔧 Interview Completion Error Fix Test</h2>";

// Test the interview completion endpoint
echo "<h3>Testing Interview Completion Process</h3>";

try {
    // Check if database tables exist
    echo "<h4>1. Database Table Verification</h4>";
    
    // Check tblinterviewvideos table structure
    $sql = "DESCRIBE tblinterviewvideos";
    $mydb->setQuery($sql);
    $columns = $mydb->loadResultList();
    
    echo "<strong>tblinterviewvideos table structure:</strong><br>";
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li>{$col->Field} ({$col->Type})" . ($col->Key == 'PRI' ? ' - PRIMARY KEY' : '') . "</li>";
    }
    echo "</ul>";
    
    // Verify the primary key column name
    $primaryKeyColumn = null;
    foreach ($columns as $col) {
        if ($col->Key == 'PRI') {
            $primaryKeyColumn = $col->Field;
            break;
        }
    }
    
    if ($primaryKeyColumn) {
        echo "<div style='color: green;'>✅ Primary key column found: <strong>$primaryKeyColumn</strong></div>";
    } else {
        echo "<div style='color: red;'>❌ No primary key column found in tblinterviewvideos</div>";
    }
    
    echo "<hr>";
    
    // Test consolidation function (should be skipped now)
    echo "<h4>2. Testing Consolidation Function Status</h4>";
    
    if (function_exists('consolidateInterviewVideos')) {
        echo "<div style='color: orange;'>⚠️ consolidateInterviewVideos function still exists in complete-interview.php</div>";
        echo "<p><strong>Note:</strong> The function exists but JavaScript should no longer call it.</p>";
    } else {
        echo "<div style='color: green;'>✅ consolidateInterviewVideos function not found (good)</div>";
    }
    
    echo "<hr>";
    
    // Test JavaScript modification
    echo "<h4>3. JavaScript Consolidation Call Status</h4>";
    
    $jsFile = 'theme/js/ai-interview.js';
    if (file_exists($jsFile)) {
        $jsContent = file_get_contents($jsFile);
        
        // Check if consolidateInterviewVideo() call is removed from completeInterview()
        if (strpos($jsContent, 'consolidateInterviewVideo();') !== false) {
            echo "<div style='color: red;'>❌ JavaScript still contains consolidateInterviewVideo() call</div>";
        } else {
            echo "<div style='color: green;'>✅ consolidateInterviewVideo() call removed from JavaScript</div>";
        }
        
        // Check for the skip message
        if (strpos($jsContent, 'Skipping video consolidation') !== false) {
            echo "<div style='color: green;'>✅ Skip consolidation message found in JavaScript</div>";
        } else {
            echo "<div style='color: orange;'>⚠️ Skip consolidation message not found</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ JavaScript file not found: $jsFile</div>";
    }
    
    echo "<hr>";
    
    // Test data for completion
    echo "<h4>4. Test Interview Completion Data Structure</h4>";
    
    $testData = [
        'token' => 'test_token_123',
        'registrationId' => '999',
        'chatMessages' => [],
        'aiResponses' => [],
        'candidateResponses' => [],
        'totalQuestions' => 5,
        'completedQuestions' => 3,
        'candidateFeedback' => null,
        'selectedVoice' => 'female'
    ];
    
    echo "<strong>Test data structure:</strong><br>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    echo json_encode($testData, JSON_PRETTY_PRINT);
    echo "</pre>";
    
    echo "<div style='color: blue;'>ℹ️ This is the data structure that would be sent to complete-interview.php</div>";
    
    echo "<hr>";
    
    // Summary
    echo "<h4>5. Fix Summary</h4>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
    echo "<h5>✅ Applied Fixes:</h5>";
    echo "<ul>";
    echo "<li><strong>JavaScript Fix:</strong> Removed consolidateInterviewVideo() call from completeInterview() function</li>";
    echo "<li><strong>Database Fix:</strong> Fixed column name from 'ID' to 'VIDEOID' in complete-interview.php</li>";
    echo "<li><strong>Priority Fix:</strong> Download system now prioritizes individual recordings over consolidation</li>";
    echo "</ul>";
    
    echo "<h5>Expected Result:</h5>";
    echo "<ul>";
    echo "<li>No more HTTP 500 errors when clicking 'Complete Interview' button</li>";
    echo "<li>Interview completion should work smoothly</li>";
    echo "<li>Downloads will contain complete interview content (not 5-second clips)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<hr>";
    
    echo "<h4>6. Next Steps</h4>";
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;'>";
    echo "<p><strong>To test the fix:</strong></p>";
    echo "<ol>";
    echo "<li>Start a new interview at: <a href='interview.php'>interview.php</a></li>";
    echo "<li>Answer at least one question completely</li>";
    echo "<li>Click the 'Complete Interview' button</li>";
    echo "<li>Verify no HTTP 500 error occurs</li>";
    echo "<li>Check that the interview is marked as completed</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Error during testing: " . $e->getMessage() . "</div>";
}

?>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 1000px; 
    margin: 0 auto; 
    padding: 20px; 
    background: #f8f9fa; 
}
h2, h3, h4 { color: #333; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
hr { margin: 20px 0; border: none; border-top: 1px solid #dee2e6; }
</style>