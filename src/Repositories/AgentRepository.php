<?php

declare(strict_types=1);

namespace BackupCenter\Repositories;

use PDO;

class AgentRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function findByInstallToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM connections
            WHERE install_token = ?
            LIMIT 1
        ");

        $stmt->execute([$token]);

        $connection = $stmt->fetch(PDO::FETCH_ASSOC);

        return $connection ?: null;
    }    

    public function getEnabledJobs(int $connectionId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                name,
                source,
                destination,
                remote_path,
                schedule,
                enabled
            FROM jobs
            WHERE connection_id = ?
            AND enabled = 1
            ORDER BY id
        ");

        $stmt->execute([$connectionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
}