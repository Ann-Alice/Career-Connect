<?php
// Simple test to verify web access is working
echo "<h1>✅ Web Access Test Successful!</h1>";
echo "<p>If you can see this page, your XAMPP setup is working correctly.</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<p><a href='fix-transcript-column.php'>🔧 Run TRANSCRIPT Column Fix</a></p>";
echo "<p><a href='transcript-fix-summary.html'>📋 View Fix Summary</a></p>";
echo "<p><a href='../setup-interview-tables.php'>🛠️ Setup All Tables</a></p>";
?>