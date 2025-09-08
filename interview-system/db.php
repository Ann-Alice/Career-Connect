<?php
require_once 'config.php';

/**
 * Connect to database
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    
    return $conn;
}

/**
 * Initialize database tables
 */
function initializeDatabase() {
    $conn = getDBConnection();
    
    // Create interview_questions table
    $conn->query("
        CREATE TABLE IF NOT EXISTS interview_questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            position_id INT NOT NULL,
            text TEXT NOT NULL,
            expected_keywords TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create interview_sessions table
    $conn->query("
        CREATE TABLE IF NOT EXISTS interview_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            candidate_name VARCHAR(255) NOT NULL,
            candidate_email VARCHAR(255) NOT NULL,
            position_id INT NOT NULL,
            video_path VARCHAR(255) NOT NULL,
            expression_score FLOAT,
            movement_score FLOAT,
            accuracy_score FLOAT,
            overall_score FLOAT,
            passed BOOLEAN,
            start_time INT,
            end_time INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

/**
 * Get interview questions for a position
 */
function getInterviewQuestions($positionId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, text, expected_keywords FROM interview_questions WHERE position_id = ?");
    $stmt->bind_param("i", $positionId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    
    return $questions;
}

/**
 * Save interview session data
 */
function saveInterviewSession($data) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        INSERT INTO interview_sessions (
            candidate_name, candidate_email, position_id, video_path,
            expression_score, movement_score, accuracy_score, overall_score,
            passed, start_time, end_time
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("ssisddddiii", 
        $data['candidate_name'],
        $data['candidate_email'],
        $data['position_id'],
        $data['video_path'],
        $data['expression_score'],
        $data['movement_score'],
        $data['accuracy_score'],
        $data['overall_score'],
        $data['passed'],
        $data['start_time'],
        $data['end_time']
    );
    
    $stmt->execute();
    return $conn->insert_id;
}

/**
 * Get interview session data
 */
function getInterviewSession($sessionId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM interview_sessions WHERE id = ?");
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Initialize database on first run
initializeDatabase();
?> 