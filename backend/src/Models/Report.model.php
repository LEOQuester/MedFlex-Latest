<?php

require_once __DIR__ . '/../../config/database.php';

function insertReport($conn, $data) {
    $patient_id = mysqli_real_escape_string($conn, $data['patient_id']);
    $hb = mysqli_real_escape_string($conn, $data['hb']);
    $mcv = mysqli_real_escape_string($conn, $data['mcv']);
    $wbc = mysqli_real_escape_string($conn, $data['wbc']);
    $neutrophils = mysqli_real_escape_string($conn, $data['neutrophils']);
    $fpg = mysqli_real_escape_string($conn, $data['fpg']);
    $egfr = mysqli_real_escape_string($conn, $data['egfr']);
    $creatinine = mysqli_real_escape_string($conn, $data['creatinine']);
    $ast = mysqli_real_escape_string($conn, $data['ast']);
    $alt = mysqli_real_escape_string($conn, $data['alt']);
    $hct = mysqli_real_escape_string($conn, $data['hct']);
    $rbc = mysqli_real_escape_string($conn, $data['rbc']);
    $mch = mysqli_real_escape_string($conn, $data['mch']);
    $mchc = mysqli_real_escape_string($conn, $data['mchc']);
    $lymphocytes = mysqli_real_escape_string($conn, $data['lymphocytes']);
    $ggt = mysqli_real_escape_string($conn, $data['ggt']);
    $albumin = mysqli_real_escape_string($conn, $data['albumin']);
    $urea = mysqli_real_escape_string($conn, $data['urea']);
    $triglycerides = mysqli_real_escape_string($conn, $data['triglycerides']);
    $cholesterol_total = mysqli_real_escape_string($conn, $data['cholesterol_total']);
    $hdl = mysqli_real_escape_string($conn, $data['hdl']);
    $ldl = mysqli_real_escape_string($conn, $data['ldl']);
    $alp = mysqli_real_escape_string($conn, $data['alp']);
    $bilirubin_total = mysqli_real_escape_string($conn, $data['bilirubin_total']);
    $bilirubin_direct = mysqli_real_escape_string($conn, $data['bilirubin_direct']);
    
    $query = "INSERT INTO Report (
                Patient_ID, Hb, MCV, WBC, Neutrophils, FPG, eGFR, Creatinine, AST, ALT,
                HCT, RBC, MCH, MCHC, Lymphocytes, GGT, Albumin, Urea, Triglycerides,
                Cholesterol_Total, HDL, LDL, ALP, Bilirubin_Total, Bilirubin_Direct, created_at
              ) VALUES (
                '$patient_id', '$hb', '$mcv', '$wbc', '$neutrophils', '$fpg', '$egfr', 
                '$creatinine', '$ast', '$alt', '$hct', '$rbc', '$mch', '$mchc', 
                '$lymphocytes', '$ggt', '$albumin', '$urea', '$triglycerides',
                '$cholesterol_total', '$hdl', '$ldl', '$alp', '$bilirubin_total', 
                '$bilirubin_direct', NOW()
              )";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function findReportById($conn, $report_id) {
    $report_id = mysqli_real_escape_string($conn, $report_id);
    $query = "SELECT * FROM Report WHERE Report_ID = '$report_id'";
    $result = mysqli_query($conn, $query);
    
    $report = null;
    if ($result) {
        $report = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    
    return $report;
}

function findReportsByPatientId($conn, $patient_id) {
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $query = "SELECT * FROM Report WHERE Patient_ID = '$patient_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    
    $reports = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reports[] = $row;
        }
        mysqli_free_result($result);
    }
    
    return $reports;
}

function findReportsByPatientIdForLab($conn, $patient_id, $lab_id) {
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $lab_id = mysqli_real_escape_string($conn, $lab_id);
    
    $checkQuery = "SELECT 1 FROM Lab_Patient WHERE Patient_ID = '$patient_id' AND Lab_ID = '$lab_id'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
        return [];
    }
    
    $query = "SELECT * FROM Report WHERE Patient_ID = '$patient_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    
    $reports = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reports[] = $row;
        }
        mysqli_free_result($result);
    }
    
    return $reports;
}

function getPatientLabIds($conn, $patient_id) {
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $query = "SELECT Lab_ID FROM Lab_Patient WHERE Patient_ID = '$patient_id'";
    $result = mysqli_query($conn, $query);
    
    $lab_ids = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $lab_ids[] = $row['Lab_ID'];
        }
        mysqli_free_result($result);
    }
    
    return $lab_ids;
}
