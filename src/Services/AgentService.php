<?php

declare(strict_types=1);

namespace BackupCenter\Services;

use BackupCenter\Repositories\AgentRepository;
use BackupCenter\Core\ApiResponse;
use PDO;

class AgentService
{
    private AgentRepository $repository;

    public function __construct(PDO $pdo)
    {
        $this->repository = new AgentRepository($pdo);
    }

public function exists(): void
{
    try
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $exists = $this->repository->fileExists(
            (int)$data['jobId'],
            $data['sha256']
        );

        ApiResponse::success([
            'exists' => $exists
        ]);
    }
    catch (\Throwable $e)
    {
        http_response_code(500);

        echo json_encode([
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString()
        ]);
    }
}

public function registerFile(): void
{
    try
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $this->repository->saveFile(
            (int)$data['JobId'],
            $data['FileName'],
            (int)$data['FileSize'],
            $data['Sha256']
        );

        ApiResponse::success([
            'registered' => true
        ]);
    }
    catch (\Throwable $e)
    {
        http_response_code(500);

        echo json_encode([
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString()
        ]);
        
    }
}

    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['installToken'])) {
            ApiResponse::error('Install Token is required');
            return;
        }

        $connection = $this->repository->findByInstallToken($input['installToken']);

        $jobs = $this->repository->getEnabledJobs(
            (int)$connection['id']
        );        

        if ($connection === null) {
            ApiResponse::error('Invalid Install Token');
            return;
        }

        ApiResponse::success([

            'connection' => [

                'id'         => (int)$connection['id'],
                'clientId'   => (int)$connection['client_id'],
                'name'       => $connection['name'],

                'host'       => $connection['host'],
                'port'       => (int)$connection['port'],
                'protocol'   => $connection['protocol'],

                'username'   => $connection['username'],
                'password'   => $connection['password'],
                'hostKey'    => $connection['hostkey'],

                'remotePath' => $connection['remote_path']

            ],

            'settings' => [

                'pollInterval' => 60

            ],

            'jobs' => $jobs

        ]);
    }
}