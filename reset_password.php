<?php
session_start();
include "includes/functions.php";

// Cleanup expired reset tokens
cleanup_expired_resets();

// Check if user is already logged in
if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$token = $_GET['token'] ?? '';
$error_message = '';
$success_message = '';

// Validate token
if (empty($token)) {
    $error_message = "Invalid reset link.";
} else {
    // Check if token exists and is valid
    $reset_data = get_password_reset($token);
    
    if (!$reset_data) {
        $error_message = "Invalid or expired reset link.";
    }
}

// Process password reset
if (isset($_POST['reset_password']) && empty($error_message)) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($new_password) || empty($confirm_password)) {
        $error_message = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        // Update password
        $update_success = update_user_password($reset_data['email'], $new_password);
        
        if ($update_success) {
            $success_message = "Password has been reset successfully! You can now login with your new password.";
        } else {
            $error_message = "Failed to reset password. Please try again.";
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <title>PharmAid - Reset Password</title>
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .panel {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 10px;
        }
        
        .panel-heading {
            background-color: #5bc0de;
            padding: 8px;
            border-bottom: 1px solid #ccc;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin: -15px -15px 15px -15px;
        }
        
        .panel-title {
            font-weight: bold;
            font-size: 18px;
            color: white;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 12px;
        }
        
        .control-label {
            font-weight: bold;
            margin-bottom: 3px;
            display: block;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            height: 35px;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        
        .btn {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 15px;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-to-login a {
            color: #5bc0de;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-to-login a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .password-requirements {
            text-align: left;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        
        .password-requirements ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .description {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">Reset Password</div>
            </div>
            
            <div class="panel-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                    <div class="back-to-login">
                        <a href="forgot_password.php">Request New Reset Link</a>
                    </div>
                <?php elseif (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                    <div class="back-to-login">
                        <a href="login.php">Go to Login</a>
                    </div>
                <?php else: ?>
                    <div class="description">
                        Enter your new password below.
                    </div>
                    
                    <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
                        <div class="form-group">
                            <label class="control-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" placeholder="Enter new password" required>
                            <div class="password-requirements">
                                <ul>
                                    <li>At least 8 characters long</li>
                                    <li>Use a combination of letters, numbers, and symbols</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                        
                        <button type="submit" name="reset_password" class="btn">
                            Reset Password
                        </button>
                    </form>
                    
                    <div class="back-to-login">
                        <a href="login.php">← Back to Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
