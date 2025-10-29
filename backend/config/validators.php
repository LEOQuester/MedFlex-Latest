<?php

function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'message' => 'Email is required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    
    if (strlen($email) > 100) {
        return ['valid' => false, 'message' => 'Email must not exceed 100 characters'];
    }
    
    return ['valid' => true];
}

function validatePhoneNumber($phone) {
    if (empty($phone)) {
        return ['valid' => false, 'message' => 'Phone number is required'];
    }
    
    $phone = preg_replace('/[\s\-]/', '', $phone);
    
    $patterns = [
        '/^0[0-9]{9}$/',
        '/^\+94[0-9]{9}$/',
        '/^94[0-9]{9}$/'
    ];
    
    $isValid = false;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $phone)) {
            $isValid = true;
            break;
        }
    }
    
    if (!$isValid) {
        return ['valid' => false, 'message' => 'Invalid phone number format. Use format: 0XXXXXXXXX'];
    }
    
    return ['valid' => true];
}

function validateUsername($username) {
    if (empty($username)) {
        return ['valid' => false, 'message' => 'Username is required'];
    }
    
    if (strlen($username) < 3) {
        return ['valid' => false, 'message' => 'Username must be at least 3 characters long'];
    }
    
    if (strlen($username) > 50) {
        return ['valid' => false, 'message' => 'Username must not exceed 50 characters'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        return ['valid' => false, 'message' => 'Username can only contain letters, numbers, underscores, and hyphens'];
    }
    
    return ['valid' => true];
}

function validatePassword($password) {
    if (empty($password)) {
        return ['valid' => false, 'message' => 'Password is required'];
    }
    
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'Password must be at least 6 characters long'];
    }
    
    if (strlen($password) > 255) {
        return ['valid' => false, 'message' => 'Password must not exceed 255 characters'];
    }
    
    return ['valid' => true];
}

function validateName($name, $fieldName = 'Name') {
    if (empty($name)) {
        return ['valid' => false, 'message' => "$fieldName is required"];
    }
    
    if (strlen($name) < 2) {
        return ['valid' => false, 'message' => "$fieldName must be at least 2 characters long"];
    }
    
    if (strlen($name) > 50) {
        return ['valid' => false, 'message' => "$fieldName must not exceed 50 characters"];
    }
    
    if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
        return ['valid' => false, 'message' => "$fieldName can only contain letters, spaces, apostrophes, and hyphens"];
    }
    
    return ['valid' => true];
}

function validateDOB($dob) {
    if (empty($dob)) {
        return ['valid' => false, 'message' => 'Date of birth is required'];
    }
    
    $date = strtotime($dob);
    if (!$date) {
        return ['valid' => false, 'message' => 'Invalid date format'];
    }
    
    if ($date > time()) {
        return ['valid' => false, 'message' => 'Date of birth cannot be in the future'];
    }
    
    $age = (time() - $date) / (365 * 24 * 60 * 60);
    if ($age > 150) {
        return ['valid' => false, 'message' => 'Invalid date of birth'];
    }
    
    return ['valid' => true];
}

function validateGender($gender) {
    if (empty($gender)) {
        return ['valid' => false, 'message' => 'Gender is required'];
    }
    
    $validGenders = ['Male', 'Female', 'Other'];
    if (!in_array($gender, $validGenders)) {
        return ['valid' => false, 'message' => 'Gender must be Male, Female, or Other'];
    }
    
    return ['valid' => true];
}

function validateAddress($address, $fieldName = 'Address') {
    if (empty($address)) {
        return ['valid' => false, 'message' => "$fieldName is required"];
    }
    
    if (strlen($address) < 5) {
        return ['valid' => false, 'message' => "$fieldName must be at least 5 characters long"];
    }
    
    if (strlen($address) > 255) {
        return ['valid' => false, 'message' => "$fieldName must not exceed 255 characters"];
    }
    
    return ['valid' => true];
}

function sanitizeInput($input) {
    if (is_string($input)) {
        return trim($input);
    }
    return $input;
}

function sanitizeData($data) {
    if (!is_array($data)) {
        return $data;
    }
    
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $sanitized[$key] = sanitizeInput($value);
        } else {
            $sanitized[$key] = $value;
        }
    }
    
    return $sanitized;
}
