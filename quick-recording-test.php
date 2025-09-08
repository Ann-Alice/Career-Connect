<!DOCTYPE html>
<html>
<head>
    <title>🎥 Quick Recording Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .btn:disabled { background: #6c757d; cursor: not-allowed; }
        video { width: 100%; max-width: 400px; background: #000; border-radius: 10px; margin: 20px 0; }
        .recording { border: 3px solid #dc3545 !important; animation: recording-pulse 1s infinite; }
        @keyframes recording-pulse { 0%, 100% { border-color: #dc3545; } 50% { border-color: #ff6b7a; } }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; text-align: center; }
        .log { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🎥 Quick Recording Test</h1>
    
    <div class="info">
        <h3>📋 This will test if video recording is working</h3>
        <p>This simple test will help us determine if the recording functionality is working properly.</p>
    </div>
    
    <div style="text-align: center;">
        <video id="video" autoplay muted playsinline></video>
        
        <div>
            <button id="startCamera" class="btn btn-success">📹 Start Camera</button>
            <button id="startRecording" class="btn btn-success" disabled>🔴 Start Recording</button>
            <button id="stopRecording" class="btn btn-danger" disabled>⏹️ Stop Recording</button>
            <button id="downloadRecording" class="btn" disabled>💾 Download</button>
        </div>
        
        <div id="status" class="status"></div>
    </div>
    
    <div class="log" id="log"></div>
    
    <script>
        let stream = null;
        let mediaRecorder = null;
        let recordedChunks = [];
        let recordedBlob = null;
        let isRecording = false;
        
        function log(message) {
            const timestamp = new Date().toLocaleTimeString();
            const logDiv = document.getElementById('log');
            logDiv.innerHTML += `[${timestamp}] ${message}\\n`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(message);
        }
        
        function updateStatus(message, type = 'info') {
            const statusDiv = document.getElementById('status');
            statusDiv.textContent = message;
            statusDiv.className = `status ${type}`;
        }
        
        document.getElementById('startCamera').addEventListener('click', async () => {
            try {
                log('🎥 Requesting camera access...');
                updateStatus('Requesting camera access...', 'info');
                
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 640, height: 480 }, 
                    audio: true 
                });
                
                const video = document.getElementById('video');
                video.srcObject = stream;
                
                log('✅ Camera access granted');
                updateStatus('✅ Camera ready! You can now start recording.', 'success');
                
                document.getElementById('startCamera').disabled = true;
                document.getElementById('startRecording').disabled = false;
                
            } catch (error) {
                log('❌ Camera access failed: ' + error.message);
                updateStatus('❌ Camera access failed: ' + error.message, 'error');
            }
        });
        
        document.getElementById('startRecording').addEventListener('click', () => {
            if (!stream) {
                log('❌ No camera stream available');
                updateStatus('❌ No camera stream available', 'error');
                return;
            }
            
            try {
                recordedChunks = [];
                
                // Test different MIME types
                let mimeType = 'video/webm;codecs=vp8,opus';
                if (!MediaRecorder.isTypeSupported(mimeType)) {
                    mimeType = 'video/webm';
                    if (!MediaRecorder.isTypeSupported(mimeType)) {
                        mimeType = 'video/mp4';
                    }
                }
                
                log('📹 Using MIME type: ' + mimeType);
                
                mediaRecorder = new MediaRecorder(stream, {
                    mimeType: mimeType,
                    videoBitsPerSecond: 500000,
                    audioBitsPerSecond: 64000
                });
                
                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        recordedChunks.push(event.data);
                        log(`📊 Chunk received: ${event.data.size} bytes (Total: ${recordedChunks.length} chunks)`);
                        updateStatus(`Recording... ${recordedChunks.length} chunks captured`, 'info');
                    }
                };
                
                mediaRecorder.onstart = () => {
                    log('✅ Recording STARTED successfully');
                    updateStatus('🔴 Recording in progress...', 'info');
                    isRecording = true;
                    
                    const video = document.getElementById('video');
                    video.classList.add('recording');
                    
                    document.getElementById('startRecording').disabled = true;
                    document.getElementById('stopRecording').disabled = false;
                };
                
                mediaRecorder.onstop = () => {
                    log('⏹️ Recording STOPPED');
                    isRecording = false;
                    
                    const video = document.getElementById('video');
                    video.classList.remove('recording');
                    
                    document.getElementById('startRecording').disabled = false;
                    document.getElementById('stopRecording').disabled = true;
                    
                    if (recordedChunks.length > 0) {
                        recordedBlob = new Blob(recordedChunks, { type: mimeType });
                        log(`✅ Recording blob created: ${Math.round(recordedBlob.size/1024)} KB`);
                        updateStatus(`✅ Recording complete: ${Math.round(recordedBlob.size/1024)} KB`, 'success');
                        
                        document.getElementById('downloadRecording').disabled = false;
                    } else {
                        log('❌ No recording data captured');
                        updateStatus('❌ No recording data captured', 'error');
                    }
                };
                
                mediaRecorder.onerror = (event) => {
                    log('❌ Recording error: ' + event.error);
                    updateStatus('❌ Recording error: ' + event.error, 'error');
                };
                
                mediaRecorder.start(1000);
                log('🎬 MediaRecorder.start() called');
                
            } catch (error) {
                log('❌ Failed to start recording: ' + error.message);
                updateStatus('❌ Failed to start recording: ' + error.message, 'error');
            }
        });
        
        document.getElementById('stopRecording').addEventListener('click', () => {
            if (mediaRecorder && isRecording) {
                log('🛑 Stopping recording...');
                mediaRecorder.stop();
            }
        });
        
        document.getElementById('downloadRecording').addEventListener('click', () => {
            if (recordedBlob) {
                const url = URL.createObjectURL(recordedBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'test-recording-' + Date.now() + '.webm';
                a.click();
                log('💾 Recording downloaded');
                
                // Test upload to server
                testUpload();
            }
        });
        
        async function testUpload() {
            if (!recordedBlob) return;
            
            try {
                log('📤 Testing upload to server...');
                updateStatus('📤 Testing upload...', 'info');
                
                const formData = new FormData();
                formData.append('video', recordedBlob, 'test-recording.webm');
                formData.append('test', 'true');
                formData.append('timestamp', Date.now());
                
                const response = await fetch('simple-upload-working.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.text();
                    log('✅ Upload successful: ' + result);
                    updateStatus('✅ Upload successful!', 'success');
                } else {
                    log('❌ Upload failed: ' + response.status + ' ' + response.statusText);
                    updateStatus('❌ Upload failed: ' + response.status, 'error');
                }
                
            } catch (error) {
                log('❌ Upload error: ' + error.message);
                updateStatus('❌ Upload error: ' + error.message, 'error');
            }
        }
        
        // Auto-initialize
        log('🎥 Quick Recording Test loaded');
        updateStatus('Click "Start Camera" to begin testing', 'info');
    </script>
</body>
</html>