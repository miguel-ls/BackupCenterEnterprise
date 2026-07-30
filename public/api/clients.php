<?php

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Application;
use BackupCenter\Core\Audit;

$app = new Application();

$repository = $app->clientRepository();

$method = $_SERVER['REQUEST_METHOD'];

$body = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($body)) {
    $body = [];
}

switch ($method) {

    case 'GET':

        echo json_encode([
            'success' => true,
            'data' => $repository->getAll()
        ]);

        break;

    case 'POST':

        $id = $repository->create(

            $body['code'] ?? '',

            $body['business_name'] ?? '',

            $body['trade_name'] ?? null,

            $body['ruc'] ?? null,

            $body['contact_name'] ?? null,

            $body['email'] ?? null,

            $body['phone'] ?? null,

            $body['address'] ?? null,

            (int)($body['status'] ?? 1),

            $body['notes'] ?? null

        );

        Audit::info(

            'CLIENTS',

            'CREATE',

            'Cliente ' . ($body['business_name'] ?? '') . ' creado',

            'admin'

        );

        echo json_encode([
            'success' => true,
            'id' => $id
        ]);

        break;

    case 'PUT':

        $ok = $repository->update(

            (int)$body['id'],

            $body['code'] ?? '',

            $body['business_name'] ?? '',

            $body['trade_name'] ?? null,

            $body['ruc'] ?? null,

            $body['contact_name'] ?? null,

            $body['email'] ?? null,

            $body['phone'] ?? null,

            $body['address'] ?? null,

            (int)($body['status'] ?? 1),

            $body['notes'] ?? null

        );

        Audit::info(

            'CLIENTS',

            'UPDATE',

            'Cliente ' . ($body['business_name'] ?? '') . ' actualizado',

            'admin'

        );

        echo json_encode([
            'success' => $ok
        ]);

        break;

    case 'DELETE':

        $ok = $repository->delete(
            (int)$body['id']
        );

        Audit::info(

            'CLIENTS',

            'DELETE',

            'Cliente ID ' . ($body['id'] ?? 0) . ' eliminado',

            'admin'

        );

        echo json_encode([
            'success' => $ok
        ]);

        break;

    default:

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);

}