<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Configure session for CORS - more permissive settings for development
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_domain', '');
ini_set('session.cookie_path', '/');

// Handle CORS before anything else
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    $allowedOrigins = [
        'http://localhost:5500',
        'http://127.0.0.1:5500',
        'http://localhost:3000',
        'http://localhost:80',
        'http://localhost',
        'http://127.0.0.1'
    ];
    
    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
    }
}

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Start session
session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/config/helpers.php';
require __DIR__ . '/src/Controllers/patient.controller.php';
require __DIR__ . '/src/Controllers/Auth.controller.php';
require __DIR__ . '/src/Controllers/Lab.controller.php';
require __DIR__ . '/src/Controllers/PatientAuth.controller.php';


$app = AppFactory::create();


$app->addErrorMiddleware(true, true, true);


$app->addBodyParsingMiddleware();


$app->group('/api', function($app) {
    
    // ========== Authentication Routes ==========
    // Patient Auth
    $app->post('/auth/patient/register', 'handlePatientRegister');
    $app->post('/auth/patient/login', 'handlePatientLogin');
    
    // Lab Auth
    $app->post('/auth/lab/register', 'handleLabRegister');
    $app->post('/auth/lab/login', 'handleLabLogin');
    
    // Common Auth
    $app->post('/auth/logout', 'handleLogout');
    $app->get('/auth/check', 'handleCheckAuth');
    
    
    // ========== Lab Routes ==========
    // Lab manages patients
    $app->post('/lab/patients', 'handleLabCreatePatient');
    $app->get('/lab/patients', 'handleLabGetPatients');
    
    // Lab creates reports with predictions
    $app->post('/lab/reports', 'handleLabCreateReport');
    $app->get('/lab/reports', 'handleLabGetReports');
    
    
    // ========== Patient Routes ==========
    // Patient views their own data
    $app->get('/patient/profile', 'handlePatientGetProfile');
    $app->get('/patient/reports', 'handlePatientGetReports');
    $app->get('/patient/reports/{id}', 'handlePatientGetReport');
    $app->get('/patient/predictions', 'handlePatientGetPredictions');
    
    
    // ========== Old Patient CRUD Routes (Keep for backward compatibility) ==========
    $app->get('/patients', 'handleGetAllPatients');
    $app->get('/patients/{id}', 'handleGetPatient');
    $app->post('/patients', 'handleCreatePatient');
    $app->put('/patients/{id}', 'handleUpdatePatient');
    $app->delete('/patients/{id}', 'handleDeletePatient');
});


$app->run();