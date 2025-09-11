<?php
require_once('include/config.php');
require_once('include/initialize.php');

echo "Web Root: " . web_root . "\n";

// Test accessing the upload script through the correct path
$test_url = "http://" . $_SERVER['HTTP_HOST'] . web_root . "upload-recording-enhanced.php";
echo "Test URL: " . $test_url . "\n";

// Check if the file exists
$upload_script_path = __DIR__ . "/upload-recording-enhanced.php";
echo "Upload script exists: " . (file_exists($upload_script_path) ? "Yes" : "No") . "\n";

// Check the database for any recent recordings
$sql = "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

echo "\nRecent recordings:\n";
if ($recordings) {
    foreach ($recordings as $recording) {
        echo "- ID: " . $recording->RECORDING_ID . ", Registration: " . $recording->REGISTRATIONID . ", Date: " . $recording->RECORDED_AT . "\n";
    }
} else {
    echo "No recordings found.\n";
}
?>