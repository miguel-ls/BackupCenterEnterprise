<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class NotificationRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function initialize(): void
    {
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS notifications
        (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            level TEXT,
            title TEXT,
            message TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            is_read INTEGER DEFAULT 0
        );
        ");
    }

    public function add(
        string $level,
        string $title,
        string $message
    ): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications
            (
                level,
                title,
                message
            )
            VALUES
            (
                ?,?,?
            )
        ");

        $stmt->execute([
            $level,
            $title,
            $message
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM notifications
            ORDER BY id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}