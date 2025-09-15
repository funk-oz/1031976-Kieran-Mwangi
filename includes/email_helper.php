<?php
/**
 * Email Helper Functions using PHPMailer
 */

require_once __DIR__ . '/email_config.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using PHPMailer with SMTP
 */
function sendEmail($to, $subject, $message, $isHTML = false) {
    // Development mode - show email content on page
    if (EMAIL_DEV_MODE) {
        echo "<div style='border: 2px solid #007bff; padding: 15px; margin: 10px; background: #f8f9fa;'>";
        echo "<h3>📧 Email (Development Mode)</h3>";
        echo "<p><strong>To:</strong> $to</p>";
        echo "<p><strong>Subject:</strong> $subject</p>";
        echo "<p><strong>Message:</strong></p>";
        echo "<div style='border: 1px solid #ddd; padding: 10px; background: white;'>";
        echo $isHTML ? $message : nl2br(htmlspecialchars($message));
        echo "</div>";
        echo "</div>";
        return true;
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if (!$isHTML) {
            $mail->AltBody = strip_tags($message);
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $resetLink) {
    $subject = "Password Reset Request - PharmAid";
    
    $htmlMessage = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #059669; color: white; padding: 20px; text-align: center;'>
            <h1 style='margin: 0;'>PharmAid</h1>
        </div>
        
        <div style='padding: 30px; background: #f8f9fa;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            
            <p>Hello,</p>
            
            <p>You have requested to reset your password for your PharmAid account.</p>
            
            <p>To reset your password, please click the button below:</p>
            
            <div style='text-align: center; margin: 30px 0;'>
                <a href='$resetLink' 
                   style='background: #059669; color: white; padding: 15px 30px; 
                          text-decoration: none; border-radius: 5px; display: inline-block;'>
                    Reset Password
                </a>
            </div>
            
            <p style='font-size: 14px; color: #666;'>
                Or copy and paste this link into your browser:<br>
                <a href='$resetLink' style='color: #059669;'>$resetLink</a>
            </p>
            
            <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p style='margin: 0; color: #856404;'>
                    <strong>⚠️ Important:</strong> This link will expire in 1 hour for security reasons.
                </p>
            </div>
            
            <p>If you did not request this password reset, please ignore this email.</p>
            
            <p>Best regards,<br>
            <strong>PharmAid Team</strong></p>
        </div>
        
        <div style='background: #333; color: white; padding: 20px; text-align: center; font-size: 12px;'>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>";
    
    $textMessage = "
    Password Reset Request - PharmAid
    
    Hello,
    
    You have requested to reset your password for your PharmAid account.
    
    To reset your password, please visit the following link:
    $resetLink
    
    This link will expire in 1 hour.
    
    If you did not request this password reset, please ignore this email.
    
    Best regards,
    PharmAid Team";
    
    return sendEmail($email, $subject, $htmlMessage, true);
}
?>
