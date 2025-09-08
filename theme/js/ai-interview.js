// AI Interview System JavaScript - VERSION 6.4 (VOICE SELECTION & RATING)
// Completely eliminated ALL follow-up questions and fixed recording
// ADDED: Voice selection, real-time transcript display, interview rating system
// FIXED: Recording no longer cuts off automatically - candidates have full control
// FIXED: Last question now shows in chat, candidate responses are captured, and chat is scrollable

// Debug function to confirm we're running the latest version
console.log('AI Interview System VERSION 6.4 loaded - Voice selection, real-time transcript, and rating system');

let video;
let canvas;
let isRecording = false;
let currentQuestion = 0;
let questions = [];
let interviewToken = '';
let registrationId = '';
let mediaRecorder;
let recordedChunks = [];
let startTime;
let endTime;
let isInterviewStarted = false;
let speechRecognition = null;
let isListening = false;
let currentTranscript = '';
let chatMessages = [];
let speechSynthesis = window.speechSynthesis;
let isAISpeaking = false;
let aiResponses = [];
let essentialQuestions = []; // Store the essential questions that must be covered
let currentEssentialQuestion = 0; // Track which essential question we're on
let essentialQuestionsCovered = new Set(); // Track which essential questions have been covered
let jobData = {}; // Store job information
let cvData = {}; // Store CV/resume information
let candidateResponses = []; // Store candidate responses for context
let silenceTimeout = null;
const SILENCE_DURATION_MS = 300000; // 5 minutes of silence - allows candidates to think and respond naturally
let conversationTopics = new Set(); // Track conversation topics covered
let currentTopic = ''; // Track current conversation topic
let conversationDepth = 0; // Track conversation depth

async function initializeInterview(jobData, token, regId) {
    console.log('Initializing interview with token:', token, 'registrationId:', regId);
    interviewToken = token;
    registrationId = regId;
    
    // Initialize video
    video = document.getElementById('video');
    canvas = document.getElementById('overlay');
    
    try {
        // First check if the browser supports getUserMedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Your browser does not support camera and microphone access. Please use a modern browser like Chrome, Firefox, or Edge.');
        }

        // Request camera and microphone permissions
        console.log('Requesting camera and microphone permissions...');
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: "user"
            }, 
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true
            }
        });
        
        console.log('Media permissions granted:', {
            videoTracks: stream.getVideoTracks().length,
            audioTracks: stream.getAudioTracks().length,
            audioTrackSettings: stream.getAudioTracks()[0]?.getSettings()
        });
        
        video.srcObject = stream;
        
        // Wait for video to be ready
        await new Promise((resolve) => {
            video.onloadedmetadata = () => {
                resolve();
            };
        });

        // Initialize face-api.js
        try {
            console.log('Loading face detection models...');
            await faceapi.nets.tinyFaceDetector.loadFromUri('/eris/theme/models');
            console.log('TinyFaceDetector loaded');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/eris/theme/models');
            console.log('FaceLandmark68Net loaded');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/eris/theme/models');
            console.log('FaceRecognitionNet loaded');
            await faceapi.nets.faceExpressionNet.loadFromUri('/eris/theme/models');
            console.log('FaceExpressionNet loaded');
        } catch (error) {
            console.error('Error loading face detection models:', error);
            updateStatus('Warning: Face detection models failed to load, but interview can continue.', 'warning');
            // Don't return, continue with the interview
        }
        
        // Start face detection (only if faceapi is available)
        if (typeof faceapi !== 'undefined') {
            video.addEventListener('play', () => {
                const displaySize = { width: video.width, height: video.height };
                faceapi.matchDimensions(canvas, displaySize);
                
                setInterval(async () => {
                    try {
                        const detections = await faceapi.detectAllFaces(
                            video, 
                            new faceapi.TinyFaceDetectorOptions()
                        ).withFaceLandmarks().withFaceExpressions();
     
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                        faceapi.draw.drawDetections(canvas, resizedDetections);
                        faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                        faceapi.draw.drawFaceExpressions(canvas, resizedDetections);
                    } catch (error) {
                        console.error('Face detection error:', error);
                    }
                }, 100);
            });
        } else {
            console.log('FaceAPI not available, skipping face detection');
        }
        
        // Initialize speech recognition
        initializeSpeechRecognition();
        
        // Generate questions based on job data
        console.log('Generating questions...');
        await generateQuestions();
        console.log('Questions generated successfully');
        
        // Set up event listeners
        document.getElementById('startInterview').addEventListener('click', startInterview);
        
        const stopButton = document.getElementById('stopAnswer');
        if (stopButton) {
            console.log('Setting up stop button event listener');
            stopButton.addEventListener('click', () => {
                console.log('Stop Answer button clicked!');
                stopAnswer();
            });
        } else {
            console.error('Stop button not found during setup!');
        }
        
        document.getElementById('completeInterview').addEventListener('click', completeInterview);
        
        // Set up periodic token check (every 5 minutes) - only if interview is started
        const tokenCheckInterval = setInterval(async () => {
            if (isInterviewStarted) {
                try {
                    const tokenValid = await checkTokenValidity();
                    if (!tokenValid) {
                        console.log('Token expiring soon, extending...');
                        await extendTokenExpiry();
                    }
                } catch (error) {
                    console.error('Token check failed:', error);
                }
            }
        }, 5 * 60 * 1000); // 5 minutes
        
        updateStatus('Camera and microphone initialized successfully. You can start the interview when ready.', 'success');
        
    } catch (error) {
        console.error('Error initializing interview:', error);
        let errorMessage = 'Error accessing camera and microphone. ';
        
        if (error.name === 'NotAllowedError') {
            errorMessage += 'Please ensure you have granted camera and microphone permissions in your browser settings.';
        } else if (error.name === 'NotFoundError') {
            errorMessage += 'No camera or microphone found. Please connect a camera and microphone and try again.';
        } else if (error.name === 'NotReadableError') {
            errorMessage += 'Your camera or microphone is already in use by another application. Please close other applications using your camera and try again.';
        } else {
            errorMessage += error.message || 'Please refresh the page and try again.';
        }
        
        updateStatus(errorMessage, 'danger');
    }
}

async function generateQuestions() {
    console.log('Starting generateQuestions function');
    try {
        updateStatus('Generating personalized interview questions...');
        
        // Use job data from PHP (stored in jobData variable)
        const jobTitle = jobData.title || '';
        const jobDescription = jobData.description || '';
        const jobRequirements = jobData.requirements || '';
        const applicantName = jobData.applicant || '';
        
        // For now, we'll use empty CV data since it's not provided in the PHP
        const cvExperience = '';
        const cvEducation = '';
        const cvSkills = '';
        const cvProjects = '';
        
        // Store job and CV data for context
        jobData = {
            title: jobTitle,
            description: jobDescription,
            requirements: jobRequirements
        };
        
        cvData = {
            experience: cvExperience,
            education: cvEducation,
            skills: cvSkills,
            projects: cvProjects
        };
        
        const questionData = {
            job_title: jobTitle,
            job_description: jobDescription,
            job_requirements: jobRequirements,
            applicant_name: applicantName,
            cv_experience: cvExperience,
            cv_education: cvEducation,
            cv_skills: cvSkills,
            cv_projects: cvProjects
        };
        
        const response = await fetch('/eris/generate-questions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(questionData)
        });
        
        const data = await response.json();
        
        if (data.success && data.questions) {
            console.log('Questions received successfully:', data.questions.length, 'questions');
            questions = data.questions;
            essentialQuestions = [...data.questions]; // Store essential questions
            displayQuestions();
            updateStatus('Personalized questions generated based on your CV and the job requirements. Ready to start interview!');
        } else {
            throw new Error(data.message || 'Failed to generate questions');
        }
    } catch (error) {
        console.error('Error generating questions:', error);
        updateStatus('Error generating questions. Using default questions.');
        console.log('Falling back to default questions...');
        
        // Fallback to default questions
        questions = [
            "Hi there! I'm so excited to chat with you today. Could you tell me a bit about yourself and what brings you here? I'd love to hear your story!",
            "I'm really curious about your journey! What is it about this position that caught your attention? What excites you most about the opportunity?",
            "I'd love to hear about your strengths! What would you say are your greatest talents, and how do you use them to make a difference?",
            "I really appreciate honesty! What's something you're working on improving? I'm curious about your growth mindset!",
            "I'm excited about what you could bring to our team! Why do you think you'd be a great fit for this role? I'd love to hear your perspective!",
            "I'm curious about your dreams! Where do you see yourself in 5 years, and what steps are you taking to get there? I'd love to hear your vision!",
            "I'm really interested in how you handle pressure! Can you tell me about a time when you had to work under stress? How did you stay focused and deliver results?",
            "I love hearing about people's problem-solving skills! Can you tell me about a challenging situation you faced at work and how you handled it? What was the outcome?",
            "I'm curious about your expectations! What are you looking for in terms of compensation and growth opportunities? I want to make sure we're aligned!",
            "I've really enjoyed our conversation! Is there anything about this role or our company that you'd love to know more about? I'm here to help!"
        ];
        essentialQuestions = [...questions]; // Store essential questions
        displayQuestions();
    }
}

function initializeSpeechRecognition() {
    // Check if browser supports speech recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        speechRecognition = new SpeechRecognition();
        
        speechRecognition.continuous = true;
        speechRecognition.interimResults = true;
        speechRecognition.lang = 'en-US';
        speechRecognition.maxAlternatives = 1; // Optimize for single best result
        
        speechRecognition.onstart = () => {
            isListening = true;
            updateChatStatus('Listening... Speak now', 'listening');
            resetSilenceTimeout();
        };
        
        speechRecognition.onresult = (event) => {
            let interimTranscript = '';
            let finalTranscript = '';
            
            console.log('Speech recognition result received:', event.results.length, 'results');
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                const isFinal = event.results[i].isFinal;
                console.log(`Result ${i}: "${transcript}" (final: ${isFinal})`);
                
                if (isFinal) {
                    finalTranscript += transcript;
                } else {
                    interimTranscript += transcript;
                }
            }
            
            if (finalTranscript) {
                currentTranscript += finalTranscript;
                console.log('Final transcript updated:', currentTranscript);
                resetSilenceTimeout();
                // Update live transcript display
                updateLiveTranscript(currentTranscript);
            }
            
            if (interimTranscript) {
                // Show interim transcript in real-time
                const fullTranscript = currentTranscript + interimTranscript;
                console.log('Interim transcript:', fullTranscript);
                updateLiveTranscript(fullTranscript);
                updateChatStatus(`Listening: ${interimTranscript}`, 'speaking');
                resetSilenceTimeout();
            }
        };
        
        speechRecognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            isListening = false;
            updateChatStatus('Speech recognition error. Please try again.', 'error');
            
            // If speech recognition fails, we can still continue with the interview
            // The user can still click "Stop Answering" to proceed
            console.log('Speech recognition failed, but interview can continue');
        };
        
        speechRecognition.onend = () => {
            isListening = false;
            updateChatStatus('Press "Stop Answering" when you\'re done speaking', 'stopped');
        };
        
    } else {
        console.warn('Speech recognition not supported in this browser');
        updateStatus('Speech recognition not supported. You can still record video answers.', 'warning');
    }
}

function startSpeechRecognition() {
    console.log('startSpeechRecognition called - speechRecognition:', !!speechRecognition, 'isListening:', isListening);
    
    if (speechRecognition && !isListening) {
        try {
            console.log('Starting speech recognition...');
            speechRecognition.start();
            console.log('Speech recognition started successfully');
        } catch (error) {
            console.error('Error starting speech recognition:', error);
        }
    } else {
        console.log('Speech recognition not started - speechRecognition:', !!speechRecognition, 'isListening:', isListening);
    }
}

function stopSpeechRecognition() {
    if (speechRecognition && isListening) {
        try {
            speechRecognition.stop();
        } catch (error) {
            console.error('Error stopping speech recognition:', error);
        }
    }
}

function stopRecording() {
    if (mediaRecorder && isRecording) {
        console.log('Legacy stopRecording called - this should not happen');
        mediaRecorder.stop();
        isRecording = false;
        endTime = Date.now();
    }
}

async function saveRecording(blob) {
    console.log('💾 Saving recording...', {
        blobSize: Math.round(blob.size/1024) + 'KB',
        conversationTurn: aiResponses.length + 1,
        questionType: 'essential_answer',
        duration: endTime ? (endTime - startTime) / 1000 : 0,
        token: interviewToken,
        registrationId: registrationId,
        currentEssentialQuestion: currentEssentialQuestion,
        transcript: currentTranscript ? currentTranscript.length : 0
    });
    
    // Check token validity before saving (but don't block if it fails)
    try {
        const tokenValid = await checkTokenValidity();
        if (!tokenValid) {
            console.error('Token is invalid or expired, attempting to extend...');
            const extended = await extendTokenExpiry();
            if (!extended) {
                console.error('Failed to extend token, but continuing with recording save');
            } else {
                console.log('Token extended successfully');
            }
        }
    } catch (error) {
        console.error('Token validation failed, but continuing with recording save:', error);
    }
    
    const formData = new FormData();
    const conversationTurn = aiResponses.length + 1;
    const questionType = 'essential_answer';
    
    // Save the transcript along with the video
    const transcript = currentTranscript.trim();
    
    formData.append('video', blob, `conversation_turn_${conversationTurn}_${questionType}.webm`);
    formData.append('conversationTurn', conversationTurn);
    formData.append('questionType', questionType);
    formData.append('currentEssentialQuestion', currentEssentialQuestion);
    formData.append('duration', endTime ? (endTime - startTime) / 1000 : 0);
    formData.append('token', interviewToken);
    formData.append('registrationId', registrationId);
    formData.append('transcript', transcript);
    
    try {
        const response = await fetch('/eris/upload-recording-enhanced.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON response:', text);
            updateStatus('Invalid JSON response from server during upload.', 'danger');
            return;
        }
        
        if (data && data.success) {
            updateStatus(`Recording saved successfully (${Math.round(blob.size / 1024)}KB)`, 'success');
            
            // Store candidate response for better tracking
            if (transcript) {
                candidateResponses.push({
                    answer: transcript,
                    questionIndex: currentEssentialQuestion,
                    questionType: questionType,
                    conversationTurn: conversationTurn,
                    timestamp: new Date().toISOString(),
                    duration: endTime ? (endTime - startTime) / 1000 : 0
                });
            }
        } else {
            updateStatus(`Failed to save recording: ${data && data.error ? data.error : 'Unknown error'}`, 'danger');
        }
    } catch (error) {
        console.error('Upload error:', error);
        updateStatus(`Failed to upload recording: ${error.message}`, 'danger');
    }
}

function updateLiveTranscript(transcript) {
    const transcriptElement = document.getElementById('live-transcript');
    const transcriptDisplay = document.getElementById('transcript-display');
    
    console.log('updateLiveTranscript called with:', transcript);
    console.log('transcriptElement found:', !!transcriptElement);
    console.log('transcriptDisplay found:', !!transcriptDisplay);
    
    if (transcriptElement && transcriptDisplay) {
        if (transcript && transcript.trim()) {
            transcriptElement.textContent = transcript;
            transcriptDisplay.style.display = 'block';
            console.log('Transcript updated successfully:', transcript);
        } else {
            transcriptElement.textContent = '';
            transcriptDisplay.style.display = 'none';
            console.log('Transcript cleared');
        }
    } else {
        console.warn('Transcript display elements not found - transcriptElement:', !!transcriptElement, 'transcriptDisplay:', !!transcriptDisplay);
        
        // Try to find alternative transcript display elements
        const allElements = document.querySelectorAll('*');
        const possibleTranscriptElements = Array.from(allElements).filter(el => 
            el.id && (el.id.includes('transcript') || el.id.includes('Transcript'))
        );
        console.log('Possible transcript elements found:', possibleTranscriptElements.map(el => el.id));
        
        // If we can't find the transcript elements, at least log the transcript
        if (transcript && transcript.trim()) {
            console.log('Transcript captured but no display element found:', transcript);
        }
    }
}

function addChatMessage(sender, message, type = 'user') {
    const chatContainer = document.getElementById('chat-container');
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
    
    // Store message for later use
    chatMessages.push({
        sender,
        message,
        type,
        timestamp: new Date().toISOString(),
        questionNumber: currentQuestion + 1
    });
}

function updateChatStatus(status, type) {
    const statusElement = document.getElementById('chat-status');
    if (statusElement) {
        statusElement.textContent = status;
        statusElement.className = `chat-status ${type}`;
    }
}

function speakText(text, callback = null) {
    console.log('speakText called with:', text);
    
    // Always call callback after a short delay if speech synthesis fails
    const fallbackCallback = () => {
        setTimeout(() => {
            if (callback) callback();
        }, 1000); // 1 second delay to simulate speaking time
    };
    
    if (speechSynthesis && !isAISpeaking) {
        isAISpeaking = true;
        
        // Stop any ongoing speech
        speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 0.95; // Slightly slower for natural conversation
        utterance.pitch = 1.1; // Slightly higher pitch for friendliness
        
        // Use selected voice if available
        if (window.interviewConfig && window.interviewConfig.selectedVoice) {
            const voices = speechSynthesis.getVoices();
            console.log('Available voices for interview:', voices.map(v => `${v.name} (${v.lang})`));
            
            if (window.interviewConfig.selectedVoice === 'male') {
                // Try to find a male voice with specific characteristics
                let maleVoice = voices.find(v => 
                    v.name.toLowerCase().includes('male') ||
                    v.name.toLowerCase().includes('david') ||
                    v.name.toLowerCase().includes('james') ||
                    v.name.toLowerCase().includes('john') ||
                    v.name.toLowerCase().includes('mike') ||
                    v.name.toLowerCase().includes('steve') ||
                    v.name.toLowerCase().includes('tom') ||
                    v.name.toLowerCase().includes('alex') ||
                    v.name.toLowerCase().includes('daniel') ||
                    v.name.toLowerCase().includes('mark')
                );
                
                if (maleVoice) {
                    utterance.voice = maleVoice;
                    console.log('Using male voice for interview:', maleVoice.name);
                } else {
                    // Fallback: use a deeper pitch for male voice
                    utterance.pitch = 0.8;
                    utterance.rate = 0.9;
                    console.log('No male voice found, using pitch adjustment for interview');
                }
            } else if (window.interviewConfig.selectedVoice === 'female') {
                // Try to find a female voice with specific characteristics
                let femaleVoice = voices.find(v => 
                    v.name.toLowerCase().includes('female') ||
                    v.name.toLowerCase().includes('sarah') ||
                    v.name.toLowerCase().includes('emma') ||
                    v.name.toLowerCase().includes('lisa') ||
                    v.name.toLowerCase().includes('jennifer') ||
                    v.name.toLowerCase().includes('samantha') ||
                    v.name.toLowerCase().includes('victoria') ||
                    v.name.toLowerCase().includes('karen') ||
                    v.name.toLowerCase().includes('alice') ||
                    v.name.toLowerCase().includes('sophie')
                );
                
                if (femaleVoice) {
                    utterance.voice = femaleVoice;
                    console.log('Using female voice for interview:', femaleVoice.name);
                } else {
                    // Fallback: use a higher pitch for female voice
                    utterance.pitch = 1.3;
                    utterance.rate = 1.0;
                    console.log('No female voice found, using pitch adjustment for interview');
                }
            }
        }
        
        utterance.onstart = () => {
            console.log('Speech synthesis started');
            updateChatStatus('AI is speaking...', 'ai-speaking');
        };
        
        utterance.onend = () => {
            console.log('Speech synthesis ended');
            isAISpeaking = false;
            updateChatStatus('Listening for your response...', 'listening');
            if (callback) callback();
        };
        
        utterance.onerror = (event) => {
            console.error('Speech synthesis error:', event);
            isAISpeaking = false;
            updateChatStatus('Speech error. Please continue.', 'error');
            if (callback) callback();
        };
        
        try {
            speechSynthesis.speak(utterance);
        } catch (error) {
            console.error('Error starting speech synthesis:', error);
            isAISpeaking = false;
            updateChatStatus('Speech error. Please continue.', 'error');
            fallbackCallback();
        }
    } else {
        console.log('Speech synthesis not available or AI already speaking, using fallback');
        // If speech synthesis is not available, just call the callback after a delay
        fallbackCallback();
    }
}

// REMOVED: generateAIResponse function - No longer needed, using generateSimpleAcknowledgment instead

// Removed unused generateInteractiveFollowUp function to prevent errors
// CACHE BUST: This ensures the browser loads the latest version

// REMOVED: generateFollowUpResponse function - No longer needed, using generateSimpleAcknowledgment instead

function generateNewTopicQuestion() {
    const uncoveredTopics = [
        'problem_solving',
        'teamwork', 
        'learning_growth',
        'achievements',
        'failure_learning',
        'motivation',
        'work_life_balance',
        'future_goals',
        'leadership',
        'customer_service'
    ].filter(topic => !conversationTopics.has(topic));
    
    if (uncoveredTopics.length === 0) {
        return generateClosingResponse();
    }
    
    const newTopic = uncoveredTopics[Math.floor(Math.random() * uncoveredTopics.length)];
    const topicQuestions = {
        'problem_solving': "I'd love to hear about how you handle challenges! Can you tell me about a time when you faced a really difficult problem? What was your approach?",
        'teamwork': "I'm really interested in how you work with others! Can you share a story about a time when you had to collaborate with a team? What made it successful?",
        'learning_growth': "I love hearing about people's growth journeys! What's something you've learned recently that really excited you? How do you approach learning new things?",
        'achievements': "I'd love to hear about your proudest moments! Can you tell me about an achievement you're really proud of? What made it special?",
        'failure_learning': "I really value honesty and growth! Can you tell me about a time when things didn't go as planned? What did you learn from that experience?",
        'motivation': "I'm curious about what drives you! What is it about this type of work that really excites you? What gets you out of bed in the morning?",
        'work_life_balance': "I think it's so important to have a healthy perspective! How do you balance your work and personal life? What's important to you outside of work?",
        'future_goals': "I love hearing about people's dreams! Where do you see yourself in the next few years? What are you working toward?",
        'leadership': "I'm interested in your leadership style! Can you tell me about a time when you had to lead or influence others? What's your approach?",
        'customer_service': "I'd love to hear about your approach to helping others! Can you tell me about a time when you went above and beyond for someone? What drives you to provide great service?"
    };
    
    return topicQuestions[newTopic];
}

function generateTransitionToNextQuestion(userAnswer, currentTopic) {
    // Generate an interactive transition that acknowledges their specific answer and moves to the next essential question
    const answer = userAnswer.toLowerCase();
    let acknowledgment = '';
    
    // Create personalized acknowledgment based on their answer
    if (answer.includes('project') || answer.includes('work')) {
        acknowledgment = "I love that you mentioned that project! ";
    } else if (answer.includes('team') || answer.includes('colleague')) {
        acknowledgment = "That's such a great example of teamwork! ";
    } else if (answer.includes('learned') || answer.includes('realized')) {
        acknowledgment = "It's wonderful that you learned that! ";
    } else if (answer.includes('challenging') || answer.includes('difficult')) {
        acknowledgment = "I really admire how you handled that challenge! ";
    } else if (answer.includes('success') || answer.includes('achieved')) {
        acknowledgment = "That's such an impressive achievement! ";
    } else if (answer.includes('passion') || answer.includes('love')) {
        acknowledgment = "Your passion really comes through! ";
    } else if (answer.includes('experience') || answer.includes('worked')) {
        acknowledgment = "That experience sounds really valuable! ";
    } else if (answer.includes('problem') || answer.includes('solved')) {
        acknowledgment = "I love how you approached that problem! ";
    } else {
        acknowledgment = "That's really insightful! ";
    }
    
    const transitions = [
        `${acknowledgment}Thank you for sharing that with me. Now, I'd love to learn more about another aspect of your experience.`,
        `${acknowledgment}It gives me a great sense of who you are. Let me ask you about something else that's important to us.`,
        `${acknowledgment}I can see how that experience has shaped you. Now, I'm curious about another area that's key to this role.`,
        `${acknowledgment}It's really helpful to understand your approach. Let me ask you about another important aspect.`,
        `${acknowledgment}It shows real depth. Now, I'd like to explore another area that's crucial for this position.`
    ];
    
    return transitions[Math.floor(Math.random() * transitions.length)];
}

function generateClosingResponse(userAnswer = '') {
    return "I've really enjoyed our conversation! You've shared so many wonderful insights. Before we wrap up, is there anything about this role or our company that you'd love to know more about? I want to make sure you have all the information you need!";
}

function generateFeedbackAcknowledgment(userAnswer) {
    // Generate acknowledgment of the candidate's feedback response and transition to next question
    const answer = userAnswer.toLowerCase();
    let acknowledgment = '';
    
    // Create personalized acknowledgment based on their feedback
    if (answer.includes('thank') || answer.includes('appreciate')) {
        acknowledgment = "I'm so glad that resonated with you! ";
    } else if (answer.includes('agree') || answer.includes('think so')) {
        acknowledgment = "I love that you feel the same way! ";
    } else if (answer.includes('question') || answer.includes('ask')) {
        acknowledgment = "That's a great question! ";
    } else if (answer.includes('experience') || answer.includes('worked')) {
        acknowledgment = "That's such a valuable perspective! ";
    } else if (answer.includes('learn') || answer.includes('realized')) {
        acknowledgment = "It's wonderful that you learned that! ";
    } else if (answer.includes('passion') || answer.includes('love')) {
        acknowledgment = "Your passion really comes through! ";
    } else if (answer.includes('challenge') || answer.includes('difficult')) {
        acknowledgment = "I really admire your approach to that! ";
    } else if (answer.includes('success') || answer.includes('achieved')) {
        acknowledgment = "That's such an impressive insight! ";
    } else {
        acknowledgment = "That's really thoughtful! ";
    }
    
    const transitions = [
        `${acknowledgment}Thank you for sharing that with me. Now, I'd love to learn more about another aspect of your experience.`,
        `${acknowledgment}I really appreciate your perspective on that. Let me ask you about something else that's important to us.`,
        `${acknowledgment}That gives me such a great sense of who you are. Now, I'm curious about another area that's key to this role.`,
        `${acknowledgment}It's really helpful to understand your approach. Let me ask you about another important aspect.`,
        `${acknowledgment}That shows real depth and insight. Now, I'd like to explore another area that's crucial for this position.`
    ];
    
    return transitions[Math.floor(Math.random() * transitions.length)];
}

function generateInteractiveDefaultResponse(userAnswer) {
    // Generate interactive responses that acknowledge what the candidate said
    const answer = userAnswer.toLowerCase();
    let acknowledgment = '';
    
    // Create personalized acknowledgment based on their answer
    if (answer.includes('project') || answer.includes('work')) {
        acknowledgment = "I love that you mentioned that project! ";
    } else if (answer.includes('team') || answer.includes('colleague')) {
        acknowledgment = "That's such a great example of teamwork! ";
    } else if (answer.includes('learned') || answer.includes('realized')) {
        acknowledgment = "It's wonderful that you learned that! ";
    } else if (answer.includes('challenging') || answer.includes('difficult')) {
        acknowledgment = "I really admire how you handled that challenge! ";
    } else if (answer.includes('success') || answer.includes('achieved')) {
        acknowledgment = "That's such an impressive achievement! ";
    } else if (answer.includes('passion') || answer.includes('love')) {
        acknowledgment = "Your passion really comes through! ";
    } else if (answer.includes('experience') || answer.includes('worked')) {
        acknowledgment = "That experience sounds really valuable! ";
    } else if (answer.includes('problem') || answer.includes('solved')) {
        acknowledgment = "I love how you approached that problem! ";
    } else {
        acknowledgment = "That's really interesting! ";
    }
    
    const responses = [
        `${acknowledgment}Could you tell me a bit more about that? I'd love to hear more details.`,
        `${acknowledgment}What made you think about it that way? I'm curious about your perspective.`,
        `${acknowledgment}How did that experience shape who you are today? I'd love to understand the impact.`,
        `${acknowledgment}What was the most memorable part of that? I love hearing about people's key moments.`,
        `${acknowledgment}What do you think was the key to your success there? I'm always interested in success factors.`,
        `${acknowledgment}If you could go back and do anything differently, what would it be? I value learning from experience.`,
        `${acknowledgment}How do you think this relates to what we're looking for here? I'd love to hear your thoughts.`,
        `${acknowledgment}What was your thought process behind that? I'm really interested in how you approach things.`,
        `${acknowledgment}What would you say is your biggest strength based on that experience? I love hearing about people's strengths.`,
        `${acknowledgment}What's something you learned about yourself through that experience? I think self-awareness is so valuable.`
    ];
    
    return responses[Math.floor(Math.random() * responses.length)];
}

function moveToNextQuestion() {
    // Continue the conversation with a new topic
    startNextQuestion();
}

function startNextQuestion() {
    console.log('startNextQuestion called - essentialQuestionsCovered:', essentialQuestionsCovered.size, 'total:', essentialQuestions.length);
    
    // Reset state for new question
    currentTranscript = '';
    
    // Check if we need to cover more essential questions
    if (essentialQuestionsCovered.size < essentialQuestions.length) {
        // Find the next uncovered essential question
        let nextQuestion = '';
        for (let i = 0; i < essentialQuestions.length; i++) {
            if (!essentialQuestionsCovered.has(i)) {
                nextQuestion = essentialQuestions[i];
                essentialQuestionsCovered.add(i);
                currentEssentialQuestion = i;
                console.log('Moving to essential question:', i + 1);
                break;
            }
        }
        
        // Add question to chat
        addChatMessage('AI Interviewer', nextQuestion, 'interviewer');
        
        // Update progress display
        updateProgressDisplay();
        
        // Speak the question
        speakText(nextQuestion, () => {
            // After speaking the question, start recording and listening for answer
            setTimeout(() => {
                        // Start new recording session for candidate response
        startRecordingForCandidate();
        updateStatus(`Great! I'd love to hear your thoughts on this. Take your time and share as much as you'd like! The recording will continue until you press "Stop Answering".`, 'info');
        document.getElementById('stopAnswer').textContent = 'Stop Answering';
            }, 500);
        });
    } else {
        console.log('All essential questions covered - asking closing question');
        // All essential questions covered, ask the closing question about company
        const closingQuestion = generateClosingResponse();
        addChatMessage('AI Interviewer', closingQuestion, 'interviewer');
        
        // Update progress display to show final question
        updateProgressDisplay();
        
        // Speak the closing question
        speakText(closingQuestion, () => {
            // After speaking the closing question, start recording for candidate's final response
            setTimeout(() => {
                startRecordingForCandidate();
                updateStatus(`Great! I'd love to hear your thoughts on this final question. Take your time and share as much as you'd like! The recording will continue until you press "Stop Answering".`, 'info');
                document.getElementById('stopAnswer').textContent = 'Stop Answering';
            }, 500);
        });
    }
}

function startRecordingForCandidate(nextStepCallback) {
    console.log('🔴 Starting reliable recording for candidate...');
    
    // Stop any previous recorder
    if (window.mediaRecorder && mediaRecorder && mediaRecorder.state !== 'inactive') {
        try { mediaRecorder.stop(); } catch (e) { console.log('Stopped previous recorder'); }
    }
    mediaRecorder = null;
    recordedChunks = [];
    isRecording = true;
    
    // Start new recording session
    const stream = video.srcObject;
    if (!stream) {
        console.error('❌ No video stream available for recording');
        updateStatus('Error: No video stream available. Please refresh the page and try again.', 'danger');
        return;
    }
    
    try {
        // Use more compatible MIME type with fallbacks
        let mimeType = 'video/webm;codecs=vp8,opus';
        if (!MediaRecorder.isTypeSupported(mimeType)) {
            mimeType = 'video/webm';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'video/mp4';
            }
        }
        
        console.log('📹 Using MIME type:', mimeType);
        
        mediaRecorder = new MediaRecorder(stream, {
            mimeType: mimeType,
            videoBitsPerSecond: 500000, // Lower bitrate for better reliability
            audioBitsPerSecond: 64000   // Lower audio bitrate
        });
        
        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) {
                recordedChunks.push(e.data);
                console.log('📈 Recording chunk received:', e.data.size, 'bytes, total chunks:', recordedChunks.length);
                
                // Log first chunk to confirm recording is working
                if (recordedChunks.length === 1) {
                    console.log('✅ First recording chunk received - recording is working!');
                    updateStatus('Recording in progress... 🔴 Chunks captured: ' + recordedChunks.length, 'info');
                } else if (recordedChunks.length % 5 === 0) {
                    // Update status every 5 chunks
                    updateStatus('Recording in progress... 🔴 Chunks captured: ' + recordedChunks.length, 'info');
                }
            } else {
                console.warn('⚠️ Empty recording chunk received');
            }
        };
        
        mediaRecorder.onstart = () => {
            console.log('✅ Recording STARTED successfully for candidate response');
            startTime = Date.now();
            updateStatus('🎥 Recording your response... Speak clearly and take your time! Press "Stop Answering" when you\'re done.', 'info');
            document.body.classList.add('recording-active'); // Add a class for visual indicator
            
            // Add visual recording indicator and enable stop button
            const stopButton = document.getElementById('stopAnswer');
            if (stopButton) {
                console.log('🔴 Enabling stop button');
                stopButton.disabled = false;
                stopButton.style.backgroundColor = '#dc3545';
                stopButton.style.color = 'white';
                stopButton.style.animation = 'recording-pulse 1s infinite';
                stopButton.textContent = '⏹️ Stop Recording';
                console.log('Stop button enabled status:', !stopButton.disabled);
            } else {
                console.error('❌ Stop button not found!');
            }
            
            // Add visual recording indicator to video
            if (video) {
                video.style.border = '3px solid #dc3545';
                video.style.borderRadius = '10px';
            }
            
            // Start speech recognition for real-time transcript
            console.log('🎤 Starting speech recognition for transcript capture...');
            startSpeechRecognition();
        };
        
        mediaRecorder.onstop = async () => {
            const totalSize = recordedChunks.reduce((sum, chunk) => sum + chunk.size, 0);
            console.log('⏹️ Recording STOPPED, saving...', {
                chunksCount: recordedChunks.length,
                totalSize: Math.round(totalSize/1024) + 'KB',
                duration: endTime ? (endTime - startTime) / 1000 : 0
            });
            
            endTime = Date.now();
            document.body.classList.remove('recording-active');
            
            // Remove visual recording indicator and disable stop button
            const stopButton = document.getElementById('stopAnswer');
            if (stopButton) {
                stopButton.disabled = true;
                stopButton.style.backgroundColor = '';
                stopButton.style.color = '';
                stopButton.style.animation = '';
                stopButton.textContent = 'Stop Answering';
            }
            
            // Remove visual recording indicator from video
            if (video) {
                video.style.border = '';
                video.style.borderRadius = '';
            }
            
            if (recordedChunks.length > 0) {
                const blob = new Blob(recordedChunks, { type: mimeType });
                console.log('✅ Recording blob created:', Math.round(blob.size/1024), 'KB');
                updateStatus('Saving your answer... (' + Math.round(blob.size/1024) + 'KB)', 'info');
                
                // Optimized saving with progress feedback
                try {
                    await saveRecording(blob);
                    updateStatus('✅ Recording saved successfully!', 'success');
                } catch (error) {
                    console.error('❌ Error saving recording:', error);
                    updateStatus('Error saving recording: ' + error.message, 'danger');
                }
            } else {
                console.warn('⚠️ No recording chunks captured - recording may have failed');
                updateStatus('Warning: No recording data captured. Please try again.', 'warning');
            }
            
            // Call the next step after saving
            if (typeof nextStepCallback === 'function') {
                setTimeout(() => nextStepCallback(), 1000);
            }
        };
        
        mediaRecorder.onerror = (event) => {
            console.error('❌ MediaRecorder error:', event.error);
            updateStatus('Recording error: ' + event.error + '. Please refresh and try again.', 'danger');
            isRecording = false;
        };
        
        // Start recording with optimized chunk collection
        mediaRecorder.start(1000); // Collect data every 1 second for better reliability
        console.log('🎥 MediaRecorder.start() called - recording should begin now');
        updateStatus(`✅ Recording started! Speak clearly and press "Stop Answering" when done.`, 'info');
        
    } catch (error) {
        console.error('Error starting MediaRecorder:', error);
        updateStatus('Error starting recording. Please refresh the page and try again.', 'danger');
    }
}

function displayQuestions() {
    const container = document.getElementById('questions-container');
    if (!container) return; // Add null check for the container
    container.innerHTML = '';
    
    // Add progress indicator
    const progressDiv = document.createElement('div');
    progressDiv.className = 'question-progress';
    progressDiv.innerHTML = `
        <div class="progress-info">
            <span class="current-question">Question 1 of ${questions.length}</span>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: ${(1/questions.length)*100}%"></div>
            </div>
        </div>
    `;
    container.appendChild(progressDiv);
    
    questions.forEach((question, index) => {
        const div = document.createElement('div');
        div.className = 'question-item';
        // Only show the first question initially, hide others
        if (index > 0) {
            div.style.display = 'none';
        }
        div.innerHTML = `
            <h5>Question ${index + 1}</h5>
            <p>${question}</p>
            <div class="answer-status"></div>
        `;
        container.appendChild(div);
    });

    // Add a "more questions" indicator if there are multiple questions
    if (questions.length > 1) {
        const indicator = document.createElement('div');
        indicator.className = 'more-questions-indicator';
        indicator.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-chevron-down"></i>
                <p>Answer this question to see the next one</p>
            </div>
        `;
        container.appendChild(indicator);
    }

    // Highlight the current question initially
    const currentQuestionElement = document.querySelectorAll('.question-item')[currentQuestion];
    if (currentQuestionElement) {
        currentQuestionElement.classList.add('active');
    }
}

function startInterview() {
    if (!isInterviewStarted) {
        isInterviewStarted = true;
        isRecording = true;
        
        // Reset conversation state
        conversationTopics.clear();
        currentTopic = '';
        conversationDepth = 0;
        aiResponses = [];
        candidateResponses = [];
        essentialQuestionsCovered.clear();
        currentEssentialQuestion = 0;
        
        // Clear previous questions display and show essential questions progress
        const questionContainer = document.getElementById('questions-container');
        if (questionContainer) {
            const jobTitle = jobData.title || 'this position';
            questionContainer.innerHTML = `
                <div class="conversation-mode-indicator">
                    <p><i class="fas fa-comments"></i> Interactive Conversation Mode</p>
                    <p class="job-specific-info"><i class="fas fa-briefcase"></i> Tailored for ${jobTitle}</p>
                </div>
                <div class="essential-questions-progress">
                    <h4>Interview Topics to Cover</h4>
                    <div class="progress-info">
                        <span class="current-question">Question ${essentialQuestionsCovered.size + 1} of ${essentialQuestions.length}</span>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: ${((essentialQuestionsCovered.size + 1)/essentialQuestions.length)*100}%"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Start recording
        startRecording();
        
        // Update UI
        document.getElementById('startInterview').style.display = 'none';
        document.getElementById('stopAnswer').style.display = 'block';
        document.getElementById('stopAnswer').disabled = false;
        document.getElementById('completeInterview').style.display = 'block';
        document.getElementById('completeInterview').disabled = false;
        
        updateStatus('Welcome! I\'m so excited to chat with you today. Let\'s get started!', 'success');
    }
}

function startRecording() {
    document.getElementById('startInterview').disabled = true;
    document.getElementById('stopAnswer').disabled = false;
    
    // Start the conversation - recording will be handled by startNextQuestion
    startNextQuestion();
    
    // Update UI
    updateStatus(`Starting interactive interview...`, 'info');
}

function stopAnswer() {
    console.log('⏹️ stopAnswer called - isRecording:', isRecording, 'isInterviewStarted:', isInterviewStarted);
    
    // Stop speech recognition and synthesis
    stopSpeechRecognition();
    if (speechSynthesis) {
        speechSynthesis.cancel();
        isAISpeaking = false;
    }
    if (silenceTimeout) clearTimeout(silenceTimeout);
    
    // Capture the transcript before clearing it
    const transcript = currentTranscript.trim();
    console.log('📝 Final transcript captured:', transcript);
    console.log('Transcript length:', transcript.length);
    
    document.body.classList.remove('recording-active');
    
    // Always stop recording if it's active
    if (isRecording && mediaRecorder && mediaRecorder.state === 'recording') {
        console.log('🛑 Stopping media recorder...');
        endTime = Date.now();
        isRecording = false;
        mediaRecorder.stop();
        console.log('✅ Media recorder stop() called');
    } else {
        console.log('⚠️ No active recording to stop - isRecording:', isRecording, 'mediaRecorder state:', mediaRecorder ? mediaRecorder.state : 'null');
    }
    
    // FINAL FIX: Process transcript and move to next question (NO FOLLOW-UPS)
    if (transcript && transcript.trim().length > 0) {
        console.log('Processing transcript:', transcript);
        // Add the candidate's response to chat
        addChatMessage('You', transcript, 'user');
        
        // Store the candidate response
        candidateResponses.push({
            answer: transcript,
            questionIndex: currentEssentialQuestion,
            timestamp: new Date().toISOString()
        });
        
        // Generate a simple acknowledgment and move to next question
        const acknowledgment = generateSimpleAcknowledgment(transcript);
        console.log('AI Acknowledgment generated:', acknowledgment);
        
        if (acknowledgment && acknowledgment.trim() !== '') {
            addChatMessage('AI Interviewer', acknowledgment, 'ai');
            
            // Store the interaction
            aiResponses.push({
                conversationTurn: aiResponses.length + 1,
                userAnswer: transcript,
                aiResponse: acknowledgment,
                topic: 'direct_acknowledgment',
                timestamp: new Date().toISOString()
            });
            
            updateProgressDisplay();
            updateStatus('AI is responding...', 'ai-speaking');
            
            // Speak the acknowledgment and then move to next question
            speakText(acknowledgment, () => {
                console.log('AI finished speaking, moving to next question');
                setTimeout(() => {
                    // Check if this is the final closing response
                    if (acknowledgment && acknowledgment.includes('Your interview is now complete')) {
                        console.log('Final closing response - ending interview');
                        updateStatus('Interview completed! Thank you for your time.', 'success');
                        document.getElementById('completeInterview').click();
                    } else {
                        // ALWAYS move to next question after any response (NO FOLLOW-UPS)
                        console.log('Moving to next essential question');
                        setTimeout(() => {
                            startNextQuestion();
                        }, 1000);
                    }
                }, 500);
            });
        } else {
            console.log('No acknowledgment generated - moving to next question');
            // If no acknowledgment, just move to next question
            setTimeout(() => {
                startNextQuestion();
            }, 1000);
        }
        
        currentTranscript = '';
    } else {
        console.log('No transcript captured - this might indicate a speech recognition issue');
        console.log('Current transcript state:', {
            transcript: transcript,
            transcriptLength: transcript ? transcript.length : 0,
            isListening: isListening,
            speechRecognitionExists: !!speechRecognition
        });
        
        // Even without transcript, we should still process the recording
        if (recordedChunks.length > 0) {
            console.log('No transcript but recording exists - processing recording only');
            // Add a placeholder message for the candidate
            addChatMessage('You', '[Audio/Video response recorded]', 'user');
            
            // Store the candidate response
            candidateResponses.push({
                answer: '[Audio/Video response recorded]',
                questionIndex: currentEssentialQuestion,
                timestamp: new Date().toISOString()
            });
            
            // Generate acknowledgment and move to next question
            const acknowledgment = generateSimpleAcknowledgment('[Audio/Video response recorded]');
            if (acknowledgment && acknowledgment.trim() !== '') {
                addChatMessage('AI Interviewer', acknowledgment, 'ai');
                
                // Store the interaction
                aiResponses.push({
                    conversationTurn: aiResponses.length + 1,
                    userAnswer: '[Audio/Video response recorded]',
                    aiResponse: acknowledgment,
                    topic: 'direct_acknowledgment',
                    timestamp: new Date().toISOString()
                });
                
                updateProgressDisplay();
                updateStatus('AI is responding...', 'ai-speaking');
                
                // Speak the acknowledgment and then move to next question
                speakText(acknowledgment, () => {
                    console.log('AI finished speaking, moving to next question');
                    setTimeout(() => {
                        if (acknowledgment && acknowledgment.includes('Your interview is now complete')) {
                            console.log('Final closing response - ending interview');
                            updateStatus('Interview completed! Thank you for your time.', 'success');
                            document.getElementById('completeInterview').click();
                        } else {
                            console.log('Moving to next essential question');
                            setTimeout(() => {
                                startNextQuestion();
                            }, 1000);
                        }
                    }, 500);
                });
            } else {
                setTimeout(() => {
                    startNextQuestion();
                }, 1000);
            }
        } else {
            console.log('No transcript and no recording - starting fresh recording');
            // If no transcript and no recording, start recording and wait for candidate response
            setTimeout(() => {
                startRecordingForCandidate();
                updateStatus(`I'd love to hear your thoughts! Please respond when you're ready.`, 'info');
                document.getElementById('stopAnswer').textContent = 'Stop Answering';
            }, 1000);
        }
    }
    
    // Update button state
    document.getElementById('stopAnswer').disabled = false;
    document.getElementById('stopAnswer').textContent = 'Stop Answering';
}

// Simple acknowledgment function (NO FOLLOW-UPS)
function generateSimpleAcknowledgment(userAnswer) {
    console.log('generateSimpleAcknowledgment called with:', userAnswer);
    
    // Check if we're at the end of essential questions (including the closing question)
    if (essentialQuestionsCovered.size >= essentialQuestions.length) {
        console.log('All essential questions covered, generating closing response');
        return "Thank you for sharing that with me! The company will review your question and contact you via email with a detailed response. Your interview is now complete - thank you for your time and thoughtful responses throughout our conversation.";
    }
    
    // Generate simple acknowledgments that lead directly to next question
    const answer = userAnswer.toLowerCase();
    let acknowledgment = '';
    
    if (answer.includes('team') || answer.includes('collaborate') || answer.includes('work with')) {
        acknowledgment = "That's such a great example of teamwork! Thank you for sharing that with me. ";
    } else if (answer.includes('problem') || answer.includes('solve') || answer.includes('challenge')) {
        acknowledgment = "I love how you approached that problem! Thank you for sharing that insight. ";
    } else if (answer.includes('learn') || answer.includes('grow') || answer.includes('improve')) {
        acknowledgment = "It's wonderful that you learned that! Thank you for sharing your growth journey. ";
    } else if (answer.includes('success') || answer.includes('achieve') || answer.includes('accomplish')) {
        acknowledgment = "That's such an impressive achievement! Thank you for sharing that with me. ";
    } else if (answer.includes('passion') || answer.includes('love') || answer.includes('enjoy')) {
        acknowledgment = "Your passion really comes through! Thank you for sharing that enthusiasm. ";
    } else if (answer.includes('experience') || answer.includes('worked') || answer.includes('project')) {
        acknowledgment = "That experience sounds really valuable! Thank you for sharing that with me. ";
    } else if (answer.includes('customer') || answer.includes('client') || answer.includes('help')) {
        acknowledgment = "That's such a great example of customer service! Thank you for sharing that approach. ";
    } else if (answer.includes('lead') || answer.includes('manage') || answer.includes('supervise')) {
        acknowledgment = "I can see your leadership skills! Thank you for sharing that perspective. ";
    } else {
        acknowledgment = "That's really interesting! Thank you for sharing that with me. ";
    }
    
    // Add transition to next question
    acknowledgment += "Now, I'd love to learn more about another aspect of your experience.";
    
    console.log('Simple acknowledgment generated:', acknowledgment);
    return acknowledgment;
}

function completeInterview() {
    console.log('Complete Interview button clicked');
    
    // Stop any ongoing recording
    if (isRecording && mediaRecorder) {
        console.log('Stopping recording for interview completion...');
        mediaRecorder.stop();
        isRecording = false;
    }
    
    // Stop speech recognition and synthesis
    stopSpeechRecognition();
    if (speechSynthesis) {
        speechSynthesis.cancel();
        isAISpeaking = false;
    }
    
    // REMOVED: consolidateInterviewVideo() call - no longer needed
    // Individual recordings contain the complete interview content
    console.log('✅ Skipping video consolidation - using individual recordings for complete content');

    // Check if this is the initial completion (show rating modal) or final completion (after rating)
    if (!window.interviewConfig.candidateFeedback && !window.interviewConfig.ratingSkipped) {
        // This is the initial completion - show rating modal
        console.log('Initial completion - showing rating modal');
        
        // Update UI immediately
        const startButton = document.getElementById('startInterview');
        const stopButton = document.getElementById('stopAnswer');
        const completeButton = document.getElementById('completeInterview');
        
        if (startButton) startButton.style.display = 'block';
        if (stopButton) stopButton.style.display = 'none';
        if (completeButton) {
            completeButton.style.display = 'none';
            completeButton.disabled = true;
        }
        
        // Show completion message
        updateStatus('🎉 Interview completed! Please rate your experience before finalizing.', 'success');
        
        // Add final completion message to chat
        addChatMessage('AI Interviewer', 'Thank you for completing the interview! Please take a moment to rate your experience, then click the complete button to finalize.', 'ai');
        
        // Show rating modal
        $('#interviewRatingModal').modal('show');
        
        // Set interview as completed (but not finalized)
        isInterviewStarted = false;
    } else {
        // This is the final completion after rating - finalize the interview
        console.log('Final completion after rating - finalizing interview');
        
        // Update status
        updateStatus('🎉 Finalizing your interview...', 'success');
        
        // Call finalize interview function
        finalizeInterview();
    }
}

function finalizeInterview() {
    console.log('Finalizing interview with rating data...');
    
    // Save interview data with chat messages and rating
    saveInterviewData();
}

function saveInterviewData() {
    console.log('Saving interview data...', {
        chatMessages: chatMessages.length,
        aiResponses: aiResponses.length,
        candidateResponses: candidateResponses.length,
        totalQuestions: essentialQuestions.length,
        coveredQuestions: essentialQuestionsCovered.size
    });
    
    const interviewData = {
        token: interviewToken,
        registrationId: registrationId,
        chatMessages: chatMessages,
        aiResponses: aiResponses,
        candidateResponses: candidateResponses,
        conversationTopics: Array.from(conversationTopics),
        essentialQuestionsCovered: Array.from(essentialQuestionsCovered),
        totalEssentialQuestions: essentialQuestions.length,
        totalConversationTurns: aiResponses.length,
        jobData: jobData,
        cvData: cvData,
        startTime: startTime,
        endTime: endTime,
        // Additional tracking data
        totalQuestionsAnswered: candidateResponses.length,
        averageResponseDuration: candidateResponses.length > 0 ? 
            candidateResponses.reduce((sum, resp) => sum + (resp.duration || 0), 0) / candidateResponses.length : 0,
        interviewDuration: endTime && startTime ? (endTime - startTime) / 1000 : 0,
        // Include candidate feedback and voice selection
        candidateFeedback: window.interviewConfig.candidateFeedback || null,
        selectedVoice: window.interviewConfig.selectedVoice || 'female'
    };
    
    updateStatus('Saving interview data...', 'info');
    
    fetch('/eris/complete-interview.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(interviewData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateStatus('✅ Interview completed successfully! Your responses have been saved. You will receive an email confirmation shortly.', 'success');
            
            // Show completion summary
            const summary = `
                <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 10px 0;">
                    <h4>🎉 Interview Summary</h4>
                    <p><strong>Questions Answered:</strong> ${candidateResponses.length}</p>
                    <p><strong>Total Duration:</strong> ${Math.round((endTime - startTime) / 1000 / 60)} minutes</p>
                    <p><strong>Status:</strong> Completed and saved</p>
                </div>
            `;
            
            // Add summary to chat
            addChatMessage('System', summary, 'system');
            
            // Disable all buttons to prevent further interaction
            const buttons = ['startInterview', 'stopAnswer', 'completeInterview'];
            buttons.forEach(buttonId => {
                const button = document.getElementById(buttonId);
                if (button) {
                    button.disabled = true;
                    button.style.opacity = '0.5';
                }
            });
            
        } else {
            console.error('Error saving interview data:', data.error);
            updateStatus(`❌ Error saving interview data: ${data.error}`, 'danger');
        }
    })
    .catch(error => {
        console.error('Error completing interview:', error);
        updateStatus(`❌ Error completing interview: ${error.message}`, 'danger');
    });
}

function updateProgressDisplay() {
    const progressInfo = document.querySelector('.essential-questions-progress .progress-info');
    if (progressInfo) {
        const currentQuestionSpan = progressInfo.querySelector('.current-question');
        const progressBarFill = progressInfo.querySelector('.progress-bar-fill');
        if (currentQuestionSpan && progressBarFill) {
            const nextQuestion = essentialQuestionsCovered.size + 1;
            currentQuestionSpan.textContent = `Question ${nextQuestion} of ${essentialQuestions.length}`;
            progressBarFill.style.width = `${(nextQuestion/essentialQuestions.length)*100}%`;
        }
    }
}

function updateStatus(message, type) {
    const statusElement = document.getElementById('interview-status'); // Changed back to original ID
    if (statusElement) {
        statusElement.textContent = message;
        statusElement.className = `alert alert-${type || 'info'} mt-3`; // Reverted to original Bootstrap classes
        statusElement.style.display = 'block';
    }
    console.log(`Status: ${message} (${type})`);
}

// Test recording functionality
function testRecording() {
    console.log('Testing recording functionality...');
    const stream = video.srcObject;
    if (!stream) {
        console.error('No video stream available');
        updateStatus('No video stream available for testing', 'danger');
        return;
    }
    
    try {
        const testRecorder = new MediaRecorder(stream);
        const testChunks = [];
        
        testRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) {
                testChunks.push(e.data);
                console.log('Test recording chunk:', e.data.size, 'bytes');
            }
        };
        
        testRecorder.onstart = () => {
            console.log('Test recording started');
            updateStatus('Test recording started...', 'info');
        };
        
        testRecorder.onstop = () => {
            console.log('Test recording stopped');
            if (testChunks.length > 0) {
                const testBlob = new Blob(testChunks, { type: 'video/webm' });
                console.log('Test recording blob size:', testBlob.size, 'bytes');
                updateStatus(`Test recording successful: ${testBlob.size} bytes`, 'success');
            } else {
                console.warn('No test recording chunks');
                updateStatus('Test recording failed: no data', 'warning');
            }
        };
        
        testRecorder.start(1000);
        setTimeout(() => {
            testRecorder.stop();
        }, 3000);
        
    } catch (error) {
        console.error('Test recording error:', error);
        updateStatus('Test recording failed: ' + error.message, 'danger');
    }
}

// Test basic functionality
function testBasicFunctionality() {
    console.log('Testing basic functionality...');
    console.log('Video element:', !!video);
    console.log('Canvas element:', !!canvas);
    console.log('Interview token:', interviewToken);
    console.log('Registration ID:', registrationId);
    console.log('Questions array:', questions.length);
    console.log('Essential questions:', essentialQuestions.length);
    console.log('Is interview started:', isInterviewStarted);
    
    // Test status update
    updateStatus('Basic functionality test completed', 'info');
    
    // Test if elements exist
    const startButton = document.getElementById('startInterview');
    const stopButton = document.getElementById('stopAnswer');
    const completeButton = document.getElementById('completeInterview');
    
    console.log('Start button:', !!startButton);
    console.log('Stop button:', !!stopButton);
    console.log('Complete button:', !!completeButton);
    
    if (startButton) {
        console.log('Start button disabled:', startButton.disabled);
        console.log('Start button display:', startButton.style.display);
    }
}

// Test minimal upload functionality
async function testMinimalUpload() {
    console.log('Testing minimal upload functionality...');
    
    // Create a test blob
    const testBlob = new Blob(['test recording data'], { type: 'video/webm' });
    console.log('Test blob created:', testBlob.size, 'bytes');
    
    const formData = new FormData();
    formData.append('video', testBlob, 'test_recording.webm');
    
    console.log('Testing minimal upload...');
    
    try {
        const response = await fetch('/eris/minimal-upload.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Minimal upload response:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (data.success) {
                console.log('Minimal upload successful:', data);
                updateStatus('Minimal upload successful!', 'success');
            } else {
                console.error('Minimal upload failed:', data.error);
                updateStatus(`Minimal upload failed: ${data.error}`, 'danger');
            }
        } catch (e) {
            console.error('Failed to parse JSON response:', text);
            updateStatus('Minimal upload failed: Invalid response from server', 'danger');
        }
    } catch (error) {
        console.error('Minimal upload error:', error);
        updateStatus(`Minimal upload error: ${error.message}`, 'danger');
    }
}

// Test upload functionality
async function testUpload() {
    console.log('Testing upload functionality...');
    
    // Create a test blob
    const testBlob = new Blob(['test recording data'], { type: 'video/webm' });
    console.log('Test blob created:', testBlob.size, 'bytes');
    
    const formData = new FormData();
    formData.append('video', testBlob, 'test_recording.webm');
    
    console.log('Testing simple upload...');
    
    try {
        const response = await fetch('/eris/simple-upload.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Simple upload response:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (data.success) {
                console.log('Simple upload successful:', data);
                updateStatus('Simple upload successful!', 'success');
                
                // Now test the full upload
                await testFullUpload();
            } else {
                console.error('Simple upload failed:', data.error);
                updateStatus(`Simple upload failed: ${data.error}`, 'danger');
            }
        } catch (e) {
            console.error('Failed to parse JSON response:', text);
            updateStatus('Simple upload failed: Invalid response from server', 'danger');
        }
    } catch (error) {
        console.error('Simple upload error:', error);
        updateStatus(`Simple upload error: ${error.message}`, 'danger');
    }
}

// Test full upload functionality
async function testFullUpload() {
    console.log('Testing full upload functionality...');
    
    if (!interviewToken) {
        console.error('No interview token available');
        updateStatus('No interview token available for full upload test', 'danger');
        return;
    }
    
    // Create a test blob
    const testBlob = new Blob(['test recording data'], { type: 'video/webm' });
    console.log('Test blob created:', testBlob.size, 'bytes');
    
    const formData = new FormData();
    formData.append('video', testBlob, 'test_recording.webm');
    formData.append('conversationTurn', 1);
    formData.append('questionType', 'test');
    formData.append('currentEssentialQuestion', 1);
    formData.append('duration', 5.0);
    formData.append('token', interviewToken);
    formData.append('registrationId', registrationId);
    
    console.log('FormData created with token:', interviewToken);
    
    try {
        const response = await fetch('/eris/upload-interview.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Full upload response:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (data.success) {
                console.log('Full upload successful:', data);
                updateStatus('Full upload successful!', 'success');
            } else {
                console.error('Full upload failed:', data.error);
                updateStatus(`Full upload failed: ${data.error}`, 'danger');
            }
        } catch (e) {
            console.error('Failed to parse JSON response:', text);
            updateStatus('Full upload failed: Invalid response from server', 'danger');
        }
    } catch (error) {
        console.error('Full upload error:', error);
        updateStatus(`Full upload error: ${error.message}`, 'danger');
    }
}

// Check token validity
async function checkTokenValidity() {
    if (!interviewToken) {
        console.error('No interview token available');
        return false;
    }
    
    try {
        const response = await fetch('/eris/check-token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ token: interviewToken })
        });
        
        const data = await response.json();
        console.log('Token validity check:', data);
        return data.valid;
    } catch (error) {
        console.error('Error checking token validity:', error);
        return false;
    }
}

// Extend token expiry
async function extendTokenExpiry() {
    if (!interviewToken) {
        console.error('No interview token available');
        return false;
    }
    
    try {
        const response = await fetch('/eris/extend-token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ token: interviewToken })
        });
        
        const data = await response.json();
        console.log('Token extension result:', data);
        return data.success;
    } catch (error) {
        console.error('Error extending token:', error);
        return false;
    }
}

function resetSilenceTimeout() {
    if (silenceTimeout) clearTimeout(silenceTimeout);
    // Only set timeout for very long periods of inactivity (5 minutes)
    // This prevents the system from hanging indefinitely while allowing candidates to think
    silenceTimeout = setTimeout(() => {
        if (isRecording && isInterviewStarted) {
            console.log('Long period of inactivity detected, prompting user to continue or stop');
            updateStatus('You\'ve been quiet for a while. Please continue speaking or press "Stop Answering" when you\'re done.', 'warning');
            // Don't auto-stop, just give a gentle reminder
        }
    }, SILENCE_DURATION_MS);
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded - Starting interview initialization');
    
    // Check if interviewConfig is available (from PHP)
    if (window.interviewConfig) {
        console.log('Using interviewConfig from PHP:', window.interviewConfig);
        
        const jobData = window.interviewConfig.jobData || {};
        const token = window.interviewConfig.interviewToken || '';
        const regId = window.interviewConfig.registrationId || '';
        
        console.log('Interview initialization data:', {
            token: token,
            registrationId: regId,
            jobDataKeys: Object.keys(jobData)
        });
        
        if (!token) {
            console.error('No interview token found in interviewConfig!');
            updateStatus('Error: No interview token found. Please check the interview link.', 'danger');
            return;
        }
        
        console.log('Calling initializeInterview...');
        initializeInterview(jobData, token, regId).catch(error => {
            console.error('Error during interview initialization:', error);
            updateStatus('Error initializing interview: ' + error.message, 'danger');
        });
    } else {
        // Fallback to looking for elements (for backward compatibility)
        console.log('interviewConfig not found, looking for elements...');
        
        const jobDataElement = document.getElementById('job-data');
        const tokenElement = document.getElementById('interview-token');
        const regIdElement = document.getElementById('registration-id');

        console.log('Initializing interview with elements:', {
            jobDataElement: !!jobDataElement,
            tokenElement: !!tokenElement,
            regIdElement: !!regIdElement
        });

        const jobData = jobDataElement ? JSON.parse(jobDataElement.textContent) : {};
        const token = tokenElement ? tokenElement.textContent.trim() : '';
        const regId = regIdElement ? regIdElement.textContent.trim() : '';
        
        console.log('Interview initialization data:', {
            token: token,
            registrationId: regId,
            jobDataKeys: Object.keys(jobData)
        });
        
        if (!token) {
            console.error('No interview token found!');
            updateStatus('Error: No interview token found. Please check the interview link.', 'danger');
            return;
        }
        
        console.log('Calling initializeInterview...');
        initializeInterview(jobData, token, regId).catch(error => {
            console.error('Error during interview initialization:', error);
            updateStatus('Error initializing interview: ' + error.message, 'danger');
        });
    }
    
    // Add test function to global scope for debugging
    window.testAIInteraction = function() {
        console.log('Testing AI interaction flow...');
        
        // Simulate a candidate response
        const testResponse = "I have experience working with teams and solving problems. I love learning new things and I'm passionate about helping customers.";
        
        // Test the AI acknowledgment generation
        const aiResponse = generateSimpleAcknowledgment(testResponse);
        console.log('AI Acknowledgment:', aiResponse);
        
        // Add to chat
        addChatMessage('You', testResponse, 'user');
        addChatMessage('AI Interviewer', aiResponse, 'ai');
        
        updateStatus('AI interaction test completed. Check console for details.', 'success');
    };
    
    // Add a simple test for the stopAnswer function
    window.testStopAnswer = function() {
        console.log('Testing stopAnswer function...');
        
        // Simulate having a transcript
        currentTranscript = "I have experience working with teams and solving problems.";
        
        // Call stopAnswer
        stopAnswer();
        
        updateStatus('stopAnswer test completed. Check console for details.', 'success');
    };
    
    // Add a test to manually trigger the stop button
    window.testStopButton = function() {
        console.log('Testing stop button manually...');
        const stopButton = document.getElementById('stopAnswer');
        if (stopButton) {
            console.log('Found stop button, clicking it...');
            stopButton.click();
        } else {
            console.error('Stop button not found!');
        }
    };
    
    // Add a test to check speech recognition status
    window.testSpeechRecognition = function() {
        console.log('Testing speech recognition...');
        console.log('Speech recognition support:', {
            'webkitSpeechRecognition': 'webkitSpeechRecognition' in window,
            'SpeechRecognition': 'SpeechRecognition' in window,
            'speechRecognition object': !!speechRecognition,
            'isListening': isListening
        });
        
        if (speechRecognition) {
            console.log('Speech recognition object details:', {
                'continuous': speechRecognition.continuous,
                'interimResults': speechRecognition.interimResults,
                'lang': speechRecognition.lang,
                'state': speechRecognition.state || 'unknown'
            });
        }
        
        updateStatus('Speech recognition test completed. Check console for details.', 'success');
    };
});

/**
 * Skip meaningless consolidation - individual recordings contain complete interview
 * The 5-second consolidation was creating incomplete videos instead of useful content
 */
async function consolidateInterviewVideo() {
    console.log('📋 Interview completion: Skipping 5-second consolidation (creates incomplete video)');
    console.log('✅ Individual question recordings contain the actual interview content and will be used for download');
    
    try {
        // Instead of creating a useless 5-second video, just mark interview as complete
        // The individual recordings already saved contain all the actual interview Q&A content
        
        updateStatus('✅ Interview completed successfully! Your recordings have been saved.', 'success');
        console.log('✅ Interview marked as complete. Download will use individual recordings with full content.');
        
        // Note: Download system now prioritizes individual recordings over consolidated videos
        // This ensures users get the complete interview content, not just a 5-second clip
        
    } catch (error) {
        console.error('Error during interview completion:', error);
        updateStatus('Interview completed with warnings', 'warning');
    }
}

/**
 * Legacy function - no longer creates consolidated videos
 * Download system now uses individual recordings which contain complete interview content
 */
async function saveConsolidatedVideo(blob) {
    console.log('saveConsolidatedVideo: Skipped - using individual recordings instead');
    // No longer needed - individual recordings contain the complete interview content
    // Download system prioritizes individual recordings over consolidated videos
}