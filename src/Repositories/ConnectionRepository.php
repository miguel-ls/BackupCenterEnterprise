<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class ConnectionRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function initialize(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS connections
            (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                host TEXT NOT NULL,
                port INTEGER NOT NULL DEFAULT 22,
                username TEXT NOT NULL,
                password TEXT NOT NULL,
                hostkey TEXT,
                protocol TEXT NOT NULL DEFAULT 'SFTP',
                client_id INTEGER,
                remote_path TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
SELECT
    c.*,
    cl.business_name AS client_name
FROM connections c
LEFT JOIN clients cl
    ON cl.id = c.client_id
ORDER BY c.name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM connections
            WHERE id=?
        ");

        $stmt->execute([$id]);

        $connection = $stmt->fetch(PDO::FETCH_ASSOC);

        return $connection ?: null;
    }

    public function create(
            int $clientId,
            string $name,
            string $host,
            int $port,
            string $username,
            string $password,
            string $hostkey,
            string $protocol,
            string $remotePath
    ): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO connections
            (
            client_id,
            name,
            host,
            port,
            username,
            password,
            hostkey,
            protocol,
            remote_path
            )
            VALUES
            (
                ?,?,?,?,?,?,?,?,?
            )
        ");

        $stmt->execute([
            $clientId,
            $name,
            $host,
            $port,
            $username,
            $password,
            $hostkey,
            $protocol,
            $remotePath
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(
        int $id,
        int $clientId,
        string $name,
        string $host,
        int $port,
        string $username,
        string $password,
        string $hostkey,
        string $protocol,
        string $remotePath
    ): bool
    {
        $stmt = $this->db->prepare("
            UPDATE connections
            SET
                client_id=?,
                name=?,
                host=?,
                port=?,
                username=?,
                password=?,
                hostkey=?,
                protocol=?,
                remote_path=?
            WHERE id=?
        ");

        return $stmt->execute([
            $clientId,
            $name,
            $host,
            $port,
            $username,
            $password,
            $hostkey,
            $protocol,
            $remotePath,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE
            FROM connections
            WHERE id=?
        ");

        return $stmt->execute([$id]);
    }
}