<?php

namespace BackupCenter\Services;

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;

class SftpGoService
{
    private array $settings;

    public function __construct()
    {
        $db = new Database(
            Paths::database() . '/backupcenter.db'
        );

        $pdo = $db->getConnection();

        $this->settings = $pdo->query("
            SELECT *
            FROM settings
            WHERE id=1
        ")->fetch(\PDO::FETCH_ASSOC);
    }

    public function isEnabled(): bool
    {
        return (int)($this->settings['sftpgo_enabled'] ?? 0) === 1;
    }

    private function token(): string
    {
        $url = "http://{$this->settings['sftpgo_host']}:{$this->settings['sftpgo_port']}/api/v2/token";

        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_HTTPGET => true,

            CURLOPT_USERPWD =>
                $this->settings["sftpgo_username"] .
                ":" .
                $this->settings["sftpgo_password"],

            CURLOPT_HTTPAUTH => CURLAUTH_BASIC

        ]);

        $response = curl_exec($ch);

        if ($response === false) {

            throw new \Exception(curl_error($ch));

        }

        curl_close($ch);

        $json = json_decode($response, true);

        if (empty($json["access_token"])) {

            throw new \Exception("No se pudo obtener el token.");

        }

        return $json["access_token"];
    }

    public function getToken(): string
    {
        return $this->token();
    }

    public function generatePassword(int $length = 16): string
    {
        $characters =
            'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*';

        $password = '';

        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {

            $password .= $characters[random_int(0, $max)];

        }

        return $password;
    }    

    public function createUser(
        string $username,
        string $password
    ): array
    {
        $token = $this->getToken();

        $home = rtrim(
            $this->settings["sftpgo_base_path"],
            "/"
        ) . "/" . $username;

        $data = [

            "status" => 1,

            "username" => $username,

            "password" => $password,

            "home_dir" => $home,

            "permissions" => [

                "/" => ["*"]

            ]

        ];

        $url =
            "http://{$this->settings['sftpgo_host']}:{$this->settings['sftpgo_port']}/api/v2/users";

        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => json_encode($data),

            CURLOPT_HTTPHEADER => [

                "Authorization: Bearer {$token}",

                "Content-Type: application/json"

            ]

        ]);

        $response = curl_exec($ch);

        if ($response === false) {

            throw new \Exception(curl_error($ch));

        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [

            "status" => $status,

            "response" => json_decode($response, true),

            "raw" => $response

        ];
    }    

    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($source)) {
            throw new \Exception("No existe la carpeta template: {$source}");
        }

        if (is_dir($destination)) {
            throw new \Exception("La carpeta destino ya existe: {$destination}");
        }

        if (!mkdir($destination, 0775, true)) {
            throw new \Exception("No se pudo crear la carpeta {$destination}");
        }

        $items = scandir($source);

        foreach ($items as $item) {

            if ($item === "." || $item === "..") {
                continue;
            }

            $src = $source . DIRECTORY_SEPARATOR . $item;
            $dst = $destination . DIRECTORY_SEPARATOR . $item;

            if (is_dir($src)) {

                $this->copyDirectory($src, $dst);

            } else {

                if (!copy($src, $dst)) {
                    throw new \Exception("No se pudo copiar {$src}");
                }

            }
        }
    }

    private function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory), ['.', '..']);

        foreach ($items as $item) {
            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }

    public function deleteUser(string $username): bool
    {
        $token = $this->getToken();

        $url =
            "http://{$this->settings['sftpgo_host']}:{$this->settings['sftpgo_port']}/api/v2/users/" .
            urlencode($username);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}"
            ]
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // SFTPGo puede responder 200 o 204 al eliminar
        if (!in_array($status, [200, 204])) {
            throw new \Exception(
                "No se pudo eliminar el usuario SFTPGo. HTTP {$status}. {$response}"
            );
        }

        return true;
    }

    public function deleteClientFolder(string $alias): bool
    {
        $folder = rtrim(
            $this->settings["sftpgo_base_path"],
            "/\\"
        ) . DIRECTORY_SEPARATOR . $alias;

        if (!is_dir($folder)) {
            return true;
        }

        $items = array_diff(scandir($folder), ['.', '..']);

        if (count($items) > 0) {
            return false;
        }

        return rmdir($folder);
    }

    public function provisionClient(string $alias): array
    {
        $basePath = rtrim(
            $this->settings["sftpgo_base_path"],
            "/\\"
        );

        


        $template = $basePath . DIRECTORY_SEPARATOR . "_template";
        $home = $basePath . DIRECTORY_SEPARATOR . $alias;

        if (is_dir($home)) {
            throw new \Exception(
                "La carpeta del cliente ya existe."
            );
        }

        if (!is_dir($template)) {
            throw new \Exception(
                "No existe la carpeta _template."
            );
        }

        try {

            // Clonar la estructura del template
            $this->copyDirectory(
                $template,
                $home
            );

            // Generar contraseña
            $password = $this->generatePassword();

            // Crear usuario en SFTPGo
            $result = $this->createUser(
                $alias,
                $password
            );

            if ($result["status"] != 201) {

                throw new \Exception(
                    "No fue posible crear el usuario en SFTPGo."
                );

            }

            return [

                "username" => $alias,

                "password" => $password,

                "home_dir" => $home

            ];

        } catch (\Throwable $e) {

            // Si ya se creó la carpeta, eliminarla
            $this->deleteDirectory($home);

            throw $e;

        }
    }
}