<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class SystemLogRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function initialize(): void
{
    $this->db->exec("
        CREATE TABLE IF NOT EXISTS system_logs
        (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL,
            level TEXT NOT NULL,
            module TEXT NOT NULL,
            action TEXT,
            message TEXT NOT NULL,
            context TEXT,
            username TEXT,
            client_id INTEGER,
            connection_id INTEGER,
            job_id INTEGER
        )
    ");
}
}