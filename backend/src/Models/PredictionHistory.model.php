<?php

require_once __DIR__ . '/../../config/database.php';

function insertPredictionHistory($conn, $data) {
    $report_id = mysqli_real_escape_string($conn, $data['report_id']);
    $ferritin = mysqli_real_escape_string($conn, $data['ferritin']);
    $vitamin_b12 = mysqli_real_escape_string($conn, $data['vitamin_b12']);
    $crp = mysqli_real_escape_string($conn, $data['crp']);
    $afp = mysqli_real_escape_string($conn, $data['afp']);
    $hba1c = mysqli_real_escape_string($conn, $data['hba1c']);
    $cystatin_c = mysqli_real_escape_string($conn, $data['cystatin_c']);

    
    $query = "INSERT INTO Prediction_History (
                Report_ID, Ferritin, Vitamin_B12, CRP, Afp, HbA1c, Cystatin_C
                
              ) VALUES (
                '$report_id', '$ferritin', '$vitamin_b12', '$crp', '$afp', '$hba1c', '$cystatin_c'
              )";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    error_log("MySQL Error in insertPredictionHistory: " . mysqli_error($conn));
    error_log("Query: " . $query);
    
    return false;
}

function findPredictionByReportId($conn, $report_id) {
    $report_id = mysqli_real_escape_string($conn, $report_id);
    $query = "SELECT * FROM Prediction_History WHERE Report_ID = '$report_id'";
    $result = mysqli_query($conn, $query);
    
    $prediction = null;
    if ($result) {
        $prediction = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $prediction;
}

function findPredictionsByPatientId($conn, $patient_id) {
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $query = "SELECT ph.*, r.created_at as report_date
              FROM Prediction_History ph
              INNER JOIN Report r ON ph.Report_ID = r.Report_ID
              WHERE r.Patient_ID = '$patient_id'
              ORDER BY r.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $predictions = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $predictions[] = $row;
        }
        mysqli_free_result($result);
    }
    
    return $predictions;
}
