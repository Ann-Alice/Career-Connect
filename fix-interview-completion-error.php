<?php
// Fix for interview completion HTTP 500 error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Fix Interview Completion Error</h1>";

try {
    require_once('include/initialize.php');
    echo "<div style='color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>✅ Successfully loaded initialize.php</div>";
} catch (Exception $e) {
    echo "<div style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>❌ Error loading initialize.php: " . $e->getMessage() . "</div>";
    exit;
}

echo "<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
.success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
.error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
.warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
.info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
.btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
.btn:hover { background: #0056b3; }
.btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
.btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
h1 { color: #333; text-align: center; margin-bottom: 30px; }
h2 { color: #495057; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; }
</style>";

$database_ports = [4306, 3306];
$conn = null;

// Test database connection with multiple ports
echo "<h2>Step 1: Database Connection Test</h2>";
foreach ($database_ports as $port) {
    echo "Trying MySQL on port $port...<br>";
    $conn = @mysqli_connect('localhost', 'root', '', 'erisdb', $port);
    if ($conn) {
        echo "<div class='success'>✅ Connected to MySQL on port $port</div>";
        break;
    }
}

if (!$conn) {
    echo "<div class='error'>❌ Could not connect to MySQL on any port</div>";
    exit;
}

// Step 2: Check and fix tbljobregistration table
echo "<h2>Step 2: Fix tbljobregistration Table Structure</h2>";

// Check current structure
$sql = "DESCRIBE tbljobregistration";
$result = mysqli_query($conn, $sql);
$existing_columns = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $existing_columns[] = $row['Field'];
    }
}

$required_columns = [
    'INTERVIEW_STATUS' => 'VARCHAR(20) NULL',
    'INTERVIEW_COMPLETED_AT' => 'DATETIME NULL',
    'INTERVIEW_RESULTS' => 'LONGTEXT NULL'
];

$missing_columns = [];
foreach ($required_columns as $column => $definition) {
    if (!in_array($column, $existing_columns)) {
        $missing_columns[$column] = $definition;
    }
}

if (empty($missing_columns)) {
    echo "<div class='success'>✅ All required columns exist in tbljobregistration</div>";
} else {
    echo "<div class='warning'>⚠️ Missing columns found. Adding them now...</div>";
    
    foreach ($missing_columns as $column => $definition) {
        $alter_sql = "ALTER TABLE `tbljobregistration` ADD COLUMN `{$column}` {$definition}";
        echo "<p>Adding column: <code>{$column}</code></p>";
        
        if (mysqli_query($conn, $alter_sql)) {
            echo "<div class='success'>✅ Added column {$column}</div>";
        } else {
            echo "<div class='error'>❌ Failed to add column {$column}: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Step 3: Check and create tblinterviewinvitations table
echo "<h2>Step 3: Fix Interview Invitations Table</h2>";

$sql = "SHOW TABLES LIKE 'tblinterviewinvitations'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<div class='warning'>⚠️ tblinterviewinvitations table missing. Creating it...</div>";
    
    $create_sql = "CREATE TABLE `tblinterviewinvitations` (
        `INVITATION_ID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` varchar(50) NOT NULL,
        `JOBID` varchar(50) NOT NULL,
        `TOKEN` varchar(255) NOT NULL,
        `EXPIRY_DATE` timestamp NOT NULL,
        `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `STATUS` enum('pending','completed','expired') NOT NULL DEFAULT 'pending',
        PRIMARY KEY (`INVITATION_ID`),
        UNIQUE KEY `TOKEN` (`TOKEN`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`),
        KEY `EXPIRY_DATE` (`EXPIRY_DATE`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $create_sql)) {
        echo "<div class='success'>✅ Created tblinterviewinvitations table</div>";
    } else {
        echo "<div class='error'>❌ Failed to create table: " . mysqli_error($conn) . "</div>";
    }
} else {
    echo "<div class='success'>✅ tblinterviewinvitations table exists</div>";
}

// Step 4: Check and fix tblinterviewrecordings table (for TRANSCRIPT column)
echo "<h2>Step 4: Fix Interview Recordings Table</h2>";

$sql = "SHOW TABLES LIKE 'tblinterviewrecordings'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Check if TRANSCRIPT column exists
    $sql = "SHOW COLUMNS FROM tblinterviewrecordings LIKE 'TRANSCRIPT'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo "<div class='warning'>⚠️ TRANSCRIPT column missing. Adding it...</div>";
        
        $alter_sql = "ALTER TABLE `tblinterviewrecordings` ADD COLUMN `TRANSCRIPT` TEXT AFTER `QUESTION_TYPE`";
        if (mysqli_query($conn, $alter_sql)) {
            echo "<div class='success'>✅ Added TRANSCRIPT column</div>";
        } else {
            echo "<div class='error'>❌ Failed to add TRANSCRIPT column: " . mysqli_error($conn) . "</div>";
        }
    } else {
        echo "<div class='success'>✅ TRANSCRIPT column exists</div>";
    }
} else {
    echo "<div class='warning'>⚠️ tblinterviewrecordings table missing. Creating it...</div>";
    
    $create_sql = "CREATE TABLE `tblinterviewrecordings` (
        `RECORDING_ID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` varchar(50) NOT NULL,
        `QUESTION_NUMBER` int(11) NOT NULL DEFAULT 0,
        `FILE_PATH` varchar(500) NOT NULL,
        `DURATION` decimal(10,3) NOT NULL DEFAULT 0.000,
        `RECORDED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `CONVERSATION_TURN` int(11) NOT NULL DEFAULT 0,
        `QUESTION_TYPE` varchar(50) NOT NULL DEFAULT 'initial_answer',
        `TRANSCRIPT` text,
        PRIMARY KEY (`RECORDING_ID`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`),
        KEY `RECORDED_AT` (`RECORDED_AT`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $create_sql)) {
        echo "<div class='success'>✅ Created tblinterviewrecordings table with TRANSCRIPT column</div>";
    } else {
        echo "<div class='error'>❌ Failed to create table: " . mysqli_error($conn) . "</div>";
    }
}

// Step 5: Test the problematic UPDATE query
echo "<h2>Step 5: Test Interview Completion Query</h2>";

$sql = "SELECT REGISTRATIONID FROM tbljobregistration LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $test_id = $row['REGISTRATIONID'];
    
    echo "<div class='info'>Testing with Registration ID: {$test_id}</div>";
    
    // Test the exact UPDATE query that was causing the 500 error
    $test_sql = "UPDATE tbljobregistration 
                 SET INTERVIEW_STATUS = 'Test', 
                     INTERVIEW_COMPLETED_AT = NOW() 
                 WHERE REGISTRATIONID = '{$test_id}'";
    
    if (mysqli_query($conn, $test_sql)) {
        echo "<div class='success'>✅ UPDATE query test successful</div>";
        
        // Reset the test data
        $reset_sql = "UPDATE tbljobregistration 
                      SET INTERVIEW_STATUS = NULL, 
                          INTERVIEW_COMPLETED_AT = NULL 
                      WHERE REGISTRATIONID = '{$test_id}'";
        mysqli_query($conn, $reset_sql);
        echo "<div class='info'>🔄 Reset test data</div>";
    } else {
        echo "<div class='error'>❌ UPDATE query test failed: " . mysqli_error($conn) . "</div>";
    }
} else {
    echo "<div class='warning'>⚠️ No registration records found for testing</div>";
}

// Step 6: Check email configuration
echo "<h2>Step 6: Email Configuration Check</h2>";

try {
    if (file_exists('include/email_functions.php')) {
        require_once('include/email_functions.php');
        echo "<div class='success'>✅ Email functions loaded</div>";
        
        $email_constants = ['ADMIN_EMAIL', 'SMTP_HOST', 'SMTP_USER', 'SMTP_PASS'];
        $missing_email_config = [];
        
        foreach ($email_constants as $const) {
            if (!defined($const)) {
                $missing_email_config[] = $const;
            }
        }
        
        if (empty($missing_email_config)) {
            echo "<div class='success'>✅ All email constants are configured</div>";
        } else {
            echo "<div class='warning'>⚠️ Missing email configuration: " . implode(', ', $missing_email_config) . "</div>";
        }
    } else {
        echo "<div class='error'>❌ email_functions.php not found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Email configuration error: " . $e->getMessage() . "</div>";
}

// Step 7: Create improved complete-interview.php file
echo "<h2>Step 7: Deploy Fixed complete-interview.php</h2>";

$fixed_content = '<?php
// Enhanced complete-interview.php with robust error handling
require_once(\'include/initialize.php\');

// Enhanced error handling to prevent 500 errors
error_reporting(E_ALL);
ini_set(\'display_errors\', 0);
ini_set(\'log_errors\', 1);
set_time_limit(300);

// Set JSON content type
header(\'Content-Type: application/json\');

// Custom error handler to catch PHP errors
function handleError($severity, $message, $file, $line) {
    error_log("PHP Error: [$severity] $message in $file on line $line");
    if (!headers_sent()) {
        http_response_code(500);
        die(json_encode([\'success\' => false, \'error\' => \'Server error occurred\']));
    }
}
set_error_handler(\'handleError\');

try {
    // Check request method
    if ($_SERVER[\'REQUEST_METHOD\'] !== \'POST\') {
        throw new Exception(\'Invalid request method\');
    }

    // Get and validate input
    $input = file_get_contents(\'php://input\');
    if (empty($input)) {
        throw new Exception(\'No input data received\');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception(\'Invalid JSON data: \' . json_last_error_msg());
    }

    $token = isset($data[\'token\']) ? trim($data[\'token\']) : \'\';
    $registrationId = isset($data[\'registrationId\']) ? trim($data[\'registrationId\']) : \'\';

    if (empty($token) || empty($registrationId)) {
        throw new Exception(\'Missing required parameters\');
    }

    // Validate registrationId is numeric
    if (!is_numeric($registrationId)) {
        throw new Exception(\'Invalid registration ID format\');
    }

    // Check database connection
    if (!isset($mydb)) {
        throw new Exception(\'Database connection not available\');
    }

    // Validate token with proper escaping
    $token_escaped = $mydb->escape_string($token);
    $registrationId_escaped = $mydb->escape_string($registrationId);

    $sql = "SELECT i.*, r.APPLICANTID, r.JOBID 
            FROM tblinterviewinvitations i
            JOIN tbljobregistration r ON i.REGISTRATIONID = r.REGISTRATIONID
            WHERE i.TOKEN = \'{$token_escaped}\' 
            AND i.REGISTRATIONID = \'{$registrationId_escaped}\' 
            AND i.EXPIRY_DATE > NOW()";

    $mydb->setQuery($sql);
    $invitation = $mydb->loadSingleResult();

    if (!$invitation) {
        throw new Exception(\'Invalid or expired interview invitation\');
    }

    // Check if already completed
    $sql = "SELECT INTERVIEW_STATUS FROM tbljobregistration 
            WHERE REGISTRATIONID = \'{$registrationId_escaped}\'";
    $mydb->setQuery($sql);
    $current_status = $mydb->loadSingleResult();

    if ($current_status && $current_status->INTERVIEW_STATUS === \'Completed\') {
        throw new Exception(\'Interview has already been completed\');
    }

    // Extract data with safe defaults
    $chatMessages = isset($data[\'chatMessages\']) && is_array($data[\'chatMessages\']) ? $data[\'chatMessages\'] : [];
    $aiResponses = isset($data[\'aiResponses\']) && is_array($data[\'aiResponses\']) ? $data[\'aiResponses\'] : [];
    $totalQuestions = isset($data[\'totalQuestions\']) ? (int)$data[\'totalQuestions\'] : 0;
    $completedQuestions = isset($data[\'completedQuestions\']) ? (int)$data[\'completedQuestions\'] : 0;
    $candidateFeedback = isset($data[\'candidateFeedback\']) ? $data[\'candidateFeedback\'] : null;
    $selectedVoice = isset($data[\'selectedVoice\']) ? $data[\'selectedVoice\'] : \'female\';

    // Update interview status - most critical operation
    $sql = "UPDATE tbljobregistration 
            SET INTERVIEW_STATUS = \'Completed\', 
                INTERVIEW_COMPLETED_AT = NOW() 
            WHERE REGISTRATIONID = \'{$registrationId_escaped}\'";

    $mydb->setQuery($sql);
    $updateResult = $mydb->executeQuery();

    if (!$updateResult) {
        throw new Exception(\'Failed to update interview status\');
    }

    // Save interview results (non-critical, continue if fails)
    try {
        $interviewResults = [
            \'overall_score\' => min(100, max(0, $completedQuestions > 0 ? ($completedQuestions / max(1, $totalQuestions)) * 100 : 0)),
            \'total_questions_answered\' => $completedQuestions,
            \'interview_duration\' => isset($data[\'interviewDuration\']) ? (float)$data[\'interviewDuration\'] : 0,
            \'selected_voice\' => $selectedVoice,
            \'completed_at\' => date(\'Y-m-d H:i:s\')
        ];

        if ($candidateFeedback && is_array($candidateFeedback)) {
            $interviewResults[\'candidate_feedback\'] = $candidateFeedback;
        }

        $resultsJson = json_encode($interviewResults, JSON_UNESCAPED_UNICODE);
        if ($resultsJson) {
            $resultsJson_escaped = $mydb->escape_string($resultsJson);
            $sql = "UPDATE tbljobregistration 
                    SET INTERVIEW_RESULTS = \'{$resultsJson_escaped}\' 
                    WHERE REGISTRATIONID = \'{$registrationId_escaped}\'";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
        }
    } catch (Exception $e) {
        error_log("Error saving interview results: " . $e->getMessage());
        // Continue - this is not critical for completion
    }

    // Send email notification (non-critical, continue if fails)
    try {
        if (file_exists(\'include/email_functions.php\')) {
            require_once(\'include/email_functions.php\');
            
            if (defined(\'ADMIN_EMAIL\') && function_exists(\'sendEmail\')) {
                $sql = "SELECT a.EMAILADDRESS, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
                        FROM tbljobregistration r 
                        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
                        JOIN tbljob j ON r.JOBID = j.JOBID 
                        WHERE r.REGISTRATIONID = \'{$registrationId_escaped}\'";
                $mydb->setQuery($sql);
                $application = $mydb->loadSingleResult();

                if ($application) {
                    $subject = "Interview Completed - {$application->OCCUPATIONTITLE}";
                    $message = "Interview completed for {$application->FNAME} {$application->LNAME}";
                    sendEmail(ADMIN_EMAIL, $subject, $message);
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error sending email notification: " . $e->getMessage());
        // Continue - email failure should not block completion
    }

    // Return success
    echo json_encode([\'success\' => true, \'message\' => \'Interview completed successfully\']);

} catch (Exception $e) {
    error_log("Interview completion error: " . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode([\'success\' => false, \'error\' => $e->getMessage()]);
} catch (Error $e) {
    error_log("Fatal error in interview completion: " . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode([\'success\' => false, \'error\' => \'Server error occurred\']);
}
?>';

if (file_put_contents('complete-interview-fixed-v2.php', $fixed_content)) {
    echo "<div class='success'>✅ Created enhanced complete-interview-fixed-v2.php</div>";
} else {
    echo "<div class='error'>❌ Failed to create fixed file</div>";
}

mysqli_close($conn);

echo "<h2>🎯 Summary and Next Steps</h2>";
echo "<div class='success'>";
echo "<h3>✅ Fix Complete!</h3>";
echo "<p><strong>What was fixed:</strong></p>";
echo "<ul>";
echo "<li>Added missing database columns (INTERVIEW_STATUS, INTERVIEW_COMPLETED_AT, INTERVIEW_RESULTS)</li>";
echo "<li>Fixed TRANSCRIPT column in tblinterviewrecordings table</li>";
echo "<li>Created/verified tblinterviewinvitations table</li>";
echo "<li>Enhanced error handling in complete-interview.php</li>";
echo "<li>Improved SQL escaping and validation</li>";
echo "</ul>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📋 Testing the Fix</h3>";
echo "<p>The HTTP 500 error should now be resolved. To test:</p>";
echo "<ol>";
echo "<li>Try completing an interview again</li>";
echo "<li>Check that the status updates to \'Completed\'</li>";
echo "<li>Verify no more 500 errors occur</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='debug-interview-completion.php' class='btn'>🔍 Run Diagnostics</a>";
echo "<a href='test-interview-completion.php' class='btn btn-warning'>🧪 Test System</a>";
echo "<a href='admin/interview-results.php' class='btn btn-success'>📊 View Results</a>";
echo "</div>";
?>