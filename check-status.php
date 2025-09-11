<?php
require_once('include/initialize.php');

echo "<h2>Checking Registration ID 3 Status</h2>";

// Check the status of registration ID 3
$sql = "SELECT REGISTRATIONID, INTERVIEW_STATUS, INTERVIEW_RESULTS FROM tbljobregistration WHERE REGISTRATIONID = 3";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result) {
    echo "<h3>Job Registration Data:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    // Check interview results
    if ($result->INTERVIEW_RESULTS) {
        $interview_data = json_decode($result->INTERVIEW_RESULTS, true);
        echo "<h3>Interview Results Data:</h3>";
        echo "<pre>";
        print_r($interview_data);
        echo "</pre>";
        
        // Check interview duration
        if (isset($interview_data['interview_duration'])) {
            echo "<h3>Interview Duration:</h3>";
            echo "<p>" . $interview_data['interview_duration'] . " seconds (" . ($interview_data['interview_duration'] / 60) . " minutes)</p>";
        }
    }
} else {
    echo "<p>No data found for registration ID 3</p>";
}

// Check recordings for registration ID 3
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = 3 ORDER BY RECORDED_AT DESC";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

echo "<h3>Interview Recordings:</h3>";
if ($recordings) {
    echo "<pre>";
    print_r($recordings);
    echo "</pre>";
} else {
    echo "<p>No recordings found for registration ID 3</p>";
}

// Check consolidated videos for registration ID 3
$sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = 3 ORDER BY CREATED_AT DESC";
$mydb->setQuery($sql);
$videos = $mydb->loadResultList();

echo "<h3>Consolidated Videos:</h3>";
if ($videos) {
    echo "<pre>";
    print_r($videos);
    echo "</pre>";
} else {
    echo "<p>No consolidated videos found for registration ID 3</p>";
}
?>