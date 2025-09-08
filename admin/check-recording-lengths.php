<?php
/**
 * Check Recording Lengths - Diagnostic Tool
 * This shows the difference between individual recordings vs consolidated videos
 * to verify that individual recordings contain the complete interview content
 */

require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔍 Recording Length Analysis</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
            background: #f8f9fa; 
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 30px; 
            border-radius: 15px; 
            margin-bottom: 30px; 
            text-align: center; 
        }
        .section { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            margin: 20px 0; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .problem { 
            color: #dc3545; 
            background: #f8d7da; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 15px 0; 
            border-left: 4px solid #dc3545;
        }
        .solution { 
            color: #155724; 
            background: #d4edda; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 15px 0; 
            border-left: 4px solid #28a745;
        }
        .warning { 
            color: #856404; 
            background: #fff3cd; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 15px 0; 
            border-left: 4px solid #ffc107;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin: 20px 0;
        }
        th, td { 
            border: 1px solid #dee2e6; 
            padding: 12px; 
            text-align: left; 
        }
        th { 
            background-color: #f8f9fa; 
            font-weight: bold;
        }
        .btn { 
            background: #007bff; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 5px; 
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .duration-good { color: #28a745; font-weight: bold; }
        .duration-bad { color: #dc3545; font-weight: bold; }
        .file-size { font-family: monospace; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Recording Length Analysis</h1>
        <p>Comparing Individual Recordings vs Consolidated Videos</p>
        <p><strong>Problem:</strong> Consolidated videos were only 5 seconds, missing the actual interview content!</p>
    </div>

    <div class="section">
        <h2>📊 Recording Analysis Results</h2>
        
        <div class="problem">
            <h3>❌ Previous Problem Identified:</h3>
            <ul>
                <li><strong>Consolidated videos:</strong> Only 5-second clips (useless)</li>
                <li><strong>Download priority:</strong> System downloaded the 5-second clips first</li>
                <li><strong>Result:</strong> Users got incomplete interview recordings</li>
            </ul>
        </div>
        
        <div class="solution">
            <h3>✅ Solution Applied:</h3>
            <ul>
                <li><strong>Priority changed:</strong> Individual recordings downloaded first</li>
                <li><strong>Consolidation disabled:</strong> No more useless 5-second videos</li>
                <li><strong>Result:</strong> Users now get complete interview content</li>
            </ul>
        </div>

        <?php
        try {
            // Get all interview registrations with recordings
            $sql = "SELECT DISTINCT 
                        r.REGISTRATIONID,
                        a.FNAME, 
                        a.LNAME,
                        j.OCCUPATIONTITLE,
                        c.COMPANYNAME
                    FROM tbljobregistration r 
                    JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                    JOIN tbljob j ON r.JOBID = j.JOBID 
                    JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                    WHERE r.REGISTRATIONID IN (
                        SELECT REGISTRATIONID FROM tblinterviewrecordings
                        UNION
                        SELECT REGISTRATIONID FROM tblinterviewvideos
                    )
                    ORDER BY r.REGISTRATIONID DESC 
                    LIMIT 20";
            
            $mydb->setQuery($sql);
            $interviews = $mydb->loadResultList();
            
            if ($interviews) {
                echo "<h3>Recent Interview Recordings:</h3>";
                echo "<table>";
                echo "<tr>";
                echo "<th>Registration ID</th>";
                echo "<th>Candidate</th>";
                echo "<th>Position</th>";
                echo "<th>Individual Recordings</th>";
                echo "<th>Consolidated Video</th>";
                echo "<th>Status</th>";
                echo "<th>Action</th>";
                echo "</tr>";
                
                foreach ($interviews as $interview) {
                    $registration_id = $interview->REGISTRATIONID;
                    
                    // Check individual recordings
                    $sql_individual = "SELECT COUNT(*) as count, SUM(DURATION) as total_duration, AVG(DURATION) as avg_duration 
                                     FROM tblinterviewrecordings WHERE REGISTRATIONID = ?";
                    $mydb->setQuery($sql_individual);
                    $mydb->bind_param('i', $registration_id);
                    $individual_stats = $mydb->loadSingleResult();
                    
                    // Check consolidated video
                    $sql_consolidated = "SELECT COUNT(*) as count, CREATED_AT FROM tblinterviewvideos WHERE REGISTRATIONID = ?";
                    $mydb->setQuery($sql_consolidated);
                    $mydb->bind_param('i', $registration_id);
                    $consolidated_stats = $mydb->loadSingleResult();
                    
                    $individual_count = $individual_stats->count ?? 0;
                    $individual_duration = $individual_stats->total_duration ?? 0;
                    $consolidated_count = $consolidated_stats->count ?? 0;
                    
                    echo "<tr>";
                    echo "<td><strong>{$registration_id}</strong></td>";
                    echo "<td>" . htmlspecialchars($interview->FNAME . ' ' . $interview->LNAME) . "</td>";
                    echo "<td>" . htmlspecialchars($interview->OCCUPATIONTITLE) . "</td>";
                    
                    // Individual recordings column
                    if ($individual_count > 0) {
                        $duration_class = $individual_duration > 10 ? 'duration-good' : 'duration-bad';
                        echo "<td class='{$duration_class}'>";
                        echo "✅ {$individual_count} recordings<br>";
                        echo "📊 Total: " . round($individual_duration, 1) . " seconds<br>";
                        echo "📈 Avg: " . round($individual_stats->avg_duration, 1) . " sec each";
                        echo "</td>";
                    } else {
                        echo "<td class='duration-bad'>❌ No recordings</td>";
                    }
                    
                    // Consolidated video column
                    if ($consolidated_count > 0) {
                        echo "<td class='duration-bad'>⚠️ {$consolidated_count} consolidated<br><small>(5-second clips)</small></td>";
                    } else {
                        echo "<td class='duration-good'>✅ No consolidation<br><small>(Using individual recordings)</small></td>";
                    }
                    
                    // Status column
                    if ($individual_count > 0 && $individual_duration > 10) {
                        echo "<td class='duration-good'>✅ Complete Interview<br><small>Full content available</small></td>";
                    } elseif ($individual_count > 0) {
                        echo "<td class='warning'>⚠️ Short Recording<br><small>May be incomplete</small></td>";
                    } else {
                        echo "<td class='duration-bad'>❌ No Recording<br><small>Interview incomplete</small></td>";
                    }
                    
                    // Action column
                    echo "<td>";
                    if ($individual_count > 0) {
                        echo "<a href='download-recording.php?id={$registration_id}' class='btn btn-success'>📥 Download</a>";
                    } else {
                        echo "<span style='color: #6c757d;'>No download</span>";
                    }
                    echo "</td>";
                    
                    echo "</tr>";
                }
                
                echo "</table>";
                
            } else {
                echo "<div class='warning'>";
                echo "<h3>⚠️ No Interview Recordings Found</h3>";
                echo "<p>No recordings found in the database. This could mean:</p>";
                echo "<ul>";
                echo "<li>No interviews have been completed yet</li>";
                echo "<li>Recording system is not working properly</li>";
                echo "<li>Database tables are empty</li>";
                echo "</ul>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='problem'>";
            echo "<h3>❌ Database Error</h3>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>🔧 Fix Summary</h2>
        
        <div class="solution">
            <h3>✅ Changes Made to Fix Partial Recording Issue:</h3>
            <ol>
                <li><strong>Download Priority Changed:</strong> 
                    <ul>
                        <li>Before: Consolidated videos (5-second clips) downloaded first</li>
                        <li>After: Individual recordings (complete content) downloaded first</li>
                    </ul>
                </li>
                <li><strong>Consolidation Process Disabled:</strong>
                    <ul>
                        <li>Before: Created meaningless 5-second videos on completion</li>
                        <li>After: Skips consolidation, uses actual interview recordings</li>
                    </ul>
                </li>
                <li><strong>Database Query Updated:</strong>
                    <ul>
                        <li>Now prioritizes recordings with longest duration</li>
                        <li>Ensures complete interview content is served</li>
                    </ul>
                </li>
            </ol>
        </div>
        
        <div class="warning">
            <h3>⚠️ Testing Recommendations:</h3>
            <ul>
                <li><strong>Test New Interview:</strong> Complete a full interview and verify download contains complete content</li>
                <li><strong>Check Existing Recordings:</strong> Re-download existing interviews to see if they now work properly</li>
                <li><strong>Verify Duration:</strong> Downloaded videos should be much longer than 5 seconds</li>
                <li><strong>Monitor Console:</strong> Check browser console during interview for recording chunk messages</li>
            </ul>
        </div>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="interview-results.php" class="btn">📊 Back to Interview Results</a>
        <a href="../test-recording.php" class="btn btn-success">🧪 Test Recording System</a>
        <a href="test-recording-system.php" class="btn">🔍 Full System Test</a>
    </div>

</body>
</html>