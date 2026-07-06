<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

echo json_encode([
    "success" => true,
    "version" => trim(file_get_contents(__DIR__ . "/../../VERSION")),
    "php" => PHP_VERSION,
    "time" => date("Y-m-d H:i:s")
]);