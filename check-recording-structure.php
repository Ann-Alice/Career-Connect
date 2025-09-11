<?php
require_once('include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    echo "Unauthorized access";
    exit;
}

echo "<h1>Interview Recording Structure Check</h1>";

// Check tblinterviewrecordings table structure
echo "<h2>tblinterviewrecordings Table Structure</h2>";
$sql = "DESCRIBE tblinterviewrecordings";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row->Field . "</td>";
        echo "<td>" . $row->Type . "</td>";
        echo "<td>" . $row->Null . "</td>";
        echo "<td>" . $row->Key . "</td>";
        echo "<td>" . $row->Default . "</td>";
        echo "<td>" . $row->Extra . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error: Could not retrieve table structure</p>";
}

// Check some sample recordings
echo "<h2>Sample Recordings</h2>";
$sql = "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 10";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

if ($recordings) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Registration ID</th><th>File Path</th><th>Duration</th><th>Recorded At</th><th>File Exists</th><th>File Size</th></tr>";
    foreach ($recordings as $recording) {
        $file_exists = file_exists($recording->FILE_PATH) ? 'Yes' : 'No';
        $file_size = file_exists($recording->FILE_PATH) ? round(filesize($recording->FILE_PATH) / 1024, 2) . ' KB' : 'N/A';
        
        echo "<tr>";
        echo "<td>" . $recording->RECORDING_ID . "</td>";
        echo "<td>" . $recording->REGISTRATIONID . "</td>";
        echo "<td>" . $recording->FILE_PATH . "</td>";
        echo "<td>" . $recording->DURATION . "s</td>";
        echo "<td>" . $recording->RECORDED_AT . "</td>";
        echo "<td>" . $file_exists . "</td>";
        echo "<td>" . $file_size . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No recordings found in database</p>";
}

// Check tblinterviewvideos table structure
echo "<h2>tblinterviewvideos Table Structure</h2>";
$sql = "DESCRIBE tblinterviewvideos";
$mydb->setQuery($sql);
$result = $mydb->loadResultList();

if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row->Field . "</td>";
        echo "<td>" . $row->Type . "</td>";
        echo "<td>" . $row->Null . "</td>";
        echo "<td>" . $row->Key . "</td>";
        echo "<td>" . $row->Default . "</td>";
        echo "<td>" . $row->Extra . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>tblinterviewvideos table not found</p>";
}

// Check some sample videos
echo "<h2>Sample Videos</h2>";
$sql = "SELECT * FROM tblinterviewvideos ORDER BY CREATED_AT DESC LIMIT 10";
$mydb->setQuery($sql);
$videos = $mydb->loadResultList();

if ($videos) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Registration ID</th><th>Video Path</th><th>Created At</th><th>File Exists</th><th>File Size</th></tr>";
    foreach ($videos as $video) {
        // Try different possible paths
        $possible_paths = [
            "uploads/interviews/consolidated/" . $video->VIDEO_PATH,
            "uploads/interviews/" . $video->VIDEO_PATH,
            "../uploads/interviews/consolidated/" . $video->VIDEO_PATH,
            "../uploads/interviews/" . $video->VIDEO_PATH
        ];
        
        $file_exists = 'No';
        $file_size = 'N/A';
        $actual_path = '';
        
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $file_exists = 'Yes';
                $file_size = round(filesize($path) / 1024, 2) . ' KB';
                $actual_path = $path;
                break;
            }
        }
        
        echo "<tr>";
        echo "<td>" . $video->VIDEOID . "</td>";
        echo "<td>" . $video->REGISTRATIONID . "</td>";
        echo "<td>" . $video->VIDEO_PATH . "</td>";
        echo "<td>" . $video->CREATED_AT . "</td>";
        echo "<td>" . $file_exists . "</td>";
        echo "<td>" . $file_size . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No videos found in database</p>";
}

echo "<h2>Directory Structure Check</h2>";
$upload_dirs = [
    'uploads/',
    'uploads/interviews/',
    'uploads/interviews/consolidated/',
    'interview-recordings/',
    'recordings/'
];

foreach ($upload_dirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? 'Writable' : 'Not Writable';
        $file_count = count(glob($dir . "*"));
        echo "<p><strong>$dir</strong>: Exists, $writable, $file_count files</p>";
    } else {
        echo "<p><strong>$dir</strong>: Does not exist</p>";
    }
}

?>