<?php


require_once __DIR__ . '/../Models/patient.model.php';

function validatePatientData($data, $isUpdate = false) {
    $errors = [];
    $required_fields = ['f_name', 'l_name', 'dob', 'gender', 'address', 'email', 'username'];
    
    
    if (!$isUpdate) {
        $required_fields[] = 'password';
    }
    
   
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }
    
  
    if (isset($data['email']) && !empty($data['email'])) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
    }
    
  
    if (isset($data['dob']) && !empty($data['dob'])) {
        if (!strtotime($data['dob'])) {
            $errors[] = "Invalid date of birth format";
        }
    }
    
    
    if (isset($data['gender']) && !empty($data['gender'])) {
        $validGenders = ['Male', 'Female', 'Other'];
        if (!in_array($data['gender'], $validGenders)) {
            $errors[] = "Invalid gender value";
        }
    }
    
   
    if (isset($data['password']) && !empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            $errors[] = "Password must be at least 6 characters long";
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
    
    $errors = validatePatientData($data);
    if (!empty($errors)) {
        throw new Exception($errors[0]);
    }
    
 
    $existingPatient = findPatientById($conn, $id);
    if (!$existingPatient) {
        throw new Exception("Patient not found", 404);
    }
    
   
    if ($data['email'] !== $existingPatient['email']) {
        $emailPatient = findPatientByEmail($conn, $data['email']);
        if ($emailPatient) {
            throw new Exception("Email already registered by another patient");
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