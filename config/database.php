<?php

require_once __DIR__ . '/database_helper.php';

$host = '152.42.164.190';    
$dbname = 'medflex';   
$username = 'root';
$password = 'medflex123';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");