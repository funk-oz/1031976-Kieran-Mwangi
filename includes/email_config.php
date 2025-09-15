<?php
/**
 * Email Configuration for PharmAid
 * Configure your SMTP settings here
 */

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');  // Gmail SMTP server
define('SMTP_PORT', 587);               // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_USERNAME', 'your-gmail@gmail.com');  // Your Gmail address
define('SMTP_PASSWORD', 'your-16-digit-app-password');     // Your Gmail app password
define('SMTP_SECURE', 'tls');           // 'tls' or 'ssl'
define('SMTP_FROM_EMAIL', 'your-gmail@gmail.com');  // Use your Gmail as sender
define('SMTP_FROM_NAME', 'PharmAid Support');

// For Gmail users:
// 1. Enable 2-factor authentication
// 2. Generate an "App Password" (not your regular password)
// 3. Use that app password in SMTP_PASSWORD

// For other providers:
// - Outlook/Hotmail: smtp-mail.outlook.com, port 587
// - Yahoo: smtp.mail.yahoo.com, port 587
// - Custom SMTP: Use your provider's settings

// Development mode - set to true to show emails on page instead of sending
define('EMAIL_DEV_MODE', false);
?>
