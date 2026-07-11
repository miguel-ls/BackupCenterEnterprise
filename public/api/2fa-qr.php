<?php

use BackupCenter\Core\ApiController;
use BackupCenter\Core\ApiResponse;
use PragmaRX\Google2FA\Google2FA;

require_once __DIR__ . '/bootstrap.php';

ApiController::boot();

ApiController::method("POST");

$data = ApiController::body();

$id = (int)($data["id"] ?? 0);

if ($id <= 0) {

    ApiResponse::error(
        "Usuario inválido."
    );

}

$stmt = $pdo->prepare("
SELECT
    id,
    username,
    fullname,
    twofactor_secret
FROM users
WHERE id=?
");

$stmt->execute([
    $id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    ApiResponse::error(
        "Usuario no encontrado."
    );

}

$google2fa = new Google2FA();

$secret = $user["twofactor_secret"];

if (empty($secret)) {

    $secret = $google2fa->generateSecretKey();

    $pdo->prepare("
        UPDATE users
        SET twofactor_secret=?
        WHERE id=?
    ")->execute([

        $secret,

        $id

    ]);

}

$uri = $google2fa->getQRCodeUrl(

    "Backup Center Enterprise",

    $user["username"],

    $secret

);

ApiResponse::success([

    "secret" => $secret,

    "uri" => $uri,

    "manual" => true

]);