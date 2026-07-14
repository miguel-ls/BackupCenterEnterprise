<?php

namespace BackupCenter\Core;

use BackupCenter\Repositories\SessionRepository;

class Auth
{
    public static function token(): ?string
    {
        return ApiController::bearerToken();
    }

    public static function check(): bool
    {
        $token = self::token();

        if (!$token) {
            return false;
        }

        /*
         * En la siguiente etapa validaremos el token
         * contra la tabla user_sessions.
         */

        return true;
    }

    public static function require(): void
    {
        if (!self::check()) {

            ApiResponse::error(
                "No autenticado.",
                401
            );

        }
    }
}