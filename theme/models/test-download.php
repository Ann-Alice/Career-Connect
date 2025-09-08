<?php
$url = 'https://www.w3.org/TR/PNG/iso_8859-1.txt'; // Small public text file
$target = __DIR__ . '/test.txt';

$ch = curl_init($url);
$fp = fopen($target, 'wb');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Download successful!';
}
curl_close($ch);
fclose($fp); 