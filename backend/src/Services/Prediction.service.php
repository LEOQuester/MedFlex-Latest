<?php

require_once __DIR__ . '/../Models/PredictionHistory.model.php';

function callPredictionAPI($reportData, $patientDOB, $patientGender) {
    $dob = new DateTime($patientDOB);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
    
    $sex = ($patientGender === 'Male' || $patientGender === 'M') ? 1 : 0;
    
    $requestBody = [
        'age' => $age,
        'sex' => $sex,
        'hb' => floatval($reportData['hb']),
        'hct' => floatval($reportData['hct']),
        'rbc' => floatval($reportData['rbc']),
        'mcv' => floatval($reportData['mcv']),
        'mch' => floatval($reportData['mch']),
        'mchc' => floatval($reportData['mchc']),
        'wbc' => floatval($reportData['wbc']),
        'neutrophils' => floatval($reportData['neutrophils']),
        'lymphocytes' => floatval($reportData['lymphocytes']),
        'alt' => floatval($reportData['alt']),
        'ast' => floatval($reportData['ast']),
        'ggt' => floatval($reportData['ggt']),
        'albumin' => floatval($reportData['albumin']),
        'urea' => floatval($reportData['urea']),
        'creatinine' => floatval($reportData['creatinine']),
        'egfr' => floatval($reportData['egfr']),
        'fpg' => floatval($reportData['fpg']),
        'triglycerides' => floatval($reportData['triglycerides']),
        'cholesterol_total' => floatval($reportData['cholesterol_total']),
        'hdl' => floatval($reportData['hdl']),
        'ldl' => floatval($reportData['ldl']),
        'alp' => floatval($reportData['alp']),
        'bilirubin_total' => floatval($reportData['bilirubin_total']),
        'bilirubin_direct' => floatval($reportData['bilirubin_direct'])
    ];
    
    $ch = curl_init('http://165.22.108.248/predict');
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    if ($curlError) {
        return ['success' => false, 'message' => 'Prediction API error: ' . $curlError];
    }
    
    if ($httpCode !== 200) {
        return ['success' => false, 'message' => 'Prediction API returned status code: ' . $httpCode];
    }
    
    $predictions = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['success' => false, 'message' => 'Failed to parse prediction response'];
    }
    
    if (!isset($predictions['predictions'])) {
        return ['success' => false, 'message' => 'Invalid prediction response format'];
    }
    
    return ['success' => true, 'predictions' => $predictions['predictions']];
}

function processPredictions($conn, $report_id, $predictions) {
    $ferritin = $predictions['Ferritin']['value'] ?? 0;
    $ferritin_status = $predictions['Ferritin']['status'] ?? 'Normal';
    
    $b12 = $predictions['B12']['value'] ?? 0;
    $b12_status = $predictions['B12']['status'] ?? 'Normal';
    
    $crp = $predictions['CRP']['value'] ?? 0;
    $crp_status = $predictions['CRP']['status'] ?? 'Normal';
    
    $afp = $predictions['AFP']['value'] ?? 0;
    
    $hba1c = $predictions['HBA1C']['value'] ?? 0;
    $hba1c_status = $predictions['HBA1C']['status'] ?? 'Normal';
    
    $cystatin_c = $predictions['Cystatin_C']['value'] ?? 0;
    $cystatin_c_status = $predictions['Cystatin_C']['status'] ?? 'Normal';
    
    $ferritin_abnormal = ($ferritin_status !== 'Normal') ? 1 : 0;
    $b12_abnormal = ($b12_status !== 'Normal') ? 1 : 0;
    $crp_abnormal = ($crp_status !== 'Normal') ? 1 : 0;
    $cystatin_c_abnormal = ($cystatin_c_status !== 'Normal') ? 1 : 0;
    $hba1c_abnormal = ($hba1c_status !== 'Normal') ? 1 : 0;
    
    $predictionData = [
        'report_id' => $report_id,
        'ferritin' => $ferritin,
        'vitamin_b12' => $b12,
        'crp' => $crp,
        'afp' => $afp,
        'hba1c' => $hba1c,
        'cystatin_c' => $cystatin_c,
        'ferritin_abnormal' => $ferritin_abnormal,
        'b12_abnormal' => $b12_abnormal,
        'crp_abnormal' => $crp_abnormal,
        'cystatin_c_abnormal' => $cystatin_c_abnormal,
        'hba1c_abnormal' => $hba1c_abnormal
    ];
    
    $prediction_id = insertPredictionHistory($conn, $predictionData);
    
    if ($prediction_id) {
        return ['success' => true, 'prediction_id' => $prediction_id, 'predictions' => $predictions];
    }
    
    $mysqlError = mysqli_error($conn);
    return ['success' => false, 'message' => 'Failed to save prediction history. MySQL Error: ' . $mysqlError];
}
