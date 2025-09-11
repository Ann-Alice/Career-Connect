<?php
require_once('include/initialize.php');

// Set up session to simulate admin login
$_SESSION['ADMIN_USERID'] = 1;

// Get the registration ID
$registration_id = 1;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Interview Recording</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Test Interview Recording</h1>
        
        <div class="row">
            <div class="col-md-12">
                <h3>Interview Recording for Registration ID: <?php echo $registration_id; ?></h3>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <video width="100%" height="400" controls style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: #000;">
                        <source src="admin/stream_recording.php?id=<?php echo $registration_id; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                
                <div class="text-center">
                    <a href="admin/download-recording.php?id=<?php echo $registration_id; ?>" 
                       class="btn btn-info" 
                       style="margin-right: 10px; border-radius: 8px; padding: 10px 20px;">
                        <i class="fa fa-download"></i> Download Recording
                    </a>
                    <button type="button" 
                            class="btn btn-primary" 
                            onclick="refreshVideoPlayer(<?php echo $registration_id; ?>)"
                            style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fa fa-refresh"></i> Refresh Player
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function refreshVideoPlayer(registrationId) {
        // Refresh the video player by reloading the source
        const videoElement = document.querySelector(`[src^="admin/stream_recording.php?id=${registrationId}"]`);
        if (videoElement) {
            const currentTime = videoElement.currentTime;
            const parent = videoElement.parentElement;
            const newSource = videoElement.src.split('&t=')[0] + '&t=' + new Date().getTime();
            videoElement.src = newSource;
            videoElement.load();
            videoElement.currentTime = currentTime;
            alert('Video player refreshed');
        }
    }
    </script>
</body>
</html>