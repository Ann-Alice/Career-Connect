<?php
// Create a proper test using cURL to simulate an actual HTTP POST request

// Create a test file
$test_content = "This is a test recording file content";
$test_file = tempnam(sys_get_temp_dir(), 'test_recording');
file_put_contents($test_file, $test_content);

// URL of the upload script (using localhost and proper path)
$url = 'http://localhost/Career%20Connect/Career-Connect/upload-recording-enhanced.php';

// Create POST data
$post_data = [
    'conversationTurn' => 1,
    'questionType' => 'essential_answer',
    'currentEssentialQuestion' => 0,
    'duration' => 15.5,
    'token' => 'test_token',
    'registrationId' => '3',
    'transcript' => 'This is a test transcript',
    'test' => 'true', // Skip token validation for test
    'video' => new CURLFile($test_file, 'video/webm', 'test_recording.webm')
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: multipart/form-data'
]);

// Execute the request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Output results
echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}

// Clean up
unlink($test_file);
?>