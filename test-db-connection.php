<?php
// Simple database connection test
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erisdb";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully<br>";
    
    // Check if tables exist
    $tables = ['tbljobregistration', 'tblinterviewrecordings', 'tblinterviewvideos', 'tblinterviewinvitations'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "Table $table exists<br>";
        } else {
            echo "Table $table does not exist<br>";
        }
    }
    
    // Check columns in tbljobregistration
    $required_columns = [
        'INTERVIEW_RESULTS',
        'INTERVIEW_STATUS',
        'INTERVIEW_COMPLETED_AT',
        'ADMIN_GRADE',
        'GRADED_AT',
        'EMAIL_SENT',
        'EMAIL_SENT_AT'
    ];
    
    foreach ($required_columns as $column) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM tbljobregistration LIKE ?");
        $stmt->execute([$column]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "Column $column exists<br>";
        } else {
            echo "Column $column does not exist<br>";
        }
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>