<?php

require_once __DIR__ . '/../Services/Auth.service.php';
require_once __DIR__ . '/../Services/Report.service.php';
require_once __DIR__ . '/../Models/Report.model.php';
require_once __DIR__ . '/../Models/PredictionHistory.model.php';

function handlePatientGetReports($request, $response) {
    global $conn;
    
    $auth = checkAuth('patient');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $patient_id = $auth['user_id'];
    $result = getPatientReports($conn, $patient_id);
    
    return jsonResponse($response, $result, 200);
}

function handlePatientGetReport($request, $response, $args) {
    global $conn;
    
    $auth = checkAuth('patient');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $patient_id = $auth['user_id'];
    $report_id = $args['id'];
    
    $result = getReportWithPrediction($conn, $report_id, $patient_id);
    
    if ($result['success']) {
        return jsonResponse($response, $result, 200);
    }
    
    return jsonResponse($response, $result, 404);
}

function handlePatientGetPredictions($request, $response) {
    global $conn;
    
    $auth = checkAuth('patient');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $patient_id = $auth['user_id'];
    $predictions = findPredictionsByPatientId($conn, $patient_id);
    
    return jsonResponse($response, [
        'success' => true,
        'predictions' => $predictions
    ], 200);
}

function handlePatientGetProfile($request, $response) {
    global $conn;
    
    $auth = checkAuth('patient');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $patient_id = $auth['user_id'];
    $patient = findPatientById($conn, $patient_id);
    
    if ($patient) {
        return jsonResponse($response, [
            'success' => true,
            'patient' => $patient
        ], 200);
    }
    
    return jsonResponse($response, ['success' => false, 'message' => 'Patient not found'], 404);
}

