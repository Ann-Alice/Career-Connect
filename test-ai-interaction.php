<?php
// Simple test page for AI interaction
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Interaction Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="theme/css/ai-interview.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="ai-interview-card">
                    <div class="interview-header">
                        <h2>AI Interaction Test</h2>
                        <p class="text-muted">Testing the AI response flow</p>
                    </div>
                    
                    <div class="interview-body">
                        <div class="interview-controls">
                            <button id="testAI" class="btn btn-success">Test AI Interaction</button>
                            <button id="testRecording" class="btn btn-primary">Test Recording</button>
                            <button id="testFullFlow" class="btn btn-warning">Test Full Flow</button>
                        </div>
                        
                        <div id="test-status" class="alert alert-info mt-3" style="display: none;"></div>
                        
                        <div class="interview-chat mt-4">
                            <h3>Test Chat</h3>
                            <div class="chat-container">
                                <div id="test-chat-container" class="chat-messages"></div>
                                <div id="test-chat-status" class="chat-status">Ready for testing</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mock interview configuration for testing
        window.interviewConfig = {
            jobData: {
                title: 'Software Developer',
                description: 'Developing web applications',
                requirements: 'JavaScript, PHP, Teamwork',
                applicant: 'Test Candidate'
            },
            interviewToken: 'test-token-123',
            registrationId: 'test-reg-123'
        };
        
        // Mock functions for testing
        let testJobData = {
            title: 'Software Developer',
            description: 'Developing web applications',
            requirements: 'JavaScript, PHP, Teamwork'
        };
        
        let testCvData = {
            experience: '3 years web development',
            education: 'Computer Science degree',
            skills: 'JavaScript, PHP, React',
            projects: 'E-commerce platform'
        };
        
        let testQuestions = [
            "Hi there! I'm so excited to chat with you today. Could you tell me a bit about yourself and what brings you here?",
            "I'm really curious about your journey! What is it about this position that caught your attention?",
            "I'd love to hear about your strengths! What would you say are your greatest talents?",
            "I really appreciate honesty! What's something you're working on improving?",
            "I'm excited about what you could bring to our team! Why do you think you'd be a great fit for this role?"
        ];
        
        // Mock the necessary functions
        function addChatMessage(sender, message, type = 'user') {
            const chatContainer = document.getElementById('test-chat-container');
            if (!chatContainer) return;
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${type}`;
            messageDiv.innerHTML = `
                <div class="message-content">
                    <strong>${sender}:</strong> ${message}
                </div>
                <div class="message-time">${new Date().toLocaleTimeString()}</div>
            `;
            
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
        
        function updateStatus(message, type) {
            const statusElement = document.getElementById('test-status');
            if (statusElement) {
                statusElement.textContent = message;
                statusElement.className = `alert alert-${type || 'info'} mt-3`;
                statusElement.style.display = 'block';
            }
            console.log(`Status: ${message} (${type})`);
        }
        
        // Test AI response generation (simplified version)
        function generateAIResponse(userAnswer) {
            const answer = userAnswer.toLowerCase();
            let response = '';
            
            if (answer.includes('team') || answer.includes('collaborate')) {
                response = "That's such a great example of teamwork! What's your secret to building strong relationships with team members? I'd love to hear your approach!";
            } else if (answer.includes('problem') || answer.includes('solve')) {
                response = "I love how you approached that problem! What was the most challenging part of that situation, and how did you figure out the best approach? I'd love to hear your thought process!";
            } else if (answer.includes('learn') || answer.includes('grow')) {
                response = "It's wonderful that you learned that! What's something new you're currently working on improving? I love hearing about people's growth journeys!";
            } else if (answer.includes('passion') || answer.includes('love')) {
                response = "Your passion really comes through! What is it about this work that really drives you? I can feel your enthusiasm coming through!";
            } else {
                response = "That's really interesting! Could you tell me a bit more about that? I'd love to hear more details!";
            }
            
            return response;
        }
        
        // Test functions
        function testAIInteraction() {
            console.log('Testing AI interaction...');
            
            const testResponses = [
                "I have experience working with teams and solving problems. I love learning new things and I'm passionate about helping customers.",
                "I worked on a challenging project where we had to solve a complex problem with limited resources.",
                "I'm always learning new technologies and I'm passionate about creating great user experiences."
            ];
            
            testResponses.forEach((response, index) => {
                setTimeout(() => {
                    addChatMessage('You', response, 'user');
                    
                    setTimeout(() => {
                        const aiResponse = generateAIResponse(response);
                        addChatMessage('AI Interviewer', aiResponse, 'ai');
                        updateStatus(`AI interaction test ${index + 1} completed`, 'success');
                    }, 1000);
                }, index * 3000);
            });
        }
        
        function testRecording() {
            console.log('Testing recording functionality...');
            updateStatus('Recording test started - this would normally start video/audio recording', 'info');
            
            // Simulate recording process
            setTimeout(() => {
                updateStatus('Recording in progress...', 'info');
                setTimeout(() => {
                    updateStatus('Recording stopped - this would normally save the recording', 'success');
                }, 2000);
            }, 1000);
        }
        
        function testFullFlow() {
            console.log('Testing full interview flow...');
            updateStatus('Starting full interview flow test...', 'info');
            
            // Simulate the complete flow
            setTimeout(() => {
                addChatMessage('AI Interviewer', "Hi there! I'm so excited to chat with you today. Could you tell me a bit about yourself?", 'interviewer');
                updateStatus('AI asked first question', 'info');
                
                setTimeout(() => {
                    addChatMessage('You', "I'm a software developer with 3 years of experience. I love working with teams and solving complex problems.", 'user');
                    updateStatus('Candidate responded', 'info');
                    
                    setTimeout(() => {
                        const aiResponse = generateAIResponse("I'm a software developer with 3 years of experience. I love working with teams and solving complex problems.");
                        addChatMessage('AI Interviewer', aiResponse, 'ai');
                        updateStatus('AI responded with follow-up question', 'success');
                    }, 1000);
                }, 2000);
            }, 1000);
        }
        
        // Event listeners
        document.getElementById('testAI').addEventListener('click', testAIInteraction);
        document.getElementById('testRecording').addEventListener('click', testRecording);
        document.getElementById('testFullFlow').addEventListener('click', testFullFlow);
        
        // Initialize
        updateStatus('AI Interaction Test ready. Click any test button to begin.', 'info');
    </script>
</body>
</html> 