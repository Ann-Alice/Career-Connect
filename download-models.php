<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create models directory if it doesn't exist
$modelsDir = __DIR__ . '/theme/models';
if (!file_exists($modelsDir)) {
    mkdir($modelsDir, 0777, true);
}

// Complete list of model files to download
$modelFiles = [
    'tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2',
    'face_expression_model-weights_manifest.json',
    'face_expression_model-shard1'
];

$baseUrl = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';

foreach ($modelFiles as $file) {
    $url = $baseUrl . $file;
    $targetPath = $modelsDir . '/' . $file;
    echo "Attempting to download: $url\n";
    $ch = curl_init($url);
    $fp = fopen($targetPath, 'wb');
    if (!$fp) {
        echo "Failed to open file for writing: $targetPath\n";
        continue;
    }
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    if (curl_errno($ch)) {
        echo "Error downloading $file: " . curl_error($ch) . "\n";
    } else {
        echo "Successfully downloaded $file\n";
    }
    curl_close($ch);
    fclose($fp);
}
echo "Script complete.\n";
?> 