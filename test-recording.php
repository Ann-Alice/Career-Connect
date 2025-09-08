<!DOCTYPE html>
<html>
<head>
    <title>🎥 Video Recording Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .test-section { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .video-container { background: #000; border-radius: 10px; margin: 20px 0; overflow: hidden; }
        video { width: 100%; height: 300px; background: #000; }
        .recording { border: 3px solid #dc3545; animation: recording-pulse 1s infinite; }
        @keyframes recording-pulse { 0%, 100% { border-color: #dc3545; } 50% { border-color: #ff6b7a; } }
        .log { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px; }
        .controls { text-align: center; margin: 20px 0; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🎥 Video Recording Diagnostic Tool</h1>
    
    <div class="info">
        <h3>📋 This tool will help diagnose video recording issues</h3>
        <p>We'll test each step of the recording process to identify where it's failing:</p>
        <ol>
            <li><strong>Camera/Microphone Access:</strong> Test if browser can access media devices</li>
            <li><strong>MediaRecorder Support:</strong> Check if browser supports video recording</li>
            <li><strong>Recording Functionality:</strong> Test actual video recording</li>
            <li><strong>File Upload:</strong> Test saving recorded videos to server</li>
        </ol>
    </div>
    
    <div class="test-section">
        <h3>🎬 Step 1: Camera & Microphone Access Test</h3>
        <div class="video-container">
            <video id="testVideo" autoplay muted playsinline></video>
        </div>
        <div class="controls">
            <button class="btn btn-success" onclick="startCamera()">📹 Start Camera</button>
            <button class="btn" onclick="stopCamera()">⏹️ Stop Camera</button>
        </div>
        <div id="cameraStatus" class="status"></div>
    </div>
    
    <div class="test-section">
        <h3>🔧 Step 2: MediaRecorder Support Test</h3>
        <div id="supportStatus" class="status"></div>
        <button class="btn" onclick="checkSupport()">🔍 Check Browser Support</button>
    </div>
    
    <div class="test-section">
        <h3>📹 Step 3: Video Recording Test</h3>
        <div class="controls">
            <button class="btn btn-success" onclick="startRecording()">🔴 Start Recording</button>
            <button class="btn btn-danger" onclick="stopRecording()" disabled id="stopBtn">⏹️ Stop Recording</button>
            <button class="btn" onclick="playRecording()" disabled id="playBtn">▶️ Play Recording</button>
            <button class="btn" onclick="downloadRecording()" disabled id="downloadBtn">💾 Download Recording</button>
        </div>
        <div id="recordingStatus" class="status"></div>
        <div id="recordingInfo"></div>
    </div>
    
    <div class="test-section">
        <h3>📤 Step 4: Upload Test</h3>
        <div class="controls">
            <button class="btn" onclick="testUpload()" disabled id="uploadBtn">📤 Test Upload to Server</button>
        </div>
        <div id="uploadStatus" class="status"></div>
    </div>
    
    <div class="test-section">
        <h3>📋 Debug Log</h3>
        <div id="debugLog" class="log"></div>
        <button class="btn" onclick="clearLog()">🗑️ Clear Log</button>
    </div>
    
    <script>
        let stream = null;
        let mediaRecorder = null;
        let recordedChunks = [];
        let recordedBlob = null;
        
        function log(message) {
            const timestamp = new Date().toLocaleTimeString();
            const logDiv = document.getElementById('debugLog');
            logDiv.innerHTML += `[${timestamp}] ${message}\n`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(message);
        }
        
        function clearLog() {
            document.getElementById('debugLog').innerHTML = '';
        }
        
        function updateStatus(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.className = `status ${type}`;
        }
        
        async function startCamera() {
            try {
                log('Requesting camera and microphone access...');
                updateStatus('cameraStatus', 'Requesting access...', 'info');
                
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 30 }
                    }, 
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true
                    }
                });
                
                const video = document.getElementById('testVideo');
                video.srcObject = stream;
                
                log('✅ Camera and microphone access granted');
                updateStatus('cameraStatus', '✅ Camera and microphone working!', 'success');
                
                // Auto-check support after camera starts
                setTimeout(checkSupport, 1000);
                
            } catch (error) {
                log('❌ Camera access failed: ' + error.message);
                updateStatus('cameraStatus', '❌ Camera access failed: ' + error.message, 'error');
                
                if (error.name === 'NotAllowedError') {
                    updateStatus('cameraStatus', '❌ Permission denied. Please allow camera/microphone access and refresh the page.', 'error');
                } else if (error.name === 'NotFoundError') {
                    updateStatus('cameraStatus', '❌ No camera/microphone found. Please connect devices and refresh.', 'error');
                }
            }
        }
        
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
                document.getElementById('testVideo').srcObject = null;
                log('📹 Camera stopped');
                updateStatus('cameraStatus', '📹 Camera stopped', 'info');
            }
        }
        
        function checkSupport() {
            log('Checking MediaRecorder support...');
            
            if (!navigator.mediaDevices) {
                log('❌ navigator.mediaDevices not supported');
                updateStatus('supportStatus', '❌ Browser does not support media devices', 'error');
                return;
            }
            
            if (!window.MediaRecorder) {
                log('❌ MediaRecorder not supported');
                updateStatus('supportStatus', '❌ Browser does not support video recording', 'error');
                return;
            }
            
            // Test different MIME types
            const mimeTypes = [
                'video/webm;codecs=vp8,opus',
                'video/webm;codecs=vp9,opus',
                'video/webm',
                'video/mp4',
                'video/mp4;codecs=h264,aac'
            ];
            
            let supportedTypes = [];
            mimeTypes.forEach(type => {
                if (MediaRecorder.isTypeSupported(type)) {
                    supportedTypes.push(type);
                    log(`✅ Supported: ${type}`);
                } else {
                    log(`❌ Not supported: ${type}`);
                }
            });
            
            if (supportedTypes.length > 0) {
                updateStatus('supportStatus', `✅ MediaRecorder supported! Found ${supportedTypes.length} compatible formats`, 'success');
                log('✅ MediaRecorder is supported');
            } else {
                updateStatus('supportStatus', '❌ No supported video formats found', 'error');
                log('❌ No supported MIME types');
            }
        }
        
        async function startRecording() {
            if (!stream) {
                updateStatus('recordingStatus', '❌ Please start camera first', 'error');
                return;
            }
            
            try {
                recordedChunks = [];
                
                // Use the best supported MIME type
                let mimeType = 'video/webm;codecs=vp8,opus';
                if (!MediaRecorder.isTypeSupported(mimeType)) {
                    mimeType = 'video/webm';
                }
                
                mediaRecorder = new MediaRecorder(stream, {
                    mimeType: mimeType,
                    videoBitsPerSecond: 1000000,
                    audioBitsPerSecond: 128000
                });
                
                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        recordedChunks.push(event.data);
                        log(`📹 Recording chunk: ${event.data.size} bytes (Total chunks: ${recordedChunks.length})`);
                        
                        const totalSize = recordedChunks.reduce((sum, chunk) => sum + chunk.size, 0);
                        document.getElementById('recordingInfo').innerHTML = 
                            `<strong>Recording:</strong> ${recordedChunks.length} chunks, ${Math.round(totalSize/1024)} KB total`;
                    }
                };
                
                mediaRecorder.onstart = () => {
                    log('🔴 Recording started');
                    updateStatus('recordingStatus', '🔴 Recording in progress...', 'warning');
                    document.getElementById('testVideo').classList.add('recording');
                    document.getElementById('stopBtn').disabled = false;
                };
                
                mediaRecorder.onstop = () => {
                    log('⏹️ Recording stopped');
                    updateStatus('recordingStatus', '⏹️ Recording stopped', 'info');
                    document.getElementById('testVideo').classList.remove('recording');
                    document.getElementById('stopBtn').disabled = true;
                    
                    if (recordedChunks.length > 0) {
                        recordedBlob = new Blob(recordedChunks, { type: mimeType });
                        log(`✅ Recording blob created: ${recordedBlob.size} bytes`);
                        updateStatus('recordingStatus', `✅ Recording complete: ${Math.round(recordedBlob.size/1024)} KB`, 'success');
                        
                        document.getElementById('playBtn').disabled = false;
                        document.getElementById('downloadBtn').disabled = false;
                        document.getElementById('uploadBtn').disabled = false;
                    } else {
                        log('❌ No recording data captured');
                        updateStatus('recordingStatus', '❌ No recording data captured', 'error');
                    }
                };
                
                mediaRecorder.onerror = (event) => {
                    log('❌ Recording error: ' + event.error);
                    updateStatus('recordingStatus', '❌ Recording error: ' + event.error, 'error');
                };
                
                mediaRecorder.start(1000); // Collect data every second
                
            } catch (error) {
                log('❌ Failed to start recording: ' + error.message);
                updateStatus('recordingStatus', '❌ Failed to start recording: ' + error.message, 'error');
            }
        }
        
        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        }
        
        function playRecording() {
            if (recordedBlob) {
                const url = URL.createObjectURL(recordedBlob);
                const video = document.getElementById('testVideo');
                video.srcObject = null;
                video.src = url;
                video.controls = true;
                video.play();
                log('▶️ Playing recorded video');
            }
        }
        
        function downloadRecording() {
            if (recordedBlob) {
                const url = URL.createObjectURL(recordedBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'test-recording-' + Date.now() + '.webm';
                a.click();
                log('💾 Downloaded recording');
            }
        }
        
        async function testUpload() {
            if (!recordedBlob) {
                updateStatus('uploadStatus', '❌ No recording to upload', 'error');
                return;
            }
            
            try {
                log('📤 Testing upload to server...');
                updateStatus('uploadStatus', '📤 Uploading...', 'info');
                
                const formData = new FormData();
                const timestamp = Date.now();
                const filename = `test-recording-${timestamp}.webm`;
                
                formData.append('video', recordedBlob, filename);
                formData.append('test', 'true');
                formData.append('timestamp', timestamp);
                
                const response = await fetch('simple-upload-working.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.text();
                    log('✅ Upload successful: ' + result);
                    updateStatus('uploadStatus', '✅ Upload successful!', 'success');
                } else {
                    log('❌ Upload failed: ' + response.status + ' ' + response.statusText);
                    updateStatus('uploadStatus', '❌ Upload failed: ' + response.status, 'error');
                }
                
            } catch (error) {
                log('❌ Upload error: ' + error.message);
                updateStatus('uploadStatus', '❌ Upload error: ' + error.message, 'error');
            }
        }
        
        // Auto-start camera when page loads
        window.addEventListener('load', () => {
            log('🎥 Video Recording Diagnostic Tool loaded');
            log('Click "Start Camera" to begin testing...');
        });
    </script>
</body>
</html>