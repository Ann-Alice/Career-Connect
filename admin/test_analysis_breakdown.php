<?php
require_once('../include/initialize.php');

// Simulate admin login for testing
if(!isset($_SESSION['ADMIN_USERID'])){
    $_SESSION['ADMIN_USERID'] = 1;
    $_SESSION['ADMIN_USERNAME'] = 'test_admin';
    $_SESSION['ADMIN_ROLE'] = 'Administrator';
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>AI Analysis Breakdown Test</title>
    <meta charset='UTF-8'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
    <link rel='stylesheet' href='interview-results.css'>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; }
        .analysis-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .progress { overflow: hidden; background: #f8f9fa; border-radius: 10px; height: 10px; margin-top: 5px; }
        .progress-bar { transition: width 1.5s ease-in-out; height: 100%; border-radius: 10px; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
<div class='container'>
    <h1><i class='fas fa-brain'></i> AI Analysis Breakdown Test</h1>
    <p>This test verifies that the AI analysis breakdown section is working correctly.</p>";

// Sample data for testing
$facial_expression_score = 82;
$body_movement_score = 78;
$voice_recognition_score = 85;
$speech_clarity_score = 88;

$facial_data = [
    'confidence_score' => 82,
    'eye_contact_score' => 85,
    'expression_balance' => 80
];

$movement_data = [
    'posture_score' => 87,
    'gesture_score' => 69
];

$speech_data = [
    'clarity_score' => 88,
    'confidence' => 85,
    'pace_score' => 82
];

echo "
<div class='analysis-card'>
    <h2 style='color: #1976d2; margin-top: 0;'>
        <i class='fas fa-chart-bar'></i> AI Interview Analysis Breakdown
    </h2>
    
    <div class='row'>
        <!-- Facial Expression -->
        <div class='col-md-6'>
            <div class='analysis-card' style='border-left: 4px solid #9c27b0;'>
                <h3 style='color: #9c27b0; margin-top: 0;'>
                    <i class='fas fa-smile'></i> Facial Expression
                </h3>
                <div>
                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;'>
                        <span><strong>Expression Score:</strong></span>
                        <span style='font-weight: bold; font-size: 1.2em;'>{$facial_expression_score}%</span>
                    </div>
                    <div class='progress'>
                        <div class='progress-bar' style='width: {$facial_expression_score}%; background: linear-gradient(90deg, #9c27b0, #e91e63);'></div>
                    </div>
                    <div style='margin-top: 10px; font-size: 0.9em;'>
                        <p><i class='fas fa-eye' style='margin-right: 5px; color: #9c27b0;'></i> <strong>Eye Contact:</strong> {$facial_data['eye_contact_score']}%</p>
                        <p><i class='fas fa-balance-scale' style='margin-right: 5px; color: #9c27b0;'></i> <strong>Expression Balance:</strong> {$facial_data['expression_balance']}%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Body Movement -->
        <div class='col-md-6'>
            <div class='analysis-card' style='border-left: 4px solid #ff9800;'>
                <h3 style='color: #ff9800; margin-top: 0;'>
                    <i class='fas fa-male'></i> Body Movement
                </h3>
                <div>
                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;'>
                        <span><strong>Movement Score:</strong></span>
                        <span style='font-weight: bold; font-size: 1.2em;'>{$body_movement_score}%</span>
                    </div>
                    <div class='progress'>
                        <div class='progress-bar' style='width: {$body_movement_score}%; background: linear-gradient(90deg, #ff9800, #ff5722);'></div>
                    </div>
                    <div style='margin-top: 10px; font-size: 0.9em;'>
                        <p><i class='fas fa-user' style='margin-right: 5px; color: #ff9800;'></i> <strong>Posture:</strong> {$movement_data['posture_score']}%</p>
                        <p><i class='fas fa-hand-paper' style='margin-right: 5px; color: #ff9800;'></i> <strong>Gestures:</strong> {$movement_data['gesture_score']}%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Voice Recognition -->
        <div class='col-md-6'>
            <div class='analysis-card' style='border-left: 4px solid #4caf50;'>
                <h3 style='color: #4caf50; margin-top: 0;'>
                    <i class='fas fa-microphone'></i> Voice Recognition
                </h3>
                <div>
                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;'>
                        <span><strong>Voice Score:</strong></span>
                        <span style='font-weight: bold; font-size: 1.2em;'>{$voice_recognition_score}%</span>
                    </div>
                    <div class='progress'>
                        <div class='progress-bar' style='width: {$voice_recognition_score}%; background: linear-gradient(90deg, #4caf50, #8bc34a);'></div>
                    </div>
                    <div style='margin-top: 10px; font-size: 0.9em;'>
                        <p><i class='fas fa-tachometer-alt' style='margin-right: 5px; color: #4caf50;'></i> <strong>Pace Control:</strong> {$speech_data['pace_score']}%</p>
                        <p><i class='fas fa-volume-up' style='margin-right: 5px; color: #4caf50;'></i> <strong>Confidence:</strong> {$speech_data['confidence']}%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Speech Clarity -->
        <div class='col-md-6'>
            <div class='analysis-card' style='border-left: 4px solid #2196f3;'>
                <h3 style='color: #2196f3; margin-top: 0;'>
                    <i class='fas fa-comments'></i> Speech Clarity
                </h3>
                <div>
                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;'>
                        <span><strong>Clarity Score:</strong></span>
                        <span style='font-weight: bold; font-size: 1.2em;'>{$speech_clarity_score}%</span>
                    </div>
                    <div class='progress'>
                        <div class='progress-bar' style='width: {$speech_clarity_score}%; background: linear-gradient(90deg, #2196f3, #03a9f4);'></div>
                    </div>
                    <div style='margin-top: 10px; font-size: 0.9em;'>
                        <p><i class='fas fa-align-left' style='margin-right: 5px; color: #2196f3;'></i> <strong>Articulation:</strong> {$speech_data['clarity_score']}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Overall AI Score -->
    <div class='row' style='margin-top: 20px;'>
        <div class='col-md-12'>
            <div class='analysis-card' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center;'>
                <h2 style='margin-top: 0; margin-bottom: 15px;'>
                    <i class='fas fa-star'></i> Overall AI Assessment Score
                </h2>
                <div style='font-size: 3rem; font-weight: 800; text-align: center; text-shadow: 0 4px 8px rgba(102,126,234,0.3);'>
                    83%
                </div>
                <p style='margin: 0; opacity: 0.9;'>
                    This score represents a comprehensive evaluation of the candidate's interview performance
                </p>
            </div>
        </div>
    </div>
</div>

<div style='text-align: center; margin-top: 30px;'>
    <a href='interview-results.php' class='btn'><i class='fas fa-arrow-left'></i> Back to Interview Results</a>
    <a href='index.php' class='btn'><i class='fas fa-home'></i> Admin Dashboard</a>
</div>

</div>
</body>
</html>";
?>