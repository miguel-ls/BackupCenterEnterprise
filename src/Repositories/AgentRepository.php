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
            SELECT
                id,
                client_id,
                name,
                host,
                port,
                username,
                password,
                hostkey,
                protocol,
                remote_path
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
                schedule
            FROM jobs
            WHERE connection_id = ?
            AND enabled = 1
            ORDER BY id
        ");

        $stmt->execute([$connectionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    
    public function fileExists(
        int $jobId,
        string $sha256
    ): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM uploaded_files
            WHERE job_id = ?
            AND sha256 = ?
        ");

        $stmt->execute([
            $jobId,
            $sha256
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }  
    
public function saveFile(
    int $jobId,
    string $filename,
    int $filesize,
    string $sha256
): void
{
    $stmt = $this->pdo->prepare("
        INSERT INTO uploaded_files
        (
            job_id,
            filename,
            filesize,
            sha256,
            uploaded_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $jobId,
        $filename,
        $filesize,
        $sha256,
        date('Y-m-d H:i:s')
    ]);
}    
}