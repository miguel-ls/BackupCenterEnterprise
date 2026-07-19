<?php

namespace BackupCenter\Repositories;

use BackupCenter\Core\Database;
use PDO;

class ReportRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    /**
     * Resumen Ejecutivo
     */
    public function summary(): array
    {
        return [

            "jobs" => (int)$this->db->query("
                SELECT COUNT(*)
                FROM jobs
            ")->fetchColumn(),

            "connections" => (int)$this->db->query("
                SELECT COUNT(*)
                FROM connections
            ")->fetchColumn(),

            "uploaded_files" => (int)$this->db->query("
                SELECT COUNT(*)
                FROM uploaded_files
            ")->fetchColumn(),

            "executions" => (int)$this->db->query("
                SELECT COUNT(*)
                FROM execution_history
            ")->fetchColumn(),

            "errors" => (int)$this->db->query("
                SELECT COUNT(*)
                FROM execution_history
                WHERE errors>0
            ")->fetchColumn()

        ];
    }

    /**
     * Backups por día
     */
    public function daily(int $days = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT

                DATE(executed_at) day,

                COUNT(*) executions,

                SUM(files_uploaded) uploaded,

                SUM(errors) errors

            FROM execution_history

            GROUP BY DATE(executed_at)

            ORDER BY DATE(executed_at) DESC

            LIMIT :days
        ");

        $stmt->bindValue(
            ":days",
            $days,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Clientes
     */
    public function clients(): array
    {
        $stmt = $this->db->query("
            SELECT

                client,

                COUNT(*) executions,

                SUM(files_uploaded) uploaded,

                SUM(errors) errors

            FROM execution_history

            GROUP BY client

            ORDER BY executions DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trabajos
     */
    public function jobs(): array
    {
        $stmt = $this->db->query("
            SELECT

                j.name,

                COALESCE(
                    (
                        SELECT CASE e.status
                            WHEN 'OK' THEN 'Correcto'
                            WHEN 'ERROR' THEN 'Error'
                            ELSE e.status
                        END
                        FROM execution_history e
                        WHERE e.client = j.name
                        ORDER BY e.id DESC
                        LIMIT 1
                    ),
                    j.last_status,
                    'Pendiente'
                ) AS last_status,

                COALESCE(
                    (
                        SELECT e.executed_at
                        FROM execution_history e
                        WHERE e.client = j.name
                        ORDER BY e.id DESC
                        LIMIT 1
                    ),
                    j.last_run,
                    '-'
                ) AS last_run

            FROM jobs j

            ORDER BY id
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ejecuciones que registraron errores.
     */
    public function errors(): array
    {
        $stmt = $this->db->query("
            SELECT
                executed_at,
                client,
                files_found,
                files_uploaded,
                errors,
                duration,
                status
            FROM execution_history
            WHERE errors > 0
               OR status = 'ERROR'
            ORDER BY id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Conexiones
     */
    public function connections(): array
    {
        $stmt = $this->db->query("
            SELECT

                name,

                protocol,

                host,

                remote_path

            FROM connections

            ORDER BY name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
