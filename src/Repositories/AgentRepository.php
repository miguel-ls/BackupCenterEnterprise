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

public function saveExecutionHistory(array $data): void
{
    $this->pdo->beginTransaction();

    try
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO execution_history
            (
                job_id,
                started_at,
                finished_at,
                files_found,
                files_uploaded,
                files_skipped,
                files_failed,
                duration_seconds,
                status,
                created_at
            )
            VALUES
            (
                :job_id,
                :started_at,
                :finished_at,
                :files_found,
                :files_uploaded,
                :files_skipped,
                :files_failed,
                :duration_seconds,
                :status,
                :created_at
            )
        ");

        $stmt->execute([
            ':job_id' => $data['jobId'],
            ':started_at' => $data['startedAt'],
            ':finished_at' => $data['finishedAt'],
            ':files_found' => $data['filesFound'],
            ':files_uploaded' => $data['filesUploaded'],
            ':files_skipped' => $data['filesSkipped'],
            ':files_failed' => $data['filesFailed'],
            ':duration_seconds' => $data['durationSeconds'],
            ':status' => $data['status'],
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        $stmt = $this->pdo->prepare("
            UPDATE jobs
            SET
                last_run = :last_run,
                last_status = :last_status,
                running = 0
            WHERE id = :job_id
        ");

        $stmt->execute([
            ':last_run' => $data['finishedAt'],
            ':last_status' => $data['status'],
            ':job_id' => $data['jobId']
        ]);

        $this->pdo->commit();
    }
    catch (\Throwable $e)
    {
        $this->pdo->rollBack();
        throw $e;
    }
}

}