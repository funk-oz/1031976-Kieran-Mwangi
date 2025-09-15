<?php
/**
 * Input Sanitization and Validation System
 * for pharmAid Website
 */

class InputSanitizer {
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input, $max_length = 255) {
        if (!is_string($input)) {
            return '';
        }
        
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Limit length
        if (strlen($input) > $max_length) {
            $input = substr($input, 0, $max_length);
        }
        
        // Remove HTML tags
        $input = strip_tags($input);
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Sanitize email address
     */
    public static function sanitizeEmail($email) {
        if (!is_string($email)) {
            return '';
        }
        
        $email = trim(strtolower($email));
        
        // Remove null bytes and HTML tags
        $email = str_replace(chr(0), '', $email);
        $email = strip_tags($email);
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }
        
        // Limit length
        if (strlen($email) > 254) {
            return '';
        }
        
        return $email;
    }
    
    /**
     * Sanitize phone number
     */
    public static function sanitizePhone($phone) {
        if (!is_string($phone)) {
            return '';
        }
        
        $phone = trim($phone);
        
        // Remove null bytes and HTML tags
        $phone = str_replace(chr(0), '', $phone);
        $phone = strip_tags($phone);
        
        // Allow only digits, spaces, hyphens, parentheses, and plus sign
        $phone = preg_replace('/[^0-9\s\-\(\)\+]/', '', $phone);
        
        // Limit length
        if (strlen($phone) > 20) {
            $phone = substr($phone, 0, 20);
        }
        
        return $phone;
    }
    
    /**
     * Sanitize numeric input
     */
    public static function sanitizeNumber($input, $min = null, $max = null) {
        if (!is_numeric($input)) {
            return 0;
        }
        
        $number = floatval($input);
        
        // Prevent negative values by default
        if ($number < 0) {
            $number = 0;
        }
        
        if ($min !== null && $number < $min) {
            $number = $min;
        }
        
        if ($max !== null && $number > $max) {
            $number = $max;
        }
        
        return $number;
    }
    
    /**
     * Sanitize positive numeric input (greater than 0)
     */
    public static function sanitizePositiveNumber($input, $max = null) {
        if (!is_numeric($input)) {
            return 1;
        }
        
        $number = floatval($input);
        
        // Ensure positive value
        if ($number <= 0) {
            $number = 1;
        }
        
        if ($max !== null && $number > $max) {
            $number = $max;
        }
        
        return $number;
    }
    
    /**
     * Sanitize integer input
     */
    public static function sanitizeInteger($input, $min = null, $max = null) {
        if (!is_numeric($input)) {
            return 0;
        }
        
        $integer = intval($input);
        
        if ($min !== null && $integer < $min) {
            $integer = $min;
        }
        
        if ($max !== null && $integer > $max) {
            $integer = $max;
        }
        
        return $integer;
    }
    
    /**
     * Sanitize file upload data
     */
    public static function sanitizeFileUpload($file) {
        if (!is_array($file) || !isset($file['name'])) {
            return false;
        }
        
        // Sanitize filename
        $filename = self::sanitizeString($file['name'], 255);
        
        // Remove path traversal attempts
        $filename = basename($filename);
        
        // Remove null bytes
        $filename = str_replace(chr(0), '', $filename);
        
        // Validate file extension
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $allowed_extensions)) {
            return false;
        }
        
        return [
            'name' => $filename,
            'type' => $file['type'],
            'tmp_name' => $file['tmp_name'],
            'error' => $file['error'],
            'size' => $file['size']
        ];
    }
    
    /**
     * Sanitize SQL input for database queries
     */
    public static function sanitizeSQL($input, $connection) {
        if (is_array($input)) {
            return array_map(function($item) use ($connection) {
                return self::sanitizeSQL($item, $connection);
            }, $input);
        }
        
        if (is_string($input)) {
            // Remove null bytes
            $input = str_replace(chr(0), '', $input);
            
            // Remove HTML tags
            $input = strip_tags($input);
            
            // Escape SQL special characters
            $input = mysqli_real_escape_string($connection, $input);
            
            return $input;
        }
        
        return $input;
    }
    
    /**
     * Validate and sanitize form data
     */
    public static function sanitizeFormData($data, $rules = []) {
        $sanitized = [];
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                if (isset($rule['required']) && $rule['required']) {
                    $errors[] = "$field is required.";
                }
                continue;
            }
            
            $value = $data[$field];
            
            // Apply sanitization based on type
            switch ($rule['type']) {
                case 'string':
                    $sanitized[$field] = self::sanitizeString($value, $rule['max_length'] ?? 255);
                    break;
                    
                case 'email':
                    $sanitized[$field] = self::sanitizeEmail($value);
                    if (empty($sanitized[$field])) {
                        $errors[] = "$field is not a valid email address.";
                    }
                    break;
                    
                case 'phone':
                    $sanitized[$field] = self::sanitizePhone($value);
                    break;
                    
                case 'number':
                    $sanitized[$field] = self::sanitizeNumber($value, $rule['min'] ?? null, $rule['max'] ?? null);
                    break;
                    
                case 'integer':
                    $sanitized[$field] = self::sanitizeInteger($value, $rule['min'] ?? null, $rule['max'] ?? null);
                    break;
                    
                case 'select':
                    if (isset($rule['options']) && !in_array($value, $rule['options'])) {
                        $errors[] = "$field contains an invalid value.";
                        continue;
                    }
                    $sanitized[$field] = self::sanitizeString($value);
                    break;
                    
                default:
                    $sanitized[$field] = self::sanitizeString($value);
            }
            
            // Check minimum length
            if (isset($rule['min_length']) && strlen($sanitized[$field]) < $rule['min_length']) {
                $errors[] = "$field must be at least {$rule['min_length']} characters long.";
            }
            
            // Check maximum length
            if (isset($rule['max_length']) && strlen($sanitized[$field]) > $rule['max_length']) {
                $errors[] = "$field must be no more than {$rule['max_length']} characters long.";
            }
            
            // Check if empty after sanitization
            if (isset($rule['required']) && $rule['required'] && empty($sanitized[$field])) {
                $errors[] = "$field is required.";
            }
        }
        
        return [
            'data' => $sanitized,
            'errors' => $errors,
            'has_errors' => !empty($errors)
        ];
    }
    
    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        
        if (strlen($password) > 128) {
            $errors[] = "Password is too long (maximum 128 characters).";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number.";
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }
        
        // Check for common weak passwords
        $weak_passwords = [
            'password', '123456', 'qwerty', 'admin', 'letmein', 'welcome',
            'monkey', 'dragon', 'master', 'football', 'baseball', 'abc123'
        ];
        
        if (in_array(strtolower($password), $weak_passwords)) {
            $errors[] = "Password is too weak. Please choose a stronger password.";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Clean output to prevent XSS
     */
    public static function cleanOutput($output) {
        if (is_array($output)) {
            return array_map([self::class, 'cleanOutput'], $output);
        }
        
        if (is_string($output)) {
            // Convert special characters to HTML entities
            return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        return $output;
    }
}

// Helper function to get sanitized POST data
function getSanitizedPost($field, $default = '') {
    if (!isset($_POST[$field])) {
        return $default;
    }
    
    return InputSanitizer::sanitizeString($_POST[$field]);
}

// Helper function to get sanitized GET data
function getSanitizedGet($field, $default = '') {
    if (!isset($_GET[$field])) {
        return $default;
    }
    
    return InputSanitizer::sanitizeString($_GET[$field]);
}

// Helper function to get sanitized file upload
function getSanitizedFile($field) {
    if (!isset($_FILES[$field])) {
        return false;
    }
    
    return InputSanitizer::sanitizeFileUpload($_FILES[$field]);
}
?>
