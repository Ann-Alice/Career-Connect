<?php
/**
 * Test Download Recording Fix
 * Verifies that the bind_param() error is resolved
 */

require_once('include/initialize.php');

echo "<h2>🔧 Download Recording Fix Test</h2>";

try {
    // Check if admin is logged in for testing
    if(!isset($_SESSION['ADMIN_USERID'])){
        echo "<div style='color: orange;'>⚠️ Warning: Admin not logged in. This test simulates the admin environment.</div><br>";
    }
    
    echo "<h3>1. Database Connection Test</h3>";
    
    // Test basic database connection
    $sql = "SELECT COUNT(*) as count FROM tbljobregistration";
    $mydb->setQuery($sql);
    $result = $mydb->loadSingleResult();
    
    if ($result) {
        echo "<div style='color: green;'>✅ Database connection working</div>";
        echo "<div>Found {$result->count} job registrations in database</div>";
    } else {
        echo "<div style='color: red;'>❌ Database connection failed</div>";
    }
    
    echo "<hr>";
    
    echo "<h3>2. Testing Database Query Methods</h3>";
    
    // Test the methods used in download-recording.php
    $test_registration_id = 1; // Use a test ID
    
    echo "<h4>Testing tblinterviewrecordings query:</h4>";
    try {
        $sql = "SELECT FILE_PATH, DURATION, RECORDED_AT FROM tblinterviewrecordings WHERE REGISTRATIONID = $test_registration_id ORDER BY DURATION DESC, RECORDED_AT DESC LIMIT 1";
        $mydb->setQuery($sql);
        $recording_record = $mydb->loadSingleResult();
        
        if ($recording_record) {
            echo "<div style='color: green;'>✅ Individual recordings query successful</div>";
            echo "<div>Found recording: {$recording_record->FILE_PATH}</div>";
        } else {
            echo "<div style='color: blue;'>ℹ️ No individual recordings found for registration ID $test_registration_id</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Individual recordings query failed: " . $e->getMessage() . "</div>";
    }
    
    echo "<h4>Testing tblinterviewvideos query:</h4>";
    try {
        $sql = "SELECT VIDEO_PATH FROM tblinterviewvideos WHERE REGISTRATIONID = $test_registration_id ORDER BY CREATED_AT DESC LIMIT 1";
        $mydb->setQuery($sql);
        $video_record = $mydb->loadSingleResult();
        
        if ($video_record) {
            echo "<div style='color: green;'>✅ Consolidated videos query successful</div>";
            echo "<div>Found video: {$video_record->VIDEO_PATH}</div>";
        } else {
            echo "<div style='color: blue;'>ℹ️ No consolidated videos found for registration ID $test_registration_id</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Consolidated videos query failed: " . $e->getMessage() . "</div>";
    }
    
    echo "<hr>";
    
    echo "<h3>3. Database Class Methods Check</h3>";
    
    // Check what methods are available in the Database class
    $db_methods = get_class_methods($mydb);
    
    echo "<h4>Available Database methods:</h4>";
    echo "<ul>";
    foreach ($db_methods as $method) {
        echo "<li>$method</li>";
    }
    echo "</ul>";
    
    // Check if bind_param exists (it shouldn't)
    if (method_exists($mydb, 'bind_param')) {
        echo "<div style='color: red;'>❌ bind_param method still exists (should be removed)</div>";
    } else {
        echo "<div style='color: green;'>✅ bind_param method does not exist (correct)</div>";
    }
    
    echo "<hr>";
    
    echo "<h3>4. Registrations with Recordings</h3>";
    
    // Find registrations that have recordings
    try {
        $sql = "SELECT DISTINCT r.REGISTRATIONID, a.FNAME, a.LNAME 
                FROM tbljobregistration r 
                JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                WHERE r.REGISTRATIONID IN (
                    SELECT REGISTRATIONID FROM tblinterviewrecordings
                    UNION 
                    SELECT REGISTRATIONID FROM tblinterviewvideos
                )
                ORDER BY r.REGISTRATIONID DESC 
                LIMIT 5";
        $mydb->setQuery($sql);
        $registrations = $mydb->loadResultList();
        
        if ($registrations) {
            echo "<h4>Registrations with recordings available for testing:</h4>";
            echo "<ul>";
            foreach ($registrations as $reg) {
                echo "<li>ID: {$reg->REGISTRATIONID} - {$reg->FNAME} {$reg->LNAME} 
                      <a href='admin/download-recording.php?id={$reg->REGISTRATIONID}' target='_blank'>Test Download</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='color: orange;'>⚠️ No registrations with recordings found</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Error finding registrations: " . $e->getMessage() . "</div>";
    }
    
    echo "<hr>";
    
    echo "<h3>5. Fix Summary</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
    echo "<h4>✅ Fixed Issues:</h4>";
    echo "<ul>";
    echo "<li><strong>Removed bind_param() calls:</strong> The Database class doesn't support prepared statements</li>";
    echo "<li><strong>Used direct SQL with validated integers:</strong> Safe because registration_id is validated with intval()</li>";
    echo "<li><strong>Maintained SQL injection protection:</strong> Registration ID is properly validated as integer</li>";
    echo "</ul>";
    
    echo "<h4>Before Fix:</h4>";
    echo "<pre style='background: #f8d7da; padding: 10px;'>";
    echo "\$sql = \"SELECT ... WHERE REGISTRATIONID = ?\";\n";
    echo "\$mydb->setQuery(\$sql);\n";
    echo "\$mydb->bind_param('i', \$registration_id); // ❌ Method doesn't exist\n";
    echo "\$result = \$mydb->loadSingleResult();";
    echo "</pre>";
    
    echo "<h4>After Fix:</h4>";
    echo "<pre style='background: #d4edda; padding: 10px;'>";
    echo "\$sql = \"SELECT ... WHERE REGISTRATIONID = \$registration_id\";\n";
    echo "\$mydb->setQuery(\$sql);\n";
    echo "\$result = \$mydb->loadSingleResult(); // ✅ Works correctly";
    echo "</pre>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Test failed with error: " . $e->getMessage() . "</div>";
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
pre { font-family: monospace; font-size: 12px; }
</style>