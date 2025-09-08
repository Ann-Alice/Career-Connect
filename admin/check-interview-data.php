<?php
// Simple script to check interview data structure
echo "Checking interview data structure...\n";

// Database connection (simplified)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'erisdb';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get interview data
$sql = "SELECT REGISTRATIONID, INTERVIEW_RESULTS FROM tbljobregistration 
        WHERE INTERVIEW_RESULTS IS NOT NULL AND INTERVIEW_RESULTS != '' AND INTERVIEW_RESULTS != 'null' 
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Registration ID: " . $row['REGISTRATIONID'] . "\n";
    echo "INTERVIEW_RESULTS JSON:\n";
    echo $row['INTERVIEW_RESULTS'] . "\n\n";
    
    // Decode and display structure
    $data = json_decode($row['INTERVIEW_RESULTS'], true);
    echo "Decoded structure:\n";
    print_r($data);
} else {
    echo "No interview data found\n";
}

$conn->close();
?>