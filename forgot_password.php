<?php
session_start();
include "includes/functions.php";
require_once "includes/email_helper.php";

// Cleanup expired reset tokens
cleanup_expired_resets();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Process forgot password request
        if (isset($_POST['forgot_password'])) {
            $email = trim(strtolower($_POST['email']));
            
            if (empty($email)) {
                $_SESSION['message'] = "Please enter your email address.";
                $_SESSION['message_type'] = "error";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = "Please enter a valid email address.";
                $_SESSION['message_type'] = "error";
            } else {
                // Check if email exists in user table
                $user_exists = check_email_user($email);
                
                if ($user_exists) {
                    // Generate password reset token
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Store reset token in database
                    $reset_success = store_password_reset($email, $token, $expires);
                    
                    if ($reset_success) {
                        // Send reset email
                        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                        
                        // Send email using PHPMailer
                        $email_sent = sendPasswordResetEmail($email, $reset_link);
                        
                        if ($email_sent) {
                            $success_message = "Password reset instructions have been sent to your email address.";
                            $message_type = "success";
                        } else {
                            $success_message = "Password reset link generated successfully. Check your email or contact support if you don't receive it within a few minutes.";
                            $message_type = "success";
                        }
                        
                        // Show success message and redirect after 2 seconds
                        $show_success = true;
                    } else {
                        $_SESSION['message'] = "An error occurred. Please try again.";
                        $_SESSION['message_type'] = "error";
                    }
                } else {
                    // Don't reveal if email exists or not for security
                    $success_message = "If an account with that email exists, password reset instructions have been sent.";
                    $message_type = "info";
                    // Show success message and redirect after 2 seconds
                    $show_success = true;
                }
            }
        }
?>

<head>
    <meta charset="UTF-8">
    <title>PharmAid - Forgot Password</title>
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
        
        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        /* Success message styling for snackbar effect */
        #success-message {
            animation: slideIn 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        #success-message::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #28a745;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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
                <div class="panel-title">Forgot Password</div>
            </div>
            
            <div class="panel-body">
                <div class="description">
                    Enter your email address and we'll send you instructions to reset your password.
                </div>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                        <?php echo htmlspecialchars($_SESSION['message']); ?>
                    </div>
                    <?php 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                <?php endif; ?>
                
                <?php if (isset($show_success) && $show_success): ?>
                    <div class="alert alert-<?php echo $message_type ?? 'success'; ?>" id="success-message">
                        <?php echo htmlspecialchars($success_message); ?>
                        <div style="margin-top: 15px;">
                            <a href="login.php" class="btn" style="display: inline-block; width: auto; padding: 8px 20px; margin-right: 10px;">Go to Login</a>
                            <a href="forgot_password.php" class="btn" style="display: inline-block; width: auto; padding: 8px 20px; background-color: #6c757d;">Send Another</a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="forgot_password.php">
                    <div class="form-group">
                        <label class="control-label">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
                    </div>
                    
                    <button type="submit" name="forgot_password" class="btn">
                        Send Reset Instructions
                    </button>
                </form>
                
                <div class="back-to-login">
                    <a href="login.php">← Back to Login</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($show_success) && $show_success): ?>
    <script>
        // Auto-redirect to login page after 10 seconds (give user time to read)
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 10000);
    </script>
    <?php endif; ?>
</body>
