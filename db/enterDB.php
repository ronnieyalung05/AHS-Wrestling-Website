<?php

$configFile = "/../config/db.ini";
$config = parse_ini_file(__DIR__ . $configFile, true);

// set variables using config file
$hostname = $config["database"]["hostname"];
$database = $config["database"]["database"];
$username = $config["database"]["username"];
$password = $config["database"]["password"];

// Create PDO instance
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
