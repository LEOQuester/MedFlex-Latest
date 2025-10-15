<?php

require_once __DIR__ . '/database_helper.php';

<<<<<<< HEAD
$host = 'gondola.proxy.rlwy.net';
$dbname = 'MedFlex';
$username = 'root';
$password = ':RkloXvIzxQNGvFMqDTXQgNOQagejoseJ';
$port = 34184;
=======
$host = '152.42.164.190';    
$dbname = 'medflex';   
$username = 'root';
$password = 'medflex123';
$port = 3306;
>>>>>>> 05b5dff91513ac51d7ff77f8ab2fa219bb8439b2

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");