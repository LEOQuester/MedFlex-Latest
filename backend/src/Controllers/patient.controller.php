<?php


require_once __DIR__ . '/../Services/patient.service.php';


function sendJSON($response, $data, $status = 200) {
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}

function sendError($response, $message, $status = 400) {
    return sendJSON($response, ['error' => $message], $status);
}


function handleGetAllPatients($request, $response) {
    global $conn;
    try {
        $patients = getAllPatients($conn);
        return sendJSON($response, $patients);
    } catch (Exception $e) {
        return sendError($response, $e->getMessage(), 500);
    }
}

function handleGetPatient($request, $response, $args) {
    global $conn;
    try {
        $patient = getPatient($conn, $args['id']);
        return sendJSON($response, $patient);
    } catch (Exception $e) {
        $status = $e->getCode() ?: 500;
        return sendError($response, $e->getMessage(), $status);
    }
}

function handleCreatePatient($request, $response) {
    global $conn;
    try {
        $data = $request->getParsedBody();
        $patientId = createPatient($conn, $data);
        return sendJSON($response, ['id' => $patientId], 201);
    } catch (Exception $e) {
        return sendError($response, $e->getMessage(), 400);
    }
}

function handleUpdatePatient($request, $response, $args) {
    global $conn;
    try {
        $data = $request->getParsedBody();
        updatePatient($conn, $args['id'], $data);
        return sendJSON($response, ['message' => 'Patient updated successfully']);
    } catch (Exception $e) {
        $status = $e->getCode() ?: 400;
        return sendError($response, $e->getMessage(), $status);
    }
}

function handleDeletePatient($request, $response, $args) {
    global $conn;
    try {
        deletePatient($conn, $args['id']);
        return $response->withStatus(204);
    } catch (Exception $e) {
        $status = $e->getCode() ?: 500;
        return sendError($response, $e->getMessage(), $status);
    }
}