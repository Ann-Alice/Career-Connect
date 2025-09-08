<?php
require_once("include/initialize.php");

echo "<h2>Messaging System Diagnostic</h2>";

// Check if messages table exists
$sql = "SHOW TABLES LIKE 'tblmessages'";
$mydb->setQuery($sql);
$result = $mydb->executeQuery();
echo "<h3>1. Checking Messages Table</h3>";
if ($result && mysqli_num_rows($result) > 0) {
    echo "✅ Messages table exists<br>";
    
    // Check table structure
    $sql = "DESCRIBE tblmessages";
    $mydb->setQuery($sql);
    $structure = $mydb->loadResultList();
    echo "<h4>Table Structure:</h4>";
    echo "<pre>";
    foreach ($structure as $column) {
        echo "{$column->Field}: {$column->Type}\n";
    }
    echo "</pre>";
} else {
    echo "❌ Messages table does not exist<br>";
}

// Check if admin table exists and has records
$sql = "SELECT COUNT(*) as count FROM tbladmin";
$mydb->setQuery($sql);
$adminCount = $mydb->loadSingleResult();
echo "<h3>2. Checking Admin Table</h3>";
if ($adminCount) {
    echo "✅ Admin table exists with {$adminCount->count} records<br>";
} else {
    echo "❌ Admin table might not exist or is empty<br>";
}

// Check if applicants table exists and has records
$sql = "SELECT COUNT(*) as count FROM tblapplicants";
$mydb->setQuery($sql);
$applicantCount = $mydb->loadSingleResult();
echo "<h3>3. Checking Applicants Table</h3>";
if ($applicantCount) {
    echo "✅ Applicants table exists with {$applicantCount->count} records<br>";
} else {
    echo "❌ Applicants table might not exist or is empty<br>";
}

// Check for any messages
$sql = "SELECT COUNT(*) as count FROM tblmessages";
$mydb->setQuery($sql);
$messageCount = $mydb->loadSingleResult();
echo "<h3>4. Checking Messages</h3>";
if ($messageCount && $messageCount->count > 0) {
    echo "✅ Found {$messageCount->count} messages in the database<br>";
    
    // Show recent messages
    $sql = "SELECT m.*, a.FULLNAME as SENDER_NAME, ap.FNAME as RECEIVER_NAME 
            FROM tblmessages m 
            LEFT JOIN tbladmin a ON m.SENDERID = a.ADMINID 
            LEFT JOIN tblapplicants ap ON m.RECEIVERID = ap.APPLICANTID 
            ORDER BY m.DATESENT DESC LIMIT 5";
    $mydb->setQuery($sql);
    $messages = $mydb->loadResultList();
    
    echo "<h4>Recent Messages:</h4>";
    echo "<pre>";
    foreach ($messages as $message) {
        echo "Message ID: {$message->MESSAGEID}\n";
        echo "From: {$message->SENDER_NAME} (ID: {$message->SENDERID})\n";
        echo "To: {$message->RECEIVER_NAME} (ID: {$message->RECEIVERID})\n";
        echo "Subject: {$message->SUBJECT}\n";
        echo "Date: {$message->DATESENT}\n";
        echo "Status: {$message->STATUS}\n";
        echo "----------------------------------------\n";
    }
    echo "</pre>";
} else {
    echo "❌ No messages found in the database<br>";
}

// Check for any errors in the error log
echo "<h3>5. Recent Error Log</h3>";
$errorLog = file_exists("logs/error.log") ? file_get_contents("logs/error.log") : "No error log found";
echo "<pre>" . htmlspecialchars(substr($errorLog, -1000)) . "</pre>";
?> 