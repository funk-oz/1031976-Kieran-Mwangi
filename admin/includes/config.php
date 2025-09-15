<?php
/**
 * Configuration file for pharmAid
 * Security and application settings
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'PharmEasy');

// Security configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes
define('MAX_SIGNUP_ATTEMPTS', 3);
define('SIGNUP_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_LIFETIME', 86400); // 24 hours
define('SESSION_REGENERATION_TIME', 1800); // 30 minutes

// File upload configuration
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_DIR', dirname(__DIR__) . '/images/');

// Error reporting configuration
define('DISPLAY_ERRORS', false); // Production setting - never show errors to users
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', dirname(__DIR__) . '/logs/error.log');
define('SECURITY_LOG_FILE', dirname(__DIR__) . '/logs/security.log');

// Application configuration
define('SITE_NAME', 'PharmAid');
define('SITE_URL', 'http://localhost/pharmAid');
define('ADMIN_EMAIL', 'admin@pharmaid.com');

// Super admin configuration (allow-list of emails)
// Only super admins can add new admins
if (!defined('SUPER_ADMIN_EMAILS')) {
    define('SUPER_ADMIN_EMAILS', json_encode([
        'admin@gmail.com', // seeded admin from SQL dump
        ADMIN_EMAIL        // configurable primary admin email
    ]));
}

// Set error reporting based on configuration
if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Set error logging
if (LOG_ERRORS) {
    ini_set('log_errors', 1);
    ini_set('error_log', ERROR_LOG_FILE);
}

// Security headers
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;");
}

// Initialize security headers
setSecurityHeaders();

// Session security configuration
function configureSession() {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_lifetime', SESSION_TIMEOUT);
    ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
    
    // Set session name
    session_name('PHARMAID_SESSION');
}

// Initialize session configuration
configureSession();
?>
