<?php

require_once __DIR__ . '/../Models/Report.model.php';
require_once __DIR__ . '/../Models/patient.model.php';
require_once __DIR__ . '/../Models/Lab.model.php';
require_once __DIR__ . '/Prediction.service.php';

function createReportWithPrediction($conn, $lab_id, $data) {
    $required = [
        'patient_id', 'hb', 'mcv', 'wbc', 'neutrophils', 'fpg', 'egfr', 
        'creatinine', 'ast', 'alt', 'hct', 'rbc', 'mch', 'mchc', 'lymphocytes',
        'ggt', 'albumin', 'urea', 'triglycerides', 'cholesterol_total', 
        'hdl', 'ldl', 'alp', 'bilirubin_total', 'bilirubin_direct'
    ];
    
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
        }
    }
    
    $patient = findPatientById($conn, $data['patient_id']);
    if (!$patient) {
        return ['success' => false, 'message' => 'Patient not found'];
    }
    
    if (!isPatientLinkedToLab($conn, $data['patient_id'], $lab_id)) {
        return ['success' => false, 'message' => 'Patient is not linked to this lab'];
    }
    
    $report_id = insertReport($conn, $data);
    
    if (!$report_id) {
        return ['success' => false, 'message' => 'Failed to create report'];
    }
    
    $predictionResult = callPredictionAPI($data, $patient['DOB'], $patient['Gender']);
    
    if (!$predictionResult['success']) {
        mysqli_query($conn, "DELETE FROM Report WHERE Report_ID = '$report_id'");
        return $predictionResult;
    }
    
    $savePredictionResult = processPredictions($conn, $report_id, $predictionResult['predictions']);
    
    if (!$savePredictionResult['success']) {
        mysqli_query($conn, "DELETE FROM Report WHERE Report_ID = '$report_id'");
        return $savePredictionResult;
    }
    
    $report = findReportById($conn, $report_id);
    $prediction = findPredictionByReportId($conn, $report_id);
    
    return [
        'success' => true,
        'report' => $report,
        'predictions' => $predictionResult['predictions'],
        'prediction_history' => $prediction
    ];
}

function getPatientReports($conn, $patient_id) {
    $reports = findReportsByPatientId($conn, $patient_id);
    
    foreach ($reports as &$report) {
        $prediction = findPredictionByReportId($conn, $report['Report_ID']);
        $report['prediction'] = $prediction;
    }
    
    return ['success' => true, 'reports' => $reports];
}

function getReportWithPrediction($conn, $report_id, $patient_id) {
    $report = findReportById($conn, $report_id);
    
    if (!$report) {
        return ['success' => false, 'message' => 'Report not found'];
    }
    
    if ($report['Patient_ID'] != $patient_id) {
        return ['success' => false, 'message' => 'Unauthorized access'];
    }
    
    $prediction = findPredictionByReportId($conn, $report_id);
    
    return [
        'success' => true,
        'report' => $report,
        'prediction' => $prediction
    ];
}
