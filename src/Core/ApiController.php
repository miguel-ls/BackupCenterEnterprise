<?php

namespace BackupCenter\Core;

class ApiController
{
    public static function boot(): void
    {
        header('Access-Control-Allow-Origin: http://localhost:5173');

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        header('Access-Control-Allow-Headers: Content-Type');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

            http_response_code(200);

            exit;

        }
    }

    public static function method(
        string|array $methods
    ): void
    {
        if (!is_array($methods)) {

            $methods = [$methods];

        }

        if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {

            ApiResponse::error(
                'Método no permitido',
                405
            );

        }
    }

    public static function body(): array
    {
        return json_decode(
            file_get_contents('php://input'),
            true
        ) ?? [];
    }
}