<?php
// Redirect to the main admin interview management page
$admin_url = '../admin/interview-invitation.php';

// Check if request came from admin area
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/') !== false) {
    header("Location: $admin_url");
    exit;
}

// Otherwise, show a simple access page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Interview System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            padding: 50px 0;
        }
        .container {
            max-width: 600px;
        }
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .info-icon {
            font-size: 48px;
            color: #3498db;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="info-card">
            <div class="info-icon">
                <i class="fa fa-video-camera"></i>
            </div>
            <h2>AI Interview System</h2>
            <p class="text-muted">This is the AI Interview System module for Eris Job Management.</p>
            
            <hr>
            
            <h4>Access Points:</h4>
            <div style="margin: 20px 0;">
                <a href="../admin/interview-invitation.php" class="btn btn-primary btn-lg">
                    <i class="fa fa-cog"></i> Admin Management
                </a>
            </div>
            <div style="margin: 20px 0;">
                <a href="interview.php" class="btn btn-success btn-lg">
                    <i class="fa fa-play"></i> Start Interview
                </a>
            </div>
            
            <hr>
            
            <p><small class="text-muted">
                For administrative access, please use the Admin Management link above.<br>
                For taking interviews, use the Start Interview button.
            </small></p>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>