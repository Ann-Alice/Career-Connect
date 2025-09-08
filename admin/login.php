<!DOCTYPE html>
<html>
<head>
    <title>Login to Career Connect</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 2;
        }
        
        .login-title {
            text-align: center;
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .input-container {
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
        }
        
        .btn-signin {
            width: 100%;
            background: #3498db;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-signin:hover {
            background: #2980b9;
        }
        
        .copyright {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
        
        /* Responsive design */
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 25px;
                max-width: 100%;
            }
            
            .login-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-title">Login to Career Connect</div>
        
        <form action="process.php?action=login" method="post">
            <div class="form-group">
                <label for="USERNAME">Username</label>
                <div class="input-container">
                    <input type="text" class="form-control" name="USERNAME" id="USERNAME" required>
                    <div class="input-icon">👤</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="PASS">Password</label>
                <div class="input-container">
                    <input type="password" class="form-control" name="PASS" id="PASS" required>
                    <div class="input-icon">🔒</div>
                </div>
            </div>
            
            <button type="submit" class="btn-signin">Sign In</button>
        </form>
        
        <div class="copyright">© 2025 Career Connect. All rights reserved.</div>
    </div>
</body>
</html>