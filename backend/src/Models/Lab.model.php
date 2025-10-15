<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/patient.model.php';

function findLabByUsername($conn, $username) {
    $username = mysqli_real_escape_string($conn, $username);
    $query = "SELECT Lab_ID, Lab_Name, Location, Contact_Num, Email, Username, Password_Hash 
              FROM Lab WHERE Username = '$username'";
    $result = mysqli_query($conn, $query);
    
    $lab = null;
    if ($result) {
        $lab = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $lab;
}

function findLabById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT Lab_ID, Lab_Name, Location, Contact_Num, Email, Username 
              FROM Lab WHERE Lab_ID = '$id'";
    $result = mysqli_query($conn, $query);
    
    $lab = null;
    if ($result) {
        $lab = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $lab;
}

function findLabByEmail($conn, $email) {
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT Lab_ID FROM Lab WHERE Email = '$email'";
    $result = mysqli_query($conn, $query);
    
    $lab = null;
    if ($result) {
        $lab = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $lab;
}

function insertLab($conn, $data) {
    $lab_name = mysqli_real_escape_string($conn, $data['lab_name']);
    $location = mysqli_real_escape_string($conn, $data['location']);
    $contact_num = mysqli_real_escape_string($conn, $data['contact_num']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO Lab (Lab_Name, Location, Contact_Num, Email, Username, Password_Hash) 
              VALUES ('$lab_name', '$location', '$contact_num', '$email', '$username', '$hashed_password')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    return false;
}
