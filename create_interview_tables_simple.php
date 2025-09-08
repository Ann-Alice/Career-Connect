<?php
echo "<h2>🎥 Creating Interview System Tables (Simplified)</h2>";

$conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);

if ($conn) {
    echo "<p style='color: green;'>✅ Connected to database: erisdb</p>";
    
    // Simple table creation queries
    $tables = [
        "tblinterviewvideos" => "CREATE TABLE IF NOT EXISTS tblinterviewvideos (
            VIDEOID INT PRIMARY KEY AUTO_INCREMENT,
            REGISTRATIONID INT NOT NULL,
            VIDEO_PATH VARCHAR(255) NOT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblinterviewinvitations" => "CREATE TABLE IF NOT EXISTS tblinterviewinvitations (
            INVITATIONID int(11) NOT NULL AUTO_INCREMENT,
            REGISTRATIONID int(11) NOT NULL,
            APPLICANTID int(11) NOT NULL,
            JOBID int(11) NOT NULL,
            TOKEN varchar(64) NOT NULL,
            EXPIRY_DATE datetime NOT NULL,
            CREATED_AT datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (INVITATIONID),
            UNIQUE KEY TOKEN (TOKEN)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblinterviewrecordings" => "CREATE TABLE IF NOT EXISTS tblinterviewrecordings (
            RECORDINGID int(11) NOT NULL AUTO_INCREMENT,
            REGISTRATIONID int(11) NOT NULL,
            QUESTION_NUMBER int(11) NOT NULL,
            FILE_PATH varchar(255) NOT NULL,
            DURATION float NOT NULL,
            RECORDED_AT datetime NOT NULL,
            CONVERSATION_TURN int(11) DEFAULT NULL,
            QUESTION_TYPE varchar(50) DEFAULT 'initial_answer',
            PRIMARY KEY (RECORDINGID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "tblmessages" => "CREATE TABLE IF NOT EXISTS tblmessages (
            MESSAGEID int(11) NOT NULL AUTO_INCREMENT,
            RECEIVERID int(11) NOT NULL,
            SENDERID int(11) NOT NULL,
            SUBJECT varchar(255) NOT NULL,
            MESSAGE text NOT NULL,
            DATESENT datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            STATUS varchar(20) NOT NULL DEFAULT 'Unread',
            PRIMARY KEY (MESSAGEID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
    
    $created = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($tables as $name => $sql) {
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ Table '$name' created successfully</p>";
            $created++;
        } else {
            $error_msg = mysqli_error($conn);
            if (strpos($error_msg, 'already exists') !== false) {
                echo "<p style='color: blue;'>ℹ️ Table '$name' already exists</p>";
                $skipped++;
            } else {
                echo "<p style='color: red;'>❌ Table '$name': $error_msg</p>";
                $errors++;
            }
        }
    }
    
    // Add columns to existing tables
    $alter_queries = [
        "ALTER TABLE tbljobregistration ADD COLUMN IF NOT EXISTS INTERVIEW_RESULTS TEXT",
        "ALTER TABLE tbljobregistration ADD COLUMN IF NOT EXISTS INTERVIEW_STATUS varchar(20) DEFAULT NULL",
        "ALTER TABLE tbljobregistration ADD COLUMN IF NOT EXISTS INTERVIEW_COMPLETED_AT datetime DEFAULT NULL"
    ];
    
    echo "<h3>🔧 Adding Interview Columns:</h3>";
    
    foreach ($alter_queries as $sql) {
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>✅ Column added successfully</p>";
        } else {
            $error_msg = mysqli_error($conn);
            if (strpos($error_msg, 'already exists') !== false) {
                echo "<p style='color: blue;'>ℹ️ Column already exists</p>";
            } else {
                echo "<p style='color: red;'>❌ Column addition failed: $error_msg</p>";
            }
        }
    }
    
    echo "<h3>📊 Summary:</h3>";
    echo "<ul>";
    echo "<li>✅ Tables created: $created</li>";
    echo "<li>ℹ️ Tables skipped (already exist): $skipped</li>";
    if ($errors > 0) {
        echo "<li>❌ Errors: $errors</li>";
    }
    echo "</ul>";
    
    // Verify final status
    $result = mysqli_query($conn, "SHOW TABLES");
    $table_count = mysqli_num_rows($result);
    echo "<p><strong>Total tables in database:</strong> $table_count</p>";
    
    if ($table_count > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        
        echo "<h3>🎉 Interview System Ready!</h3>";
        echo "<p>Your CAREER CONNECT application with interview system is now complete!</p>";
        echo "<p><a href='index.php'>🚀 Test your application now</a></p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?> 