<?php

require_once __DIR__ . '/../Services/Auth.service.php';
require_once __DIR__ . '/../Services/Report.service.php';
require_once __DIR__ . '/../Models/Lab.model.php';
require_once __DIR__ . '/../Models/patient.model.php';

function handleLabCreatePatient($request, $response) {
    global $conn;
    
    // Check authentication
    $auth = checkAuth('lab');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $lab_id = $auth['user_id'];
    $data = $request->getParsedBody();
    
    // Insert patient
    $patient_id = insertPatient($conn, $data);
    
    if (!$patient_id) {
        return jsonResponse($response, ['success' => false, 'message' => 'Failed to create patient'], 400);
    }
    
    // Link patient to lab
    $linked = linkPatientToLab($conn, $patient_id, $lab_id);
    
    if (!$linked) {
        return jsonResponse($response, ['success' => false, 'message' => 'Failed to link patient to lab'], 400);
    }
    
    $patient = findPatientById($conn, $patient_id);
    
    return jsonResponse($response, [
        'success' => true,
        'message' => 'Patient created successfully',
        'patient' => $patient
    ], 201);
}

function handleLabGetPatients($request, $response) {
    global $conn;
    
    // Check authentication
    $auth = checkAuth('lab');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $lab_id = $auth['user_id'];
    $patients = getPatientsByLabId($conn, $lab_id);
    
    return jsonResponse($response, [
        'success' => true,
        'patients' => $patients
    ], 200);
}

function handleLabCreateReport($request, $response) {
    global $conn;
    
    try {
        // Check authentication
        $auth = checkAuth('lab');
        if (!$auth['authenticated']) {
            return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
        }
        
        $lab_id = $auth['user_id'];
        $data = $request->getParsedBody();
        
        $result = createReportWithPrediction($conn, $lab_id, $data);
        
        if ($result['success']) {
            return jsonResponse($response, $result, 201);
        }
        
        return jsonResponse($response, $result, 400);
    } catch (Exception $e) {
        return jsonResponse($response, [
            'success' => false, 
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

function handleLabGetReports($request, $response) {
    global $conn;
    
    // Check authentication
    $auth = checkAuth('lab');
    if (!$auth['authenticated']) {
        return jsonResponse($response, ['success' => false, 'message' => $auth['message']], 401);
    }
    
    $lab_id = $auth['user_id'];
    $patient_id = $request->getQueryParams()['patient_id'] ?? null;
    
    if (!$patient_id) {
        return jsonResponse($response, ['success' => false, 'message' => 'Patient ID is required'], 400);
    }
    
    // Verify patient is linked to this lab
    if (!isPatientLinkedToLab($conn, $patient_id, $lab_id)) {
        return jsonResponse($response, ['success' => false, 'message' => 'Patient not linked to this lab'], 403);
    }
    
    $reports = findReportsByPatientIdForLab($conn, $patient_id, $lab_id);
    
    // Get predictions for each report
    foreach ($reports as &$report) {
        $prediction = findPredictionByReportId($conn, $report['Report_ID']);
        $report['prediction'] = $prediction;
    }
    
    return jsonResponse($response, [
        'success' => true,
        'reports' => $reports
    ], 200);
}

