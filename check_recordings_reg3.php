<?php
require_once('include/initialize.php');

// Check if interview recording exists for registration ID 3
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

echo "Recordings for registration ID 3:\n";
if ($recordings) {
    foreach ($recordings as $recording) {
        echo "Recording ID: " . $recording->RECORDING_ID . "\n";
        echo "File Path: " . $recording->FILE_PATH . "\n";
        echo "Duration: " . $recording->DURATION . "\n";
        echo "Recorded At: " . $recording->RECORDED_AT . "\n";
        echo "------------------------\n";
    }
} else {
    echo "No recordings found for registration ID 3\n";
}

// Also check the interview status
$sql = "SELECT INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE REGISTRATIONID = '3'";
$mydb->setQuery($sql);
$interview = $mydb->loadSingleResult();

if ($interview) {
    echo "\nInterview Status: " . $interview->INTERVIEW_STATUS . "\n";
    echo "Interview Results: " . $interview->INTERVIEW_RESULTS . "\n";
    
    // Parse the results to check duration
    if ($interview->INTERVIEW_RESULTS) {
        $results = json_decode($interview->INTERVIEW_RESULTS, true);
        if ($results && isset($results['interview_duration'])) {
            echo "Interview Duration from Results: " . $results['interview_duration'] . " seconds\n";
        }
        if ($results && isset($results['overall_score'])) {
            echo "Overall Score: " . $results['overall_score'] . "\n";
        }
    }
} else {
    echo "No interview found for registration ID 3\n";
}
?>