<?php
// Setup interview database tables with better port handling
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Setting up Interview Database Tables</h1>";

try {
    // Database connection with multiple port attempts
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'erisdb';
    
    // Try different ports
    $ports = [4306, 4306, 3307, 3308];
    $conn = null;
    
    foreach ($ports as $port) {
        echo "Trying to connect to MySQL on port $port...<br>";
        $conn = @mysqli_connect($host, $username, $password, $database, $port);
        if ($conn) {
            echo "✅ Database connection successful on port $port<br>";
            break;
        } else {
            echo "❌ Failed to connect on port $port: " . mysqli_connect_error() . "<br>";
        }
    }
    
    if (!$conn) {
        throw new Exception('Could not connect to MySQL on any port. Please ensure MySQL is running.');
    }
    
    // Check if tables exist
    echo "<h2>Checking existing tables...</h2>";
    
    $tables = ['tblinterviewrecordings', 'tblinterviewinvitations'];
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo "✅ Table $table already exists<br>";
        } else {
            echo "❌ Table $table missing - will create it<br>";
        }
    }
    
    // Create interview recordings table
    echo "<h2>Creating interview recordings table...</h2>";
    $sql = "CREATE TABLE IF NOT EXISTS `tblinterviewrecordings` (
        `RECORDING_ID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` varchar(50) NOT NULL,
        `QUESTION_NUMBER` int(11) NOT NULL DEFAULT 0,
        `FILE_PATH` varchar(500) NOT NULL,
        `DURATION` decimal(10,3) NOT NULL DEFAULT 0.000,
        `RECORDED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `CONVERSATION_TURN` int(11) NOT NULL DEFAULT 0,
        `QUESTION_TYPE` varchar(50) NOT NULL DEFAULT 'initial_answer',
        `TRANSCRIPT` text,
        PRIMARY KEY (`RECORDING_ID`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`),
        KEY `RECORDED_AT` (`RECORDED_AT`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $sql)) {
        echo "✅ Interview recordings table created successfully<br>";
    } else {
        echo "❌ Error creating interview recordings table: " . mysqli_error($conn) . "<br>";
    }
    
    // Add TRANSCRIPT column to existing table if it doesn't exist
    echo "<h2>Checking for TRANSCRIPT column...</h2>";
    $sql = "SHOW COLUMNS FROM `tblinterviewrecordings` LIKE 'TRANSCRIPT'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "Adding TRANSCRIPT column to existing table...<br>";
        $sql = "ALTER TABLE `tblinterviewrecordings` ADD COLUMN `TRANSCRIPT` text AFTER `QUESTION_TYPE`";
        if (mysqli_query($conn, $sql)) {
            echo "✅ TRANSCRIPT column added successfully<br>";
        } else {
            echo "❌ Error adding TRANSCRIPT column: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "✅ TRANSCRIPT column already exists<br>";
    }
    
    // Create interview invitations table
    echo "<h2>Creating interview invitations table...</h2>";
    $sql = "CREATE TABLE IF NOT EXISTS `tblinterviewinvitations` (
        `INVITATION_ID` int(11) NOT NULL AUTO_INCREMENT,
        `REGISTRATIONID` varchar(50) NOT NULL,
        `JOBID` varchar(50) NOT NULL,
        `TOKEN` varchar(255) NOT NULL,
        `EXPIRY_DATE` timestamp NOT NULL,
        `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `STATUS` enum('pending','completed','expired') NOT NULL DEFAULT 'pending',
        PRIMARY KEY (`INVITATION_ID`),
        UNIQUE KEY `TOKEN` (`TOKEN`),
        KEY `REGISTRATIONID` (`REGISTRATIONID`),
        KEY `EXPIRY_DATE` (`EXPIRY_DATE`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $sql)) {
        echo "✅ Interview invitations table created successfully<br>";
    } else {
        echo "❌ Error creating interview invitations table: " . mysqli_error($conn) . "<br>";
    }
    
    // Verify tables were created
    echo "<h2>Verifying tables...</h2>";
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo "✅ Table $table exists and is ready<br>";
            
            // Show table structure
            $sql = "DESCRIBE $table";
            $result = mysqli_query($conn, $sql);
            echo "<h3>Structure of $table:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table><br>";
        } else {
            echo "❌ Table $table still missing<br>";
        }
    }
    
    mysqli_close($conn);
    echo "<h2>Setup Complete!</h2>";
    echo "<p>✅ Interview database tables have been created successfully.</p>";
    echo "<p>You can now test the interview system again.</p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running</li>";
    echo "<li>Check if XAMPP Control Panel is open and MySQL is started</li>";
    echo "<li>Verify the database 'erisdb' exists</li>";
    echo "<li>Try starting MySQL manually: C:\\xampp\\mysql\\bin\\mysqld.exe --console</li>";
    echo "</ul>";
}
?> 