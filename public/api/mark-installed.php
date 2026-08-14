<?php

require_once __DIR__ . '/bootstrap.php';

$data = json_decode(file_get_contents("php://input"), true);

$token = $data['InstallToken'] ?? '';

if (!$token) {
    echo json_encode(["success" => false]);
    exit;
}

$pdo = $db->getConnection();

$stmt = $pdo->prepare("
    UPDATE connections
    SET installed = 1
    WHERE install_token = ?
");

$stmt->execute([$token]);

echo json_encode(["success" => true]);