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

    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['installToken'])) {
            ApiResponse::error('Install Token is required');
            return;
        }

        $connection = $this->repository->findByInstallToken($input['installToken']);

        if ($connection === null) {
            ApiResponse::error('Invalid Install Token');
            return;
        }

        ApiResponse::success([
            'connectionId' => $connection['id'],
            'clientId'     => $connection['client_id'],
            'name'         => $connection['name'],
            'message'      => 'Agent registered successfully'
        ]);
    }
}