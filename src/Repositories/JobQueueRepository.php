<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class JobQueueRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

public function initialize(): void
{
    $this->db->exec("
    CREATE TABLE IF NOT EXISTS job_queue
    (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        job_id INTEGER NOT NULL,
        status TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        started_at TEXT,
        finished_at TEXT,
        worker TEXT,
        attempts INTEGER DEFAULT 0,
        last_error TEXT
    );
    ");
}

public function enqueue(int $jobId): void
{
    if ($this->existsPendingOrRunning($jobId)) {

        echo "Job {$jobId} ya está en cola." . PHP_EOL;

        return;
    }

    $stmt = $this->db->prepare("
        INSERT INTO job_queue
        (
            job_id,
            status
        )
        VALUES
        (
            ?,
            'Pending'
        )
    ");

    $stmt->execute([
        $jobId
    ]);
}

public function getNext(): ?array
{
    $stmt = $this->db->query("
        SELECT *
        FROM job_queue
        WHERE status='Pending'
        ORDER BY id
        LIMIT 1
    ");

    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    return $job ?: null;
}

public function start(int $id,string $worker): void
{
    $stmt = $this->db->prepare("
        UPDATE job_queue
        SET
            status='Running',
            started_at=datetime('now'),
            worker=?
        WHERE id=?
    ");

    $stmt->execute([
        $worker,
        $id
    ]);
}

public function finish(int $id): void
{
    $stmt = $this->db->prepare("
        UPDATE job_queue
        SET
            status='Completed',
            finished_at=datetime('now')
        WHERE id=?
    ");

    $stmt->execute([
        $id
    ]);
}

public function incrementAttempts(
    int $id,
    string $error = ''
): void
{
    $stmt = $this->db->prepare("
        UPDATE job_queue
        SET
            attempts = attempts + 1,
            last_error = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $error,
        $id
    ]);
}

public function fail(
    int $id,
    string $error
): void
{
    $this->incrementAttempts(
        $id,
        $error
    );

    $stmt = $this->db->prepare("
        UPDATE job_queue
        SET
            status='Failed',
            finished_at=datetime('now')
        WHERE id=?
    ");

    $stmt->execute([
        $id
    ]);
}

public function existsPendingOrRunning(int $jobId): bool
{
    $stmt = $this->db->prepare("
        SELECT COUNT(*)
        FROM job_queue
        WHERE job_id = ?
        AND status IN ('Pending','Running')
    ");

    $stmt->execute([
        $jobId
    ]);

    return (int)$stmt->fetchColumn() > 0;
}

public function getAll(): array
{
    $stmt = $this->db->query("
        SELECT
            q.id,
            q.job_id,
            j.name,
            q.status,
            q.worker,
            q.attempts,
            q.created_at,
            q.started_at,
            q.finished_at,
            q.last_error
        FROM job_queue q

        INNER JOIN jobs j
            ON j.id=q.job_id

        ORDER BY q.id DESC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}