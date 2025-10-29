<?php

require_once __DIR__ . '/../Models/patient.model.php';
require_once __DIR__ . '/../../config/validators.php';

function validatePatientData($data, $isUpdate = false) {
    $errors = [];
    
    $data = sanitizeData($data);
    
    $required_fields = ['f_name', 'l_name', 'dob', 'gender', 'address', 'email', 'username'];
    
    if (!$isUpdate) {
        $required_fields[] = 'password';
    }
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }
    
    if (!empty($errors)) {
        return $errors;
    }
    
    $nameValidation = validateName($data['f_name'], 'First name');
    if (!$nameValidation['valid']) {
        $errors[] = $nameValidation['message'];
    }
    
    $nameValidation = validateName($data['l_name'], 'Last name');
    if (!$nameValidation['valid']) {
        $errors[] = $nameValidation['message'];
    }
    
    $emailValidation = validateEmail($data['email']);
    if (!$emailValidation['valid']) {
        $errors[] = $emailValidation['message'];
    }
    
    $dobValidation = validateDOB($data['dob']);
    if (!$dobValidation['valid']) {
        $errors[] = $dobValidation['message'];
    }
    
    $genderValidation = validateGender($data['gender']);
    if (!$genderValidation['valid']) {
        $errors[] = $genderValidation['message'];
    }
    
    $addressValidation = validateAddress($data['address']);
    if (!$addressValidation['valid']) {
        $errors[] = $addressValidation['message'];
    }
    
    $usernameValidation = validateUsername($data['username']);
    if (!$usernameValidation['valid']) {
        $errors[] = $usernameValidation['message'];
    }
    
    if (isset($data['password']) && !empty($data['password'])) {
        $passwordValidation = validatePassword($data['password']);
        if (!$passwordValidation['valid']) {
            $errors[] = $passwordValidation['message'];
        }
    }
    
    return $errors;
}

function getAllPatients($conn) {
    return findAllPatients($conn);
}

function getPatient($conn, $id) {
    if (!is_numeric($id)) {
        throw new Exception("Invalid patient ID");
    }
    
    $patient = findPatientById($conn, $id);
    if (!$patient) {
        throw new Exception("Patient not found", 404);
    }
    
    return $patient;
}

function createPatient($conn, $data) {
    $errors = validatePatientData($data);
    if (!empty($errors)) {
        throw new Exception($errors[0]);
    }
    
   
    $existingPatient = findPatientByEmail($conn, $data['email']);
    if ($existingPatient) {
        throw new Exception("Email already registered");
    }
    
    $patientId = insertPatient($conn, $data);
    if (!$patientId) {
        throw new Exception("Failed to create patient");
    }
    
    return $patientId;
}

function updatePatient($conn, $id, $data) {
    if (!is_numeric($id)) {
        throw new Exception("Invalid patient ID");
    }
    
    $data = sanitizeData($data);
    
    $errors = validatePatientData($data);
    if (!empty($errors)) {
        throw new Exception($errors[0]);
    }
    
    $existingPatient = findPatientById($conn, $id);
    if (!$existingPatient) {
        throw new Exception("Patient not found", 404);
    }
    
    if ($data['email'] !== $existingPatient['Email']) {
        $emailPatient = findPatientByEmail($conn, $data['email']);
        if ($emailPatient && $emailPatient['Patient_ID'] != $id) {
            throw new Exception("Email already registered by another patient");
        }
    }
    
    if ($data['username'] !== $existingPatient['Username']) {
        $usernamePatient = findPatientByUsername($conn, $data['username']);
        if ($usernamePatient && $usernamePatient['Patient_ID'] != $id) {
            throw new Exception("Username already taken by another patient");
        }
    }
    
    $success = updatePatientById($conn, $id, $data);
    if (!$success) {
        throw new Exception("Failed to update patient");
    }
    
    return true;
}

function deletePatient($conn, $id) {
    if (!is_numeric($id)) {
        throw new Exception("Invalid patient ID");
    }
    
    $existingPatient = findPatientById($conn, $id);
    if (!$existingPatient) {
        throw new Exception("Patient not found", 404);
    }
    
    $success = deletePatientById($conn, $id);
    if (!$success) {
        throw new Exception("Failed to delete patient");
    }
    
    return true;
}


function findPatientByEmail($conn, $email) {
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM Patient WHERE Email = '$email'";
    $result = mysqli_query($conn, $query);
    
    $patient = null;
    if ($result) {
        $patient = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $patient;
}

function findPatientByUsername($conn, $username) {
    $username = mysqli_real_escape_string($conn, $username);
    $query = "SELECT * FROM Patient WHERE Username = '$username'";
    $result = mysqli_query($conn, $query);
    
    $patient = null;
    if ($result) {
        $patient = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $patient;
}