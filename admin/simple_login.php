<?php
require_once("../include/initialize.php");

// Check if already logged in
if(isset($_SESSION['ADMIN_USERID'])){
    redirect(web_root."admin/");
}

$error_message = '';
$success_message = '';

// Handle login form submission
if(isset($_POST['btnlogin'])){
    $username = trim($_POST['USERNAME']);
    $password = trim($_POST['PASS']);
    
    if(empty($username) || empty($password)){
        $error_message = "Please enter both username and password.";
    } else {
        // Try to login
        try {
            $hashed_password = sha1($password);
            
            $sql = "SELECT * FROM tblusers WHERE USERNAME = '{$mydb->escape_string($username)}' AND PASS = '{$mydb->escape_string($hashed_password)}'";
            $mydb->setQuery($sql);
            $result = $mydb->loadSingleResult();
            
            if ($result) {
                // Login successful
                $_SESSION['ADMIN_USERID'] = $result->USERID;
                $_SESSION['ADMIN_FULLNAME'] = $result->FULLNAME;
                $_SESSION['ADMIN_USERNAME'] = $result->USERNAME;
                $_SESSION['ADMIN_ROLE'] = $result->ROLE;
                $_SESSION['ADMIN_PICLOCATION'] = $result->PICLOCATION ?? 'avatar.jpg';
                
                $success_message = "Login successful! Welcome " . $result->FULLNAME;
                
                // Redirect to admin dashboard
                redirect(web_root . "admin/");
            } else {
                $error_message = "Invalid username or password.";
            }
        } catch (Exception $e) {
            $error_message = "Login error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ERIS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 500px;
        }
        
        .login-left {
            background: linear-gradient(135deg, rgba(4, 91, 150, 0.9), rgba(4, 91, 150, 0.9));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex: 1;
        }
        
        .login-right {
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .brand-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .features-list {
            list-style: none;
            margin-bottom: 30px;
        }
        
        .features-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .features-list li:before {
            content: "✅";
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .login-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .message.success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
        
        .home-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .home-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .home-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .login-left {
                padding: 30px 20px;
            }
            
            .login-right {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div>
                <h1 class="brand-title">🚀 ERIS Admin</h1>
                <p class="brand-subtitle">AI Interview Management System</p>
                
                <ul class="features-list">
                    <li>Manage job applications</li>
                    <li>AI interview monitoring</li>
                    <li>Company management</li>
                    <li>User administration</li>
                    <li>System analytics</li>
                </ul>
            </div>
            
            <div>
                <p style="opacity: 0.8; margin-bottom: 15px;">Need help?</p>
                <a href="../index.php" style="color: white; text-decoration: none; border: 1px solid white; padding: 8px 20px; border-radius: 20px; display: inline-block;">🏠 Return to Home</a>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-right">
            <h2 class="login-title">🔐 Admin Login</h2>
            
            <?php if($error_message): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success_message): ?>
                <div class="message success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="USERNAME" class="form-label">Username</label>
                    <input type="text" id="USERNAME" name="USERNAME" class="form-input" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="PASS" class="form-label">Password</label>
                    <input type="password" id="PASS" name="PASS" class="form-input" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" name="btnlogin" class="login-btn">
                    🚀 Login to Admin Panel
                </button>
            </form>
            
            <div class="home-link">
                <a href="../index.php">← Back to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html> 