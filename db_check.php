<?php
// Database connection test
require_once('include/config.php');

echo "<h1>Database Connection Test</h1>";

$host = server;
$username = user;
$password = pass;
$database = database_name;
$port = mysql_port;

echo "<p>Connecting to database: $host:$port, database: $database</p>";

$conn = mysqli_connect($host, $username, $password, $database, $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "<p style='color: green;'>✅ Database connection successful</p>";

// Check tblinterviewrecordings table
echo "<h2>tblinterviewrecordings Table</h2>";
$result = mysqli_query($conn, "DESCRIBE tblinterviewrecordings");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
}

// Check some sample data
echo "<h2>Sample Recordings</h2>";
$result = mysqli_query($conn, "SELECT * FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Registration ID</th><th>File Path</th><th>Duration</th><th>Recorded At</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['RECORDING_ID'] . "</td>";
        echo "<td>" . $row['REGISTRATIONID'] . "</td>";
        echo "<td>" . $row['FILE_PATH'] . "</td>";
        echo "<td>" . $row['DURATION'] . "s</td>";
        echo "<td>" . $row['RECORDED_AT'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
}

// Check if files exist
echo "<h2>File Existence Check</h2>";
$result = mysqli_query($conn, "SELECT FILE_PATH FROM tblinterviewrecordings ORDER BY RECORDED_AT DESC LIMIT 5");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $file_path = $row['FILE_PATH'];
        $file_exists = file_exists($file_path) ? '✅ Exists' : '❌ Not Found';
        $file_size = file_exists($file_path) ? round(filesize($file_path) / 1024, 2) . ' KB' : 'N/A';
        echo "<p><strong>$file_path</strong>: $file_exists ($file_size)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>