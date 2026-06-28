<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use BackupCenter\Models\ExecutionSummary;

class ExecutionHistoryRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

public function save(ExecutionSummary $summary, string $client): void
{
    $sql = "
    INSERT INTO execution_history
    (
        executed_at,
        client,
        files_found,
        files_uploaded,
        files_skipped,
        errors,
        duration,
        status
    )
    VALUES
    (
        :executed_at,
        :client,
        :files_found,
        :files_uploaded,
        :files_skipped,
        :errors,
        :duration,
        :status
    )
    ";

    $stmt = $this->database
        ->getConnection()
        ->prepare($sql);

    $stmt->execute([
        ':executed_at'    => date('Y-m-d H:i:s'),
        ':client'         => $client,
        ':files_found'    => $summary->found,
        ':files_uploaded' => $summary->uploaded,
        ':files_skipped'  => $summary->skipped,
        ':errors'         => $summary->errors,
        ':duration'       => $summary->getDuration(),
        ':status'         => $summary->isSuccess() ? 'OK' : 'ERROR'
    ]);
}

public function latest(int $limit = 10): array
{
    $stmt = $this->database
        ->getConnection()
        ->prepare("
            SELECT
                id,
                executed_at,
                client,
                files_found,
                files_uploaded,
                files_skipped,
                errors,
                duration,
                status
            FROM execution_history
            ORDER BY id DESC
            LIMIT :limit
        ");

    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function statistics(): array
{
    $sql = "
        SELECT

            COUNT(*) total_runs,

            SUM(
                CASE
                    WHEN status='OK'
                    THEN 1
                    ELSE 0
                END
            ) success_runs,

            SUM(
                CASE
                    WHEN status='ERROR'
                    THEN 1
                    ELSE 0
                END
            ) error_runs,

            SUM(files_uploaded) uploaded,

            SUM(files_skipped) skipped,

            ROUND(AVG(duration),2) average_duration

        FROM execution_history
    ";

    $stmt = $this->database
        ->getConnection()
        ->query($sql);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

}