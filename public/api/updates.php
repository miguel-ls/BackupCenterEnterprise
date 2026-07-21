<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$versionFile = __DIR__ . '/../../resources/updates/version.json';

if (!file_exists($versionFile)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró la metadata de actualización'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => json_decode(file_get_contents($versionFile), true)
]);
