<?php

require_once __DIR__ . '/../Models/patient.model.php';
require_once __DIR__ . '/../Models/Lab.model.php';
require_once __DIR__ . '/patient.service.php';

function registerPatient($conn, $data) {
    // Validate required fields
    $required = ['f_name', 'l_name', 'dob', 'gender', 'email', 'username', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Check if username already exists
    $existingPatient = findPatientByUsername($conn, $data['username']);
    if ($existingPatient) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email already exists
    $existingEmail = findPatientByEmail($conn, $data['email']);
    if ($existingEmail) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Insert patient
    $patient_id = insertPatient($conn, $data);
    
    if ($patient_id) {
        $patient = findPatientById($conn, $patient_id);
        return ['success' => true, 'patient' => $patient];
    }
    
    return ['success' => false, 'message' => 'Failed to register patient'];
}

function loginPatient($conn, $username, $password) {
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username and password are required'];
    }
    
    $patient = findPatientByUsername($conn, $username);
    
    if (!$patient) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Verify password (note: column name is Password_hash with lowercase 'h')
    if (!password_verify($password, $patient['Password_hash'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Remove password from response
    unset($patient['Password_hash']);
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['user_type'] = 'patient';
    $_SESSION['user_id'] = $patient['Patient_ID'];
    $_SESSION['username'] = $patient['Username'];
    
    return ['success' => true, 'patient' => $patient];
}

function registerLab($conn, $data) {
    // Validate required fields
    $required = ['lab_name', 'location', 'contact_num', 'email', 'username', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Check if username already exists
    $existingLab = findLabByUsername($conn, $data['username']);
    if ($existingLab) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email already exists
    $existingEmail = findLabByEmail($conn, $data['email']);
    if ($existingEmail) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Insert lab
    $lab_id = insertLab($conn, $data);
    
    if ($lab_id) {
        $lab = findLabById($conn, $lab_id);
        return ['success' => true, 'lab' => $lab];
    }
    
    return ['success' => false, 'message' => 'Failed to register lab'];
}

function loginLab($conn, $username, $password) {
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username and password are required'];
    }
    
    $lab = findLabByUsername($conn, $username);
    
    if (!$lab) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Verify password
    if (!password_verify($password, $lab['Password_Hash'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Remove password from response
    unset($lab['Password_Hash']);
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    $_SESSION['user_type'] = 'lab';
    $_SESSION['user_id'] = $lab['Lab_ID'];
    $_SESSION['username'] = $lab['Username'];
    
    // Debug logging
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
    
    // Debug: Log session data
    error_log("Session ID: " . session_id());
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("Session user_type: " . ($_SESSION['user_type'] ?? 'NOT SET'));
    error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
    
    if (!isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
        return ['authenticated' => false, 'message' => 'Not authenticated'];
    }
    
    if ($required_type && $_SESSION['user_type'] !== $required_type) {
        return ['authenticated' => false, 'message' => 'Unauthorized access'];
    }
    
    return [
        'authenticated' => true,
        'user_type' => $_SESSION['user_type'],
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null
    ];
}
