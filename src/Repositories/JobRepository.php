<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class JobRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function initialize(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS jobs
            (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                source TEXT,
                destination TEXT,
                schedule TEXT,
                enabled INTEGER DEFAULT 1,
                last_run TEXT,
                last_status TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function getJobs(): array
    {
        $stmt = $this->db->query("
SELECT
    id,
    name,
    source,
    destination,
    schedule,
    enabled,
    COALESCE(last_run, '-') AS time,
    COALESCE(last_status, 'Pendiente') AS status
FROM jobs
ORDER BY id
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function createJob(
    string $name,
    string $source,
    string $destination,
    string $schedule
): int
{
    $stmt = $this->db->prepare("
        INSERT INTO jobs
        (
            name,
            source,
            destination,
            schedule,
            enabled,
            last_status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            1,
            'Pendiente'
        )
    ");

    $stmt->execute([
        $name,
        $source,
        $destination,
        $schedule
    ]);

    return (int)$this->db->lastInsertId();
}

public function deleteJob(int $id): bool
{
    $stmt = $this->db->prepare("
        DELETE FROM jobs
        WHERE id = ?
    ");

    return $stmt->execute([$id]);
}

public function getJob(int $id): ?array
{
    $stmt = $this->db->prepare("
        SELECT
            *
        FROM jobs
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    return $job ?: null;
}

public function updateJob(
    int $id,
    string $name,
    string $source,
    string $destination,
    string $schedule
): bool
{
    $stmt = $this->db->prepare("
        UPDATE jobs
        SET
            name = ?,
            source = ?,
            destination = ?,
            schedule = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $name,
        $source,
        $destination,
        $schedule,
        $id
    ]);
}

}