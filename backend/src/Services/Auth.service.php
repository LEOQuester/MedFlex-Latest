<?php

require_once __DIR__ . '/../Models/patient.model.php';
require_once __DIR__ . '/../Models/Lab.model.php';
require_once __DIR__ . '/patient.service.php';
require_once __DIR__ . '/../../config/validators.php';

function registerPatient($conn, $data) {
    $data = sanitizeData($data);
    
    $required = ['f_name', 'l_name', 'dob', 'gender', 'email', 'username', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    $nameValidation = validateName($data['f_name'], 'First name');
    if (!$nameValidation['valid']) {
        return ['success' => false, 'message' => $nameValidation['message']];
    }
    
    $nameValidation = validateName($data['l_name'], 'Last name');
    if (!$nameValidation['valid']) {
        return ['success' => false, 'message' => $nameValidation['message']];
    }
    
    $dobValidation = validateDOB($data['dob']);
    if (!$dobValidation['valid']) {
        return ['success' => false, 'message' => $dobValidation['message']];
    }
    
    $genderValidation = validateGender($data['gender']);
    if (!$genderValidation['valid']) {
        return ['success' => false, 'message' => $genderValidation['message']];
    }
    
    if (!empty($data['address'])) {
        $addressValidation = validateAddress($data['address']);
        if (!$addressValidation['valid']) {
            return ['success' => false, 'message' => $addressValidation['message']];
        }
    }
    
    $emailValidation = validateEmail($data['email']);
    if (!$emailValidation['valid']) {
        return ['success' => false, 'message' => $emailValidation['message']];
    }
    
    $usernameValidation = validateUsername($data['username']);
    if (!$usernameValidation['valid']) {
        return ['success' => false, 'message' => $usernameValidation['message']];
    }
    
    $passwordValidation = validatePassword($data['password']);
    if (!$passwordValidation['valid']) {
        return ['success' => false, 'message' => $passwordValidation['message']];
    }
    
    $existingPatient = findPatientByUsername($conn, $data['username']);
    if ($existingPatient) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    $existingEmail = findPatientByEmail($conn, $data['email']);
    if ($existingEmail) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    $patient_id = insertPatient($conn, $data);
    
    if ($patient_id) {
        $patient = findPatientById($conn, $patient_id);
        return ['success' => true, 'patient' => $patient];
    }
    
    return ['success' => false, 'message' => 'Failed to register patient'];
}

function loginPatient($conn, $username, $password) {
    $username = sanitizeInput($username);
    $password = sanitizeInput($password);
    
    if (empty($username)) {
        return ['success' => false, 'message' => 'Username is required'];
    }
    
    if (empty($password)) {
        return ['success' => false, 'message' => 'Password is required'];
    }
    
    // Find patient
    $patient = findPatientByUsername($conn, $username);
    
    if (!$patient) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    if (!password_verify($password, $patient['Password_hash'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    unset($patient['Password_hash']);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['user_type'] = 'patient';
    $_SESSION['user_id'] = $patient['Patient_ID'];
    $_SESSION['username'] = $patient['Username'];
    
    return ['success' => true, 'patient' => $patient];
}

function registerLab($conn, $data) {
    $data = sanitizeData($data);
    
    $required = ['lab_name', 'location', 'contact_num', 'email', 'username', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    $nameValidation = validateName($data['lab_name'], 'Lab name');
    if (!$nameValidation['valid']) {
        return ['success' => false, 'message' => $nameValidation['message']];
    }
    
    if (strlen($data['lab_name']) > 100) {
        return ['success' => false, 'message' => 'Lab name must not exceed 100 characters'];
    }
    
    $locationValidation = validateAddress($data['location'], 'Location');
    if (!$locationValidation['valid']) {
        return ['success' => false, 'message' => $locationValidation['message']];
    }
    
    if (strlen($data['location']) > 100) {
        return ['success' => false, 'message' => 'Location must not exceed 100 characters'];
    }
    
    // Validate contact number
    $phoneValidation = validatePhoneNumber($data['contact_num']);
    if (!$phoneValidation['valid']) {
        return ['success' => false, 'message' => $phoneValidation['message']];
    }
    
    $emailValidation = validateEmail($data['email']);
    if (!$emailValidation['valid']) {
        return ['success' => false, 'message' => $emailValidation['message']];
    }
    
    $usernameValidation = validateUsername($data['username']);
    if (!$usernameValidation['valid']) {
        return ['success' => false, 'message' => $usernameValidation['message']];
    }
    
    $passwordValidation = validatePassword($data['password']);
    if (!$passwordValidation['valid']) {
        return ['success' => false, 'message' => $passwordValidation['message']];
    }
    
    $existingLab = findLabByUsername($conn, $data['username']);
    if ($existingLab) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    $existingEmail = findLabByEmail($conn, $data['email']);
    if ($existingEmail) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    $existingContact = findLabByContactNum($conn, $data['contact_num']);
    if ($existingContact) {
        return ['success' => false, 'message' => 'Contact number already exists'];
    }
    
    $lab_id = insertLab($conn, $data);
    
    if ($lab_id) {
        $lab = findLabById($conn, $lab_id);
        return ['success' => true, 'lab' => $lab];
    }
    
    return ['success' => false, 'message' => 'Failed to register lab'];
}

function loginLab($conn, $username, $password) {
    $username = sanitizeInput($username);
    $password = sanitizeInput($password);
    
    if (empty($username)) {
        return ['success' => false, 'message' => 'Username is required'];
    }
    
    if (empty($password)) {
        return ['success' => false, 'message' => 'Password is required'];
    }
    
    $lab = findLabByUsername($conn, $username);
    
    if (!$lab) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    if (!password_verify($password, $lab['Password_Hash'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Remove sensitive data
    unset($lab['Password_Hash']);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    session_regenerate_id(true);
    
    $_SESSION['user_type'] = 'lab';
    $_SESSION['user_id'] = $lab['Lab_ID'];
    $_SESSION['username'] = $lab['Username'];
    
    error_log("Lab logged in - Session ID: " . session_id());
    error_log("Session data after login: " . print_r($_SESSION, true));
    
    return ['success' => true, 'lab' => $lab, 'session_id' => session_id()];
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

function checkAuth($required_type = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    error_log("=== checkAuth called ===");
    error_log("Session ID: " . session_id());
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("Required type: " . ($required_type ?? 'none'));
    error_log("Session user_type: " . ($_SESSION['user_type'] ?? 'NOT SET'));
    error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
    
    if (!isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
        error_log("Authentication FAILED: Session variables not set");
        return ['authenticated' => false, 'message' => 'Not authenticated'];
    }
    
    if ($required_type && $_SESSION['user_type'] !== $required_type) {
        error_log("Authentication FAILED: Wrong user type. Expected: $required_type, Got: " . $_SESSION['user_type']);
        return ['authenticated' => false, 'message' => 'Unauthorized access'];
    }
    
    error_log("Authentication SUCCESS");
    return [
        'authenticated' => true,
        'user_type' => $_SESSION['user_type'],
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null
    ];
}
