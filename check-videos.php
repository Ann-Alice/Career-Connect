<?php
require_once('include/initialize.php');

$reg_id = 3; // James Carta
echo "Checking video files for Registration ID: $reg_id\n\n";

echo "=== tblinterviewvideos ===\n";
$sql = "SELECT * FROM tblinterviewvideos WHERE REGISTRATIONID = $reg_id";
$mydb->setQuery($sql);
$videos = $mydb->loadResultList();

if ($videos) {
    foreach ($videos as $v) {
        echo "Video: {$v->VIDEO_PATH}\n";
        $path1 = "uploads/interviews/consolidated/{$v->VIDEO_PATH}";
        $path2 = "uploads/interviews/{$v->VIDEO_PATH}";
        echo "Path1: $path1 - " . (file_exists($path1) ? 'EXISTS (' . filesize($path1) . ' bytes)' : 'NOT FOUND') . "\n";
        echo "Path2: $path2 - " . (file_exists($path2) ? 'EXISTS (' . filesize($path2) . ' bytes)' : 'NOT FOUND') . "\n";
    }
} else {
    echo "No videos found in tblinterviewvideos\n";
}

echo "\n=== tblinterviewrecordings ===\n";
$sql = "SELECT * FROM tblinterviewrecordings WHERE REGISTRATIONID = $reg_id";
$mydb->setQuery($sql);
$recordings = $mydb->loadResultList();

if ($recordings) {
    foreach ($recordings as $r) {
        echo "Recording: {$r->FILE_PATH} - " . (file_exists($r->FILE_PATH) ? 'EXISTS (' . filesize($r->FILE_PATH) . ' bytes)' : 'NOT FOUND') . "\n";
    }
} else {
    echo "No recordings found in tblinterviewrecordings\n";
}

echo "\n=== Direct Download Links ===\n";
echo "Download URL: http://localhost/eris/admin/download-recording.php?id=$reg_id\n";
?>