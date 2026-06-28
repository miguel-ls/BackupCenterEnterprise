<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class UploadedFileRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function initialize(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS uploaded_files
            (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                filename TEXT NOT NULL,
                filesize INTEGER NOT NULL,
                sha256 TEXT NOT NULL,
                uploaded_at TEXT NOT NULL
            );
        ");
    }

    public function exists(string $sha256): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM uploaded_files
            WHERE sha256 = ?
        ");

        $stmt->execute([$sha256]);

        return (int)$stmt->fetchColumn() > 0;
    }    

    public function save(
        string $filename,
        int $filesize,
        string $sha256
    ): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO uploaded_files
            (
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
                datetime('now')
            )
        ");

        $stmt->execute([
            $filename,
            $filesize,
            $sha256
        ]);
    }    

    public function calculateSha256(string $file): string
    {
        return hash_file('sha256', $file);
    }    
}