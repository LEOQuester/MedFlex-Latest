<?php

require_once __DIR__ . '/../../config/database.php';

function findAllPatients($conn) {
    $query = "SELECT Patient_ID, F_name, L_name, DOB, Gender, Address, Email, Username FROM Patient";
    $result = mysqli_query($conn, $query);
    
    $patients = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }
        mysqli_free_result($result);
    }
    
    return $patients;
}

function findPatientById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT Patient_ID, F_name, L_name, DOB, Gender, Address, Email, Username FROM Patient WHERE Patient_ID = '$id'";
    $result = mysqli_query($conn, $query);
    
    $patient = null;
    if ($result) {
        $patient = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $patient;
}

function insertPatient($conn, $data) {
    $f_name = mysqli_real_escape_string($conn, $data['f_name']);
    $l_name = mysqli_real_escape_string($conn, $data['l_name']);
    $dob = mysqli_real_escape_string($conn, $data['dob']);
    $gender = mysqli_real_escape_string($conn, $data['gender']);
    $address = isset($data['address']) ? mysqli_real_escape_string($conn, $data['address']) : '';
    $email = mysqli_real_escape_string($conn, $data['email']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO Patient (F_name, L_name, DOB, Gender, Address, Email, Username, Password_Hash) 
              VALUES ('$f_name', '$l_name', '$dob', '$gender', '$address', '$email', '$username', '$hashed_password')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function updatePatientById($conn, $id, $data) {
    $id = mysqli_real_escape_string($conn, $id);
    $f_name = mysqli_real_escape_string($conn, $data['f_name']);
    $l_name = mysqli_real_escape_string($conn, $data['l_name']);
    $dob = mysqli_real_escape_string($conn, $data['dob']);
    $gender = mysqli_real_escape_string($conn, $data['gender']);
    $address = mysqli_real_escape_string($conn, $data['address']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    
    $passwordUpdate = '';
    if (isset($data['password']) && !empty($data['password'])) {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        $passwordUpdate = ", Password = '$hashed_password'";
    }
    
    $query = "UPDATE Patient 
              SET F_name = '$f_name',
                  L_name = '$l_name',
                  DOB = '$dob',
                  Gender = '$gender',
                  Address = '$address',
                  Email = '$email',
                  Username = '$username'
                  $passwordUpdate
              WHERE Patient_ID = '$id'";
    
    return mysqli_query($conn, $query);
}

function deletePatientById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "DELETE FROM patients WHERE id = '$id'";
    return mysqli_query($conn, $query);
}

function getPatientsByLabId($conn, $lab_id) {
    $lab_id = mysqli_real_escape_string($conn, $lab_id);
    $query = "SELECT p.Patient_ID, p.F_name, p.L_name, p.DOB, p.Gender, p.Address, p.Email, p.Username, lp.created_at
              FROM Patient p
              INNER JOIN Lab_Patient lp ON p.Patient_ID = lp.Patient_ID
              WHERE lp.Lab_ID = '$lab_id'
              ORDER BY lp.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $patients = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }
        mysqli_free_result($result);
    }
    
    return $patients;
}

function linkPatientToLab($conn, $patient_id, $lab_id) {
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $lab_id = mysqli_real_escape_string($conn, $lab_id);
    
    $query = "INSERT INTO Lab_Patient (Lab_ID, Patient_ID) VALUES ('$lab_id', '$patient_id')";
    return mysqli_query($conn, $query);
}

function isPatientLinkedToLab($conn, $patient_id, $lab_id) {
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $lab_id = mysqli_real_escape_string($conn, $lab_id);
    
    $query = "SELECT 1 FROM Lab_Patient WHERE Patient_ID = '$patient_id' AND Lab_ID = '$lab_id'";
    $result = mysqli_query($conn, $query);
    
    $exists = false;
    if ($result && mysqli_num_rows($result) > 0) {
        $exists = true;
        mysqli_free_result($result);
    }
    
    return $exists;
}