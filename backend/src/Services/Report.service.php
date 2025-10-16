<?php

require_once __DIR__ . '/../Models/Report.model.php';
require_once __DIR__ . '/../Models/patient.model.php';
require_once __DIR__ . '/../Models/Lab.model.php';
require_once __DIR__ . '/Prediction.service.php';

function createReportWithPrediction($conn, $lab_id, $data) {
    // Validate required fields
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
    
    // Check if patient exists and is linked to this lab
    $patient = findPatientById($conn, $data['patient_id']);
    if (!$patient) {
        return ['success' => false, 'message' => 'Patient not found'];
    }
    
    // Check if patient is linked to this lab
    if (!isPatientLinkedToLab($conn, $data['patient_id'], $lab_id)) {
        return ['success' => false, 'message' => 'Patient is not linked to this lab'];
    }
    
    // Insert report
    $report_id = insertReport($conn, $data);
    
    if (!$report_id) {
        return ['success' => false, 'message' => 'Failed to create report'];
    }
    
    // Call prediction API
    $predictionResult = callPredictionAPI($data, $patient['DOB'], $patient['Gender']);
    
    if (!$predictionResult['success']) {
        // Delete the report if prediction fails
        mysqli_query($conn, "DELETE FROM Report WHERE Report_ID = '$report_id'");
        return $predictionResult;
    }
    
    // Save predictions
    $savePredictionResult = processPredictions($conn, $report_id, $predictionResult['predictions']);
    
    if (!$savePredictionResult['success']) {
        // Delete the report if saving predictions fails
        mysqli_query($conn, "DELETE FROM Report WHERE Report_ID = '$report_id'");
        return $savePredictionResult;
    }
    
    // Get the complete report with predictions
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
    
    // Get predictions for each report
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
    
    // Verify report belongs to the patient
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
