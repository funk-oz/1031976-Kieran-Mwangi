<?php
/**
 * Centralized Validation and Error Handling System
 * for pharmAid Website
 */

class Validation {
    private static $errors = [];
    
    public static function validateEmail($email, $field_name = 'Email') {
        $email = trim(strtolower($email));
        
        if (empty($email)) {
            self::$errors[] = "$field_name is required.";
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::$errors[] = "$field_name format is invalid.";
            return false;
        }
        
        return $email;
    }
    
    public static function validatePassword($password, $field_name = 'Password') {
        if (empty($password)) {
            self::$errors[] = "$field_name is required.";
            return false;
        }
        
        if (strlen($password) < 8) {
            self::$errors[] = "$field_name must be at least 8 characters long.";
            return false;
        }
        
        return $password;
    }
    
    public static function validateText($text, $field_name, $min_length = 1, $max_length = 255) {
        $text = trim($text);
        
        if (empty($text)) {
            self::$errors[] = "$field_name is required.";
            return false;
        }
        
        if (strlen($text) < $min_length) {
            self::$errors[] = "$field_name must be at least $min_length characters long.";
            return false;
        }
        
        if (strlen($text) > $max_length) {
            self::$errors[] = "$field_name is too long (maximum $max_length characters).";
            return false;
        }
        
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    public static function validatePhone($phone, $field_name = 'Phone') {
        $phone = trim($phone);
        
        if (empty($phone)) {
            self::$errors[] = "$field_name is required.";
            return false;
        }
        
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($clean_phone) < 10 || strlen($clean_phone) > 15) {
            self::$errors[] = "$field_name must be between 10 and 15 digits.";
            return false;
        }
        
        return $phone;
    }
    
    public static function validateNumber($number, $field_name, $min = null, $max = null) {
        if (!is_numeric($number)) {
            self::$errors[] = "$field_name must be a valid number.";
            return false;
        }
        
        $number = floatval($number);
        
        // Prevent negative values by default
        if ($number < 0) {
            self::$errors[] = "$field_name cannot be negative.";
            return false;
        }
        
        if ($min !== null && $number < $min) {
            self::$errors[] = "$field_name must be at least $min.";
            return false;
        }
        
        if ($max !== null && $number > $max) {
            self::$errors[] = "$field_name must be no more than $max.";
            return false;
        }
        
        return $number;
    }
    
    /**
     * Validate positive number (greater than 0)
     */
    public static function validatePositiveNumber($number, $field_name, $max = null) {
        if (!is_numeric($number)) {
            self::$errors[] = "$field_name must be a valid number.";
            return false;
        }
        
        $number = floatval($number);
        
        if ($number <= 0) {
            self::$errors[] = "$field_name must be greater than 0.";
            return false;
        }
        
        if ($max !== null && $number > $max) {
            self::$errors[] = "$field_name must be no more than $max.";
            return false;
        }
        
        return $number;
    }
    
    /**
     * Validate positive integer (whole number greater than 0)
     */
    public static function validatePositiveInteger($number, $field_name, $max = null) {
        if (!is_numeric($number)) {
            self::$errors[] = "$field_name must be a valid number.";
            return false;
        }
        
        $number = floatval($number);
        
        if ($number <= 0) {
            self::$errors[] = "$field_name must be greater than 0.";
            return false;
        }
        
        if (floor($number) != $number) {
            self::$errors[] = "$field_name must be a whole number.";
            return false;
        }
        
        if ($max !== null && $number > $max) {
            self::$errors[] = "$field_name must be no more than $max.";
            return false;
        }
        
        return intval($number);
    }
    
    public static function hasErrors() {
        return !empty(self::$errors);
    }
    
    public static function getErrors() {
        return self::$errors;
    }
    
    public static function getFirstError() {
        return !empty(self::$errors) ? self::$errors[0] : '';
    }
    
    public static function clearErrors() {
        self::$errors = [];
    }
    
    public static function addError($message) {
        self::$errors[] = $message;
    }
    
    /**
     * Validate gender selection
     */
    public static function validateGender($gender, $field_name = 'Gender') {
        $valid_genders = ['Male', 'Female', 'Other'];
        
        if (empty($gender)) {
            self::$errors[] = "$field_name is required.";
            return false;
        }
        
        if (!in_array($gender, $valid_genders)) {
            self::$errors[] = "Please select a valid $field_name.";
            return false;
        }
        
        return $gender;
    }
}

class Security {
    public static function sanitizeInput($input, $connection) {
        if (is_array($input)) {
            return array_map(function($item) use ($connection) {
                return self::sanitizeInput($item, $connection);
            }, $input);
        }
        
        if (is_string($input)) {
            return mysqli_real_escape_string($connection, trim($input));
        }
        
        return $input;
    }
    
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

function safeRedirect($url, $message = '') {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo "<script>window.location.href = '$url';</script>";
        exit();
    }
}
?>
