<?php

require_once __DIR__ . '/../Services/Auth.service.php';

function handlePatientRegister($request, $response) {
    global $conn;
    
    try {
        error_log("=== Patient Registration Started ===");
        
        $data = $request->getParsedBody();
        error_log("Request data: " . print_r($data, true));
        
        $result = registerPatient($conn, $data);
        error_log("Registration result: " . print_r($result, true));
        
        $statusCode = $result['success'] ? 201 : 400;
        return jsonResponse($response, $result, $statusCode);
    } catch (Exception $e) {
        error_log("ERROR in handlePatientRegister: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        return jsonResponse($response, [
            'success' => false, 
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

function handlePatientLogin($request, $response) {
    global $conn;
    
    try {
        error_log("=== Patient Login Started ===");
        
        $data = $request->getParsedBody();
        error_log("Request data: " . print_r($data, true));
        
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        error_log("Calling loginPatient for username: $username");
        $result = loginPatient($conn, $username, $password);
        error_log("Login result: " . print_r($result, true));
        
        $statusCode = $result['success'] ? 200 : 401;
        error_log("Returning status code: $statusCode");
        
        return jsonResponse($response, $result, $statusCode);
    } catch (Exception $e) {
        error_log("ERROR in handlePatientLogin: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        return jsonResponse($response, [
            'success' => false, 
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

function handleLabRegister($request, $response) {
    global $conn;
    
    try {
        error_log("=== Lab Registration Started ===");
        
        $data = $request->getParsedBody();
        error_log("Request data: " . print_r($data, true));
        
        $result = registerLab($conn, $data);
        error_log("Registration result: " . print_r($result, true));
        
        $statusCode = $result['success'] ? 201 : 400;
        return jsonResponse($response, $result, $statusCode);
    } catch (Exception $e) {
        error_log("ERROR in handleLabRegister: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        return jsonResponse($response, [
            'success' => false, 
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

function handleLabLogin($request, $response) {
    global $conn;
    
    $data = $request->getParsedBody();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    $result = loginLab($conn, $username, $password);
    
    $statusCode = $result['success'] ? 200 : 401;
    return jsonResponse($response, $result, $statusCode);
}

function handleLogout($request, $response) {
    $result = logout();
    return jsonResponse($response, $result, 200);
}

function handleCheckAuth($request, $response) {
    $result = checkAuth();
    
    $statusCode = $result['authenticated'] ? 200 : 401;
    return jsonResponse($response, $result, $statusCode);
}

