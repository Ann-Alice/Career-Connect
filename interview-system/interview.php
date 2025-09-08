<?php
require_once 'config.php';
require_once 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $candidate_name = $_POST['candidate_name'];
    $candidate_email = $_POST['candidate_email'];
    $position_id = $_POST['position_id'];
    
    // Store candidate info in session
    $_SESSION['candidate'] = [
        'name' => $candidate_name,
        'email' => $candidate_email,
        'position_id' => $position_id,
        'start_time' => time()
    ];
}

// Get interview questions based on position
$questions = getInterviewQuestions($_SESSION['candidate']['position_id'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Interview System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.0.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        
        #video-container {
            position: relative;
            margin: 20px 0;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #video {
            width: 100%;
            max-width: 800px;
            display: block;
        }
        
        #canvas {
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .question {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .question h3 {
            margin-top: 0;
            color: #333;
        }
        
        .controls {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #0056b3;
        }
        
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .status {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        
        .status.recording {
            background: #ffebee;
            color: #c62828;
        }
        
        .status.ready {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <h1>AI-Powered Interview</h1>
    
    <?php if (!isset($_SESSION['candidate'])): ?>
        <!-- Candidate Information Form -->
        <form method="POST" class="candidate-form">
            <div>
                <label>Full Name:</label>
                <input type="text" name="candidate_name" required>
            </div>
            <div>
                <label>Email:</label>
                <input type="email" name="candidate_email" required>
            </div>
            <div>
                <label>Position:</label>
                <select name="position_id" required>
                    <option value="1">Software Developer</option>
                    <option value="2">Sales Manager</option>
                    <option value="3">Customer Support</option>
                </select>
            </div>
            <button type="submit">Start Interview</button>
        </form>
    <?php else: ?>
        <!-- Interview Interface -->
        <div id="video-container">
            <video id="video" autoplay muted></video>
            <canvas id="canvas"></canvas>
        </div>
        
        <div class="controls">
            <button id="start-btn" onclick="startInterview()">Start Interview</button>
            <button id="complete-btn" onclick="completeInterview()" disabled>Complete Interview</button>
        </div>
        
        <div id="questions">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question" data-question-id="<?= $question['id'] ?>">
                    <h3>Question <?= $index + 1 ?>:</h3>
                    <p><?= htmlspecialchars($question['text']) ?></p>
                    <div class="controls">
                        <button onclick="startAnswer(<?= $question['id'] ?>)" class="start-answer-btn">Start Answer</button>
                        <button onclick="stopAnswer(<?= $question['id'] ?>)" class="stop-answer-btn" disabled>Stop Answer</button>
                    </div>
                    <div id="answer-<?= $question['id'] ?>" class="status"></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <script>
            // Variables to store analysis data
            const analysisData = {
                expressions: {},
                movements: {},
                answers: {},
                timestamps: {}
            };
            
            let mediaRecorder;
            let recordedChunks = [];
            let currentQuestionId = null;
            let isInterviewStarted = false;
            
            // Load face-api models
            async function loadModels() {
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                await faceapi.nets.faceExpressionNet.loadFromUri('/models');
                
                startVideo();
            }
            
            // Start video stream
            function startVideo() {
                navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                    .then(stream => {
                        const video = document.getElementById('video');
                        video.srcObject = stream;
                    })
                    .catch(err => console.error(err));
            }
            
            // Start interview
            function startInterview() {
                isInterviewStarted = true;
                document.getElementById('start-btn').disabled = true;
                document.getElementById('complete-btn').disabled = false;
                
                // Setup media recorder
                const stream = document.getElementById('video').srcObject;
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.ondataavailable = handleDataAvailable;
                mediaRecorder.start(1000); // Collect data every second
                
                // Start face detection
                detectFaces();
            }
            
            // Detect faces and expressions
            function detectFaces() {
                const video = document.getElementById('video');
                const canvas = document.getElementById('canvas');
                const displaySize = { width: video.width, height: video.height };
                
                faceapi.matchDimensions(canvas, displaySize);
                
                setInterval(async () => {
                    if (!isInterviewStarted) return;
                    
                    const detections = await faceapi.detectAllFaces(video, 
                        new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceExpressions();
                    
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                    faceapi.draw.drawFaceExpressions(canvas, resizedDetections);
                    
                    // Store expression data if answering a question
                    if (currentQuestionId && detections[0]) {
                        if (!analysisData.expressions[currentQuestionId]) {
                            analysisData.expressions[currentQuestionId] = [];
                        }
                        analysisData.expressions[currentQuestionId].push(detections[0].expressions);
                    }
                }, 100);
            }
            
            // Handle recorded data
            function handleDataAvailable(event) {
                if (event.data.size > 0) {
                    recordedChunks.push(event.data);
                    
                    // Store movement data if answering a question
                    if (currentQuestionId) {
                        if (!analysisData.movements[currentQuestionId]) {
                            analysisData.movements[currentQuestionId] = 0;
                        }
                        // Simple movement detection (would be enhanced with proper AI)
                        analysisData.movements[currentQuestionId] += 5; // Placeholder
                    }
                }
            }
            
            // Start answering a question
            function startAnswer(questionId) {
                currentQuestionId = questionId;
                analysisData.timestamps[questionId] = {
                    start: new Date().toISOString()
                };
                
                // Update UI
                const answerDiv = document.getElementById(`answer-${questionId}`);
                answerDiv.innerHTML = '<div class="status recording">Recording answer...</div>';
                
                // Enable/disable buttons
                document.querySelectorAll('.start-answer-btn').forEach(btn => btn.disabled = true);
                document.querySelectorAll('.stop-answer-btn').forEach(btn => btn.disabled = true);
                document.querySelector(`[data-question-id="${questionId}"] .stop-answer-btn`).disabled = false;
            }
            
            // Stop answering a question
            function stopAnswer(questionId) {
                currentQuestionId = null;
                analysisData.timestamps[questionId].end = new Date().toISOString();
                
                // For demo, we'll just store a placeholder answer
                analysisData.answers[questionId] = "Candidate's answer recorded.";
                
                // Update UI
                const answerDiv = document.getElementById(`answer-${questionId}`);
                answerDiv.innerHTML = '<div class="status ready">Answer recorded.</div>';
                
                // Enable/disable buttons
                document.querySelectorAll('.start-answer-btn').forEach(btn => btn.disabled = false);
                document.querySelectorAll('.stop-answer-btn').forEach(btn => btn.disabled = true);
            }
            
            // Complete the interview
            async function completeInterview() {
                // Stop recording
                mediaRecorder.stop();
                
                // Process the video
                const blob = new Blob(recordedChunks, { type: 'video/webm' });
                const formData = new FormData();
                formData.append('video', blob, 'interview.webm');
                formData.append('analysisData', JSON.stringify(analysisData));
                formData.append('candidateData', JSON.stringify(<?= json_encode($_SESSION['candidate']) ?>));
                
                // Send to server for processing
                const response = await fetch('assessment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Interview completed! Assessment is being processed.');
                    window.location.href = 'report-generator.php?session_id=' + result.session_id;
                } else {
                    alert('Error processing interview: ' + result.error);
                }
            }
            
            // Initialize
            window.onload = loadModels;
        </script>
    <?php endif; ?>
</body>
</html> 