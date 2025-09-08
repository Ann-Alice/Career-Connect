// Simplified Video Recording Fix for AI Interview
// This replaces the complex recording logic with a more reliable approach

console.log('🎥 Loading simplified video recording system...');

// Override the existing startRecordingForCandidate function with a simpler, more reliable version
window.startRecordingForCandidateFixed = function(nextStepCallback) {
    console.log('🔴 Starting simplified recording...');
    
    // Clear previous recording data
    if (window.mediaRecorder && window.mediaRecorder.state !== 'inactive') {
        try { window.mediaRecorder.stop(); } catch (e) { console.log('Stopped previous recorder'); }
    }
    window.recordedChunks = [];
    window.isRecording = true;
    
    // Get video stream
    const video = document.getElementById('video') || document.querySelector('video');
    if (!video || !video.srcObject) {
        console.error('❌ No video stream found');
        alert('Error: Camera not available. Please refresh the page and allow camera access.');
        return;
    }
    
    const stream = video.srcObject;
    console.log('✅ Video stream found:', stream);
    
    try {
        // Create MediaRecorder with fallback MIME types
        let mimeType = 'video/webm;codecs=vp8,opus';
        if (!MediaRecorder.isTypeSupported(mimeType)) {
            mimeType = 'video/webm';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'video/mp4';
            }
        }
        
        console.log('📹 Using MIME type:', mimeType);
        
        window.mediaRecorder = new MediaRecorder(stream, {
            mimeType: mimeType,
            videoBitsPerSecond: 500000, // Lower bitrate for reliability
            audioBitsPerSecond: 64000
        });
        
        // Record data chunks
        window.mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                window.recordedChunks.push(event.data);
                console.log(`📊 Chunk recorded: ${event.data.size} bytes (Total: ${window.recordedChunks.length} chunks)`);
                
                // Update UI with recording info
                updateRecordingStatus(`Recording... ${window.recordedChunks.length} segments captured`);
            }
        };
        
        // Recording started
        window.mediaRecorder.onstart = () => {
            console.log('✅ Recording STARTED successfully');
            window.startTime = Date.now();
            
            // Update UI
            updateRecordingStatus('🔴 Recording in progress... Speak now!');
            
            // Enable stop button
            const stopBtn = document.getElementById('stopAnswer');
            if (stopBtn) {
                stopBtn.disabled = false;
                stopBtn.style.backgroundColor = '#dc3545';
                stopBtn.style.color = 'white';
                stopBtn.textContent = '⏹️ Stop Recording';
                console.log('✅ Stop button enabled');
            }
            
            // Add visual recording indicator
            if (video) {
                video.style.border = '3px solid #dc3545';
                video.style.borderRadius = '10px';
            }
        };
        
        // Recording stopped
        window.mediaRecorder.onstop = async () => {
            window.endTime = Date.now();
            const duration = (window.endTime - window.startTime) / 1000;
            
            console.log(`⏹️ Recording STOPPED. Duration: ${duration}s, Chunks: ${window.recordedChunks.length}`);
            
            // Remove visual indicators
            if (video) {
                video.style.border = '';
                video.style.borderRadius = '';
            }
            
            const stopBtn = document.getElementById('stopAnswer');
            if (stopBtn) {
                stopBtn.disabled = true;
                stopBtn.style.backgroundColor = '';
                stopBtn.textContent = 'Stop Answering';
            }
            
            if (window.recordedChunks.length > 0) {
                // Create video blob
                const blob = new Blob(window.recordedChunks, { type: mimeType });
                console.log(`✅ Video blob created: ${Math.round(blob.size/1024)}KB`);
                
                updateRecordingStatus(`💾 Saving recording... (${Math.round(blob.size/1024)}KB)`);
                
                // Save the recording
                try {
                    await saveRecordingFixed(blob, duration);
                    updateRecordingStatus('✅ Recording saved successfully!');
                    
                    // Continue with next step
                    if (typeof nextStepCallback === 'function') {
                        setTimeout(() => nextStepCallback(), 1000);
                    }
                } catch (error) {
                    console.error('❌ Save failed:', error);
                    updateRecordingStatus('❌ Failed to save recording: ' + error.message);
                }
            } else {
                console.error('❌ No recording data captured');
                updateRecordingStatus('❌ No recording data captured');
            }
        };
        
        // Recording error
        window.mediaRecorder.onerror = (event) => {
            console.error('❌ Recording error:', event.error);
            updateRecordingStatus('❌ Recording error: ' + event.error);
        };
        
        // Start recording with 1-second intervals
        window.mediaRecorder.start(1000);
        console.log('🎬 MediaRecorder.start() called');
        
    } catch (error) {
        console.error('❌ Failed to create MediaRecorder:', error);
        updateRecordingStatus('❌ Recording failed: ' + error.message);
        alert('Recording failed: ' + error.message);
    }
};

// Simplified save function
window.saveRecordingFixed = async function(blob, duration) {
    console.log('💾 Saving recording blob...', { size: blob.size, duration: duration });
    
    const formData = new FormData();
    
    // Create filename with timestamp
    const timestamp = Date.now();
    const filename = `interview_${window.registrationId || 'test'}_${timestamp}.webm`;
    
    formData.append('video', blob, filename);
    formData.append('duration', duration || 0);
    formData.append('test', 'true'); // For testing
    formData.append('timestamp', timestamp);
    
    // Add token if available
    if (window.interviewToken) {
        formData.append('token', window.interviewToken);
        formData.append('registrationId', window.registrationId || '');
        formData.append('currentEssentialQuestion', window.currentEssentialQuestion || 0);
        formData.append('conversationTurn', (window.aiResponses ? window.aiResponses.length : 0) + 1);
        formData.append('questionType', 'essential_answer');
        formData.append('transcript', window.currentTranscript || '');
    }
    
    try {
        console.log('📤 Uploading to server...');
        
        const response = await fetch('/eris/simple-upload-working.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('📥 Server response:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const responseText = await response.text();
        console.log('📄 Response text:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('❌ Invalid JSON response:', responseText);
            throw new Error('Server returned invalid response');
        }
        
        if (result.success) {
            console.log('✅ Upload successful:', result);
            return result;
        } else {
            console.error('❌ Upload failed:', result.error);
            throw new Error(result.error || 'Upload failed');
        }
        
    } catch (error) {
        console.error('❌ Upload error:', error);
        throw error;
    }
};

// Helper function to update recording status
function updateRecordingStatus(message) {
    console.log('📱 Status:', message);
    
    // Try to find status elements and update them
    const statusElements = [
        document.getElementById('status'),
        document.querySelector('.status'),
        document.querySelector('.alert'),
        document.querySelector('[class*="status"]')
    ].filter(el => el);
    
    statusElements.forEach(el => {
        el.textContent = message;
        el.className = message.includes('❌') ? 'alert alert-danger' : 
                      message.includes('✅') ? 'alert alert-success' : 
                      message.includes('🔴') ? 'alert alert-warning' : 'alert alert-info';
    });
    
    // Also update any existing updateStatus function
    if (typeof window.updateStatus === 'function') {
        const type = message.includes('❌') ? 'danger' : 
                    message.includes('✅') ? 'success' : 
                    message.includes('🔴') ? 'warning' : 'info';
        window.updateStatus(message, type);
    }
}

// Override the stopAnswer function to use the fixed recording
window.stopAnswerFixed = function() {
    console.log('⏹️ Stop answer called');
    
    if (window.mediaRecorder && window.isRecording) {
        console.log('🛑 Stopping media recorder...');
        window.mediaRecorder.stop();
        window.isRecording = false;
    } else {
        console.log('❌ No active recording to stop');
        updateRecordingStatus('No active recording to stop');
    }
};

// Test function to verify everything works
window.testRecordingSystem = function() {
    console.log('🧪 Testing recording system...');
    
    // Check camera access
    const video = document.getElementById('video') || document.querySelector('video');
    if (!video || !video.srcObject) {
        console.error('❌ No camera access');
        return false;
    }
    
    // Check MediaRecorder support
    if (!window.MediaRecorder) {
        console.error('❌ MediaRecorder not supported');
        return false;
    }
    
    console.log('✅ Recording system ready');
    return true;
};

console.log('✅ Simplified recording system loaded');
console.log('📋 Available functions:');
console.log('  - window.startRecordingForCandidateFixed()');
console.log('  - window.stopAnswerFixed()');
console.log('  - window.testRecordingSystem()');
console.log('');
console.log('🎯 To use: Replace startRecordingForCandidate() calls with startRecordingForCandidateFixed()');