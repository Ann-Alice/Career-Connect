<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add error logging
error_log("Starting interview.php processing");
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Script Name: " . $_SERVER['SCRIPT_NAME']);
error_log("Document Root: " . $_SERVER['DOCUMENT_ROOT']);
error_log("Token: " . (isset($_GET['token']) ? $_GET['token'] : 'not set'));

require_once('include/initialize.php');
error_log("After initialize.php");

require_once('include/config.php');
error_log("After config.php");
error_log("Web Root: " . web_root);

require_once('include/database.php');
error_log("After database.php");

require_once('include/db_object.php');
error_log("After db_object.php");

require_once('include/session.php');
error_log("After session.php");

require_once('include/functions.php');
error_log("After functions.php");

// Validate token
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($token)) {
    error_log("No token provided");
    die("Invalid interview link.");
}

// Check if token is valid and not expired
$sql = "SELECT i.*, r.APPLICANT, r.REGISTRATIONID, r.APPLICANTID,
        a.DEGREE, a.FNAME, a.LNAME,
        j.OCCUPATIONTITLE, j.JOBDESCRIPTION as DESCRIPTION, j.QUALIFICATION_WORKEXPERIENCE as REQUIREMENTS
        FROM tblinterviewinvitations i
        JOIN tbljobregistration r ON i.REGISTRATIONID = r.REGISTRATIONID
        JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID
        JOIN tbljob j ON i.JOBID = j.JOBID
        WHERE i.TOKEN = '{$token}'
        AND i.EXPIRY_DATE > NOW()";
error_log("SQL Query: " . $sql);
$mydb->setQuery($sql);
$invitation = $mydb->loadSingleResult();

if (!$invitation) {
    error_log("Invalid or expired token: " . $token);
    die("This interview link is invalid or has expired.");
}

// Check if interview is already completed
$sql = "SELECT INTERVIEW_RESULTS FROM tbljobregistration
        WHERE REGISTRATIONID = '{$invitation->REGISTRATIONID}'";
$mydb->setQuery($sql);
$result = $mydb->loadSingleResult();

if ($result && $result->INTERVIEW_RESULTS) {
    die("You have already completed this interview.");
}

// Prepare job data for questions
$jobData = [
    'title' => $invitation->OCCUPATIONTITLE,
    'description' => $invitation->DESCRIPTION,
    'requirements' => $invitation->REQUIREMENTS,
    'education' => $invitation->DEGREE,
    'applicant' => $invitation->FNAME . ' ' . $invitation->LNAME
];

// Convert PHP array to JSON for JavaScript
$jobDataJson = json_encode($jobData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>AI Interview - <?php echo $invitation->OCCUPATIONTITLE; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo web_root; ?>theme/css/ai-interview.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.0.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="row">
            <!-- Left Column - Main Interview Interface -->
            <div class="col-lg-8">
                <div class="ai-interview-card">
                    <div class="interview-header">
                        <h2>AI Interview</h2>
                        <p class="text-muted">Position: <?php echo $invitation->OCCUPATIONTITLE; ?></p>
                    </div>
                    
                    <div class="interview-body">
                        <div class="camera-preview">
                            <video id="video" width="640" height="480" autoplay muted></video>
                            <canvas id="overlay"></canvas>
                        </div>
                        
                        <!-- Real-time transcript display -->
                        <div id="transcript-display" class="transcript-container mt-3" style="display: none;">
                            <h5><i class="fas fa-microphone"></i> Your Response</h5>
                            <div id="live-transcript" class="live-transcript"></div>
                        </div>
                        
                        <div class="interview-controls">
                            <button id="startInterview" class="btn btn-success">Start Interview</button>
                            <button id="stopAnswer" class="btn btn-danger" disabled>Stop Answering</button>
                            <button id="completeInterview" class="btn btn-primary" disabled>Complete Interview</button>
                        </div>
                        <div id="interview-status" class="alert alert-info mt-3" style="display: none;"></div>
                        
                        <div class="interview-questions mt-4">
                            <h3>Questions</h3>
                            <div id="questions-container"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Chat Interface -->
            <div class="col-lg-4">
                <div class="ai-interview-card chat-sidebar">
                    <div class="interview-chat">
                        <h3><i class="fas fa-comments"></i> Interview Chat</h3>
                        <div class="chat-container">
                            <div id="chat-container" class="chat-messages"></div>
                            <div id="chat-status" class="chat-status">Ready to start</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voice Selection Modal -->
    <div class="modal fade" id="voiceSelectionModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose Your Interview Voice</h5>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Please select your preferred voice for the AI interviewer:</p>
                    
                    <div class="voice-options">
                        <div class="voice-option" data-voice="male">
                            <div class="voice-card">
                                <div class="voice-icon">
                                    <i class="fas fa-male"></i>
                                </div>
                                <h5>Male Voice</h5>
                                <p class="text-muted">Professional and authoritative</p>
                                <div class="voice-status" id="male-voice-status">
                                    <small class="text-info">Loading available voices...</small>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" onclick="previewVoice('male')">
                                    <i class="fas fa-play"></i> Preview
                                </button>
                            </div>
                        </div>
                        
                        <div class="voice-option" data-voice="female">
                            <div class="voice-card">
                                <div class="voice-icon">
                                    <i class="fas fa-female"></i>
                                </div>
                                <h5>Female Voice</h5>
                                <p class="text-muted">Friendly and approachable</p>
                                <div class="voice-status" id="female-voice-status">
                                    <small class="text-info">Loading available voices...</small>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" onclick="previewVoice('female')">
                                    <i class="fas fa-play"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmVoiceSelection" disabled>
                        Continue with Selected Voice
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Interview Rating Modal -->
    <div class="modal fade" id="interviewRatingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rate Your Interview Experience</h5>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Please take a moment to rate your AI interview experience:</p>
                    
                    <div class="rating-section mb-4">
                        <h6>Overall Experience</h6>
                        <div class="star-rating">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                        <small class="text-muted">Click to rate from 1 (poor) to 5 (excellent)</small>
                    </div>
                    
                    <div class="rating-section mb-4">
                        <h6>Interview Difficulty</h6>
                        <select class="form-control" id="difficultyRating">
                            <option value="">Select difficulty level</option>
                            <option value="Very Easy">Very Easy</option>
                            <option value="Easy">Easy</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Difficult">Difficult</option>
                            <option value="Very Difficult">Very Difficult</option>
                        </select>
                    </div>
                    
                    <div class="rating-section mb-4">
                        <h6>Voice Quality</h6>
                        <div class="star-rating" id="voiceRating">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                        <small class="text-muted">How clear was the AI interviewer's voice?</small>
                    </div>
                    
                    <div class="rating-section mb-4">
                        <h6>Additional Comments</h6>
                        <textarea class="form-control" id="interviewComments" rows="3" 
                                  placeholder="Share your thoughts about the interview experience..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="skipRating">Skip Rating</button>
                    <button type="button" class="btn btn-primary" id="submitRating">Submit Rating</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Voice Selection Styles */
        .voice-options {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        .voice-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .voice-option:hover {
            transform: translateY(-5px);
        }
        
        .voice-option.selected .voice-card {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .voice-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .voice-icon {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .voice-option.selected .voice-icon {
            color: #007bff;
        }
        
        /* Rating Styles */
        .star-rating {
            font-size: 24px;
            color: #ffc107;
            cursor: pointer;
        }
        
        .star-rating i {
            margin-right: 5px;
            transition: all 0.2s ease;
        }
        
        .star-rating i:hover {
            transform: scale(1.2);
        }
        
        .star-rating i.fas {
            color: #ffc107;
        }
        
        .star-rating i.far {
            color: #e9ecef;
        }
        
        /* Transcript Display Styles */
        .transcript-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
        }
        
        .live-transcript {
            min-height: 60px;
            max-height: 120px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px;
            font-style: italic;
            color: #495057;
        }
        
        .live-transcript:empty::before {
            content: "Your response will appear here as you speak...";
            color: #6c757d;
        }
        
        /* Complete Interview Button Styles */
        .btn-complete-final {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }
        
        .btn-complete-final:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>

    <script>
        window.interviewConfig = {
            jobData: <?php echo $jobDataJson; ?>,
            interviewToken: '<?php echo $token; ?>',
            registrationId: '<?php echo $invitation->REGISTRATIONID; ?>',
            webRoot: '<?php echo web_root; ?>'
        };
        
        // Voice selection functionality
        let selectedVoice = null;
        
        function previewVoice(voice) {
            // Stop any current speech
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
            }
            
            // Create a simple preview message
            const message = "Hello, I'm your AI interviewer. How are you today?";
            const utterance = new SpeechSynthesisUtterance(message);
            
            // Get available voices
            const voices = window.speechSynthesis.getVoices();
            console.log('Available voices:', voices.map(v => `${v.name} (${v.lang})`));
            
            if (voice === 'male') {
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
                    console.log('Selected male voice:', maleVoice.name);
                } else {
                    // Fallback: use a deeper pitch for male voice
                    utterance.pitch = 0.8;
                    utterance.rate = 0.9;
                    console.log('No male voice found, using pitch adjustment');
                }
            } else if (voice === 'female') {
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
                    console.log('Selected female voice:', femaleVoice.name);
                } else {
                    // Fallback: use a higher pitch for female voice
                    utterance.pitch = 1.3;
                    utterance.rate = 1.0;
                    console.log('No female voice found, using pitch adjustment');
                }
            }
            
            // Set additional properties for better distinction
            utterance.volume = 1.0;
            
            // Speak the preview
            window.speechSynthesis.speak(utterance);
        }
        
        // Initialize voice selection
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - initializing voice selection');
            
            // Test jQuery and Bootstrap
            if (typeof $ !== 'undefined') {
                console.log('jQuery is loaded');
            } else {
                console.error('jQuery is NOT loaded');
            }
            
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap is loaded');
            } else {
                console.error('Bootstrap is NOT loaded');
            }
            
            // Test if modals exist
            const voiceModal = document.getElementById('voiceSelectionModal');
            const ratingModal = document.getElementById('interviewRatingModal');
            
            if (voiceModal) {
                console.log('Voice selection modal found');
            } else {
                console.error('Voice selection modal NOT found');
            }
            
            if (ratingModal) {
                console.log('Rating modal found');
            } else {
                console.error('Rating modal NOT found');
            }
            
            // Load voices and show available options
            function loadVoices() {
                const voices = window.speechSynthesis.getVoices();
                console.log('Available voices:', voices.map(v => `${v.name} (${v.lang})`));
                
                // Display available voices in console for debugging
                const maleVoices = voices.filter(v => 
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
                
                const femaleVoices = voices.filter(v => 
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
                
                console.log('Male voices found:', maleVoices.map(v => v.name));
                console.log('Female voices found:', femaleVoices.map(v => v.name));
                
                // Update visual indicators
                const maleStatus = document.getElementById('male-voice-status');
                const femaleStatus = document.getElementById('female-voice-status');
                
                if (maleStatus) {
                    if (maleVoices.length > 0) {
                        maleStatus.innerHTML = `<small class="text-success">✓ ${maleVoices[0].name} available</small>`;
                    } else {
                        maleStatus.innerHTML = `<small class="text-warning">⚠ Using pitch adjustment for male voice</small>`;
                    }
                }
                
                if (femaleStatus) {
                    if (femaleVoices.length > 0) {
                        femaleStatus.innerHTML = `<small class="text-success">✓ ${femaleVoices[0].name} available</small>`;
                    } else {
                        femaleStatus.innerHTML = `<small class="text-warning">⚠ Using pitch adjustment for female voice</small>`;
                    }
                }
                
                // Show voice selection modal after voices are loaded
                $('#voiceSelectionModal').modal('show');
            }
            
            // Load voices immediately if available, otherwise wait for them to load
            if (window.speechSynthesis.getVoices().length > 0) {
                loadVoices();
            } else {
                window.speechSynthesis.onvoiceschanged = loadVoices;
            }
            
            // Voice selection handlers
            document.querySelectorAll('.voice-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.voice-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedVoice = this.dataset.voice;
                    document.getElementById('confirmVoiceSelection').disabled = false;
                });
            });
            
            // Confirm voice selection
            document.getElementById('confirmVoiceSelection').addEventListener('click', function() {
                if (selectedVoice) {
                    window.interviewConfig.selectedVoice = selectedVoice;
                    $('#voiceSelectionModal').modal('hide');
                    // Enable start interview button
                    document.getElementById('startInterview').disabled = false;
                }
            });
            
            // Rating functionality
            document.querySelectorAll('.star-rating').forEach(rating => {
                rating.addEventListener('click', function(e) {
                    if (e.target.tagName === 'I') {
                        const stars = this.querySelectorAll('i');
                        const ratingValue = parseInt(e.target.dataset.rating);
                        
                        stars.forEach((star, index) => {
                            if (index < ratingValue) {
                                star.classList.remove('far');
                                star.classList.add('fas');
                            } else {
                                star.classList.remove('fas');
                                star.classList.add('far');
                            }
                        });
                        
                        this.dataset.value = ratingValue;
                    }
                });
            });
            
            // Submit rating
            document.getElementById('submitRating').addEventListener('click', function() {
                const overallRating = document.querySelector('.star-rating').dataset.value || 0;
                const difficultyRating = document.getElementById('difficultyRating').value;
                const voiceRating = document.getElementById('voiceRating').dataset.value || 0;
                const comments = document.getElementById('interviewComments').value;
                
                // Store rating data
                window.interviewConfig.candidateFeedback = {
                    overallRating: parseInt(overallRating),
                    difficultyRating: difficultyRating,
                    voiceRating: parseInt(voiceRating),
                    comments: comments,
                    timestamp: new Date().toISOString()
                };
                
                // Set flag to indicate rating was submitted
                window.interviewConfig.ratingSubmitted = true;
                
                $('#interviewRatingModal').modal('hide');
                
                // Show success message and enable complete button
                updateStatus('✅ Thank you for your feedback! Please click "Complete Interview" to finalize your interview.', 'success');
                
                // Show and enable the complete interview button
                const completeButton = document.getElementById('completeInterview');
                if (completeButton) {
                    completeButton.style.display = 'inline-block';
                    completeButton.disabled = false;
                    completeButton.textContent = 'Complete Interview';
                    completeButton.className = 'btn btn-complete-final';
                }
                
                // Add a message to the chat
                addChatMessage('System', 'Thank you for rating your interview experience! Please click "Complete Interview" to finalize your interview.', 'system');
            });
            
            // Skip rating
            document.getElementById('skipRating').addEventListener('click', function() {
                $('#interviewRatingModal').modal('hide');
                
                // Set flag to indicate rating was skipped
                window.interviewConfig.ratingSkipped = true;
                
                // Show message and enable complete button
                updateStatus('✅ Interview ready to complete! Please click "Complete Interview" to finalize your interview.', 'success');
                
                // Show and enable the complete interview button
                const completeButton = document.getElementById('completeInterview');
                if (completeButton) {
                    completeButton.style.display = 'inline-block';
                    completeButton.disabled = false;
                    completeButton.textContent = 'Complete Interview';
                    completeButton.className = 'btn btn-complete-final';
                }
                
                // Add a message to the chat
                addChatMessage('System', 'Interview ready to complete! Please click "Complete Interview" to finalize your interview.', 'system');
            });
            
            // Initially disable start interview button until voice is selected
            document.getElementById('startInterview').disabled = true;
        });
    </script>
    <script src="<?php echo web_root; ?>theme/js/ai-interview.js?v=6.4&t=<?php echo time(); ?>"></script>
</body>
</html> 