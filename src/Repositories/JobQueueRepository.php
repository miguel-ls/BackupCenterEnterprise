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
            created_at TEXT,
            started_at TEXT,
            finished_at TEXT,
            worker TEXT,
            attempts INTEGER DEFAULT 0,
            last_error TEXT
        );
        ");
    }

    public function enqueue(int $jobId): bool
    {
        $jobStmt = $this->db->prepare("
            SELECT enabled
            FROM jobs
            WHERE id=?
        ");

        $jobStmt->execute([$jobId]);
        $job = $jobStmt->fetch(PDO::FETCH_ASSOC);

        if (!$job || (int)($job['enabled'] ?? 0) !== 1) {
            return false;
        }

        if ($this->existsPendingOrRunning($jobId)) {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO job_queue
            (
                job_id,
                status,
                created_at,
                attempts
            )
            VALUES
            (
                ?,
                'Pending',
                ?,
                0
            )
        ");

        return $stmt->execute([
            $jobId,
            date('Y-m-d H:i:s')
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

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function start(int $id, string $worker): void
    {
        $stmt = $this->db->prepare("
            UPDATE job_queue
            SET
                status='Running',
                started_at=?,
                worker=?
            WHERE id=?
        ");

        $stmt->execute([
            date('Y-m-d H:i:s'),
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
                finished_at=?
            WHERE id=?
        ");

        $stmt->execute([
            date('Y-m-d H:i:s'),
            $id
        ]);
    }

    public function fail(
        int $id,
        string $error
    ): void
    {
        $stmt = $this->db->prepare("
            UPDATE job_queue
            SET
                status='Failed',
                finished_at=?,
                attempts=attempts+1,
                last_error=?
            WHERE id=?
        ");

        $stmt->execute([
            date('Y-m-d H:i:s'),
            $error,
            $id
        ]);
    }

    public function existsPendingOrRunning(int $jobId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM job_queue
            WHERE job_id=?
            AND status IN
            (
                'Pending',
                'Running'
            )
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
            LIMIT 50
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPending(): int
    {
        return (int)$this->db->query("
            SELECT COUNT(*)
            FROM job_queue
            WHERE status='Pending'
        ")->fetchColumn();
    }

    public function countRunning(): int
    {
        return (int)$this->db->query("
            SELECT COUNT(*)
            FROM job_queue
            WHERE status='Running'
        ")->fetchColumn();
    }

    public function countFailed(): int
    {
        return (int)$this->db->query("
            SELECT COUNT(*)
            FROM job_queue
            WHERE status='Failed'
        ")->fetchColumn();
    }

    public function countCompleted(): int
    {
        return (int)$this->db->query("
            SELECT COUNT(*)
            FROM job_queue
            WHERE status='Completed'
        ")->fetchColumn();
    }
}