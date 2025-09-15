<?php
/**
 * Enhanced Error Handling System for pharmAid
 */

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = "Error [$errno]: $errstr in $errfile on line $errline";
    
    // Log error to file
    error_log($error_message);
    
    // Log to security log if it's a security-related error
    if (in_array($errno, [E_WARNING, E_ERROR, E_PARSE, E_CORE_ERROR])) {
        logSecurityEvent("PHP Error", "$errstr in $errfile:$errline");
    }
    
    // Never display errors in production - only log them
    if (ini_get('display_errors')) {
        echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
    }
    
    return true;
}

// Exception handler
function customExceptionHandler($exception) {
    $error_message = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    
    // Log exception
    error_log($error_message);
    logSecurityEvent("Uncaught Exception", $exception->getMessage());
    
    // Never display errors in production - only log them
    if (ini_get('display_errors')) {
        echo "<div class='alert alert-danger'>An unexpected error occurred. Please try again later.</div>";
    }
}

// Fatal error handler
function fatalErrorHandler() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $error_message = "Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}";
        error_log($error_message);
        logSecurityEvent("Fatal Error", $error['message']);
        
        if (ini_get('display_errors')) {
            echo "<div class='alert alert-danger'>A critical error occurred. Please try again later.</div>";
        }
    }
}

// Security event logging
function logSecurityEvent($event, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " - $event" . ($details ? " - $details" : '') . "\n";
    $log_file = dirname(__DIR__) . '/logs/security.log';
    
    // Create logs directory if it doesn't exist
    $log_dir = dirname($log_file);
    if (!file_exists($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    
    if (is_writable($log_dir)) {
        @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Database error handler
function handleDatabaseError($connection, $query = '') {
    $error = mysqli_error($connection);
    $errno = mysqli_errno($connection);
    
    if ($error) {
        $error_message = "Database Error [$errno]: $error";
        if ($query) {
            $error_message .= " Query: " . substr($query, 0, 100) . "...";
        }
        
        error_log($error_message);
        logSecurityEvent("Database Error", $error);
        
        return "Database operation failed. Please try again later.";
    }
    
    return false;
}

// Input validation error handler
function handleValidationError($field_name, $error_type, $custom_message = '') {
    $error_messages = [
        'required' => "$field_name is required.",
        'invalid_format' => "$field_name format is invalid.",
        'too_short' => "$field_name is too short.",
        'too_long' => "$field_name is too long.",
        'invalid_type' => "$field_name type is invalid.",
        'already_exists' => "$field_name already exists.",
        'not_found' => "$field_name not found.",
        'custom' => $custom_message ?: "$field_name validation failed."
    ];
    
    $message = isset($error_messages[$error_type]) ? $error_messages[$error_type] : $error_messages['custom'];
    
    return $message;
}

// File upload error handler
function handleFileUploadError($file, $field_name = 'File') {
    $error_messages = [
        UPLOAD_ERR_INI_SIZE => "$field_name exceeds maximum allowed size.",
        UPLOAD_ERR_FORM_SIZE => "$field_name exceeds form maximum size.",
        UPLOAD_ERR_PARTIAL => "$field_name was only partially uploaded.",
        UPLOAD_ERR_NO_FILE => "No $field_name was uploaded.",
        UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder.",
        UPLOAD_ERR_CANT_WRITE => "Failed to write $field_name to disk.",
        UPLOAD_ERR_EXTENSION => "A PHP extension stopped the $field_name upload."
    ];
    
    $error_code = $file['error'];
    $message = isset($error_messages[$error_code]) ? $error_messages[$error_code] : "Unknown upload error for $field_name.";
    
    logSecurityEvent("File Upload Error", $message);
    return $message;
}

// Session security check
function checkSessionSecurity() {
    // Regenerate session ID periodically to prevent session fixation
    if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    // Check for session hijacking
    if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        logSecurityEvent("Session Hijacking Attempt", "IP mismatch detected");
        return false;
    }
    
    // Set user IP if not set
    if (!isset($_SESSION['user_ip'])) {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    }
    
    return true;
}

// CSRF protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    // Regenerate token every hour
    if (time() - $_SESSION['csrf_token_time'] > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Check if token is expired (24 hours)
    if (time() - $_SESSION['csrf_token_time'] > 86400) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Rate limiting
function checkRateLimit($identifier, $max_attempts = 5, $time_window = 900) {
    $key = "rate_limit_$identifier";
    $current_time = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 0, 'first_attempt' => $current_time];
    }
    
    $rate_data = $_SESSION[$key];
    
    // Reset if time window has passed
    if ($current_time - $rate_data['first_attempt'] > $time_window) {
        $_SESSION[$key] = ['attempts' => 1, 'first_attempt' => $current_time];
        return true;
    }
    
    // Check if max attempts exceeded
    if ($rate_data['attempts'] >= $max_attempts) {
        logSecurityEvent("Rate Limit Exceeded", "Identifier: $identifier");
        return false;
    }
    
    // Increment attempts
    $_SESSION[$key]['attempts']++;
    return true;
}

// SQL injection prevention
function sanitizeSQLInput($input, $connection) {
    if (is_array($input)) {
        return array_map(function($item) use ($connection) {
            return sanitizeSQLInput($item, $connection);
        }, $input);
    }
    
    if (is_string($input)) {
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Escape SQL special characters
        $input = mysqli_real_escape_string($connection, $input);
        
        // Additional security checks
        if (preg_match('/(union|select|insert|update|delete|drop|create|alter|exec|execute|script|javascript|vbscript|onload|onerror)/i', $input)) {
            logSecurityEvent("SQL Injection Attempt", "Suspicious input detected: " . substr($input, 0, 100));
        }
        
        return $input;
    }
    
    return $input;
}

// XSS prevention
function sanitizeOutput($output) {
    if (is_array($output)) {
        return array_map('sanitizeOutput', $output);
    }
    
    if (is_string($output)) {
        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    return $output;
}

// Set error handlers
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");
register_shutdown_function("fatalErrorHandler");

// Initialize session security
if (session_status() === PHP_SESSION_ACTIVE) {
    checkSessionSecurity();
}
?>
