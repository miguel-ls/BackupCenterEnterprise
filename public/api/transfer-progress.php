<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;

header('Content-Type: application/json');

$data = json_decode(
    file_get_contents('php://input'),
    true
);

$jobId    = $data['job_id'] ?? 0;
$fileName = $data['file_name'] ?? '';
$uploaded = $data['uploaded_bytes'] ?? 0;
$total    = $data['total_bytes'] ?? 0;
$status   = $data['status'] ?? 'uploading';

if (!$jobId || !$fileName) {
    http_response_code(400);

    echo json_encode([
        'error' => 'Invalid data'
    ]);

    exit;
}

try {

    $databaseFile = Paths::database() . '/backupcenter.db';

    $database = new Database($databaseFile);

    $pdo = $database->getConnection();

    $sql = "
        INSERT INTO transfer_progress
        (
            job_id,
            file_name,
            total_bytes,
            uploaded_bytes,
            speed,
            status,
            updated_at
        )
        VALUES
        (
            :job_id,
            :file_name,
            :total_bytes,
            :uploaded_bytes,
            :speed,
            :status,
            datetime('now')
        )
        ON CONFLICT(job_id, file_name)
        DO UPDATE SET
            total_bytes = excluded.total_bytes,
            uploaded_bytes = excluded.uploaded_bytes,
            speed = excluded.speed,
            status = excluded.status,
            updated_at = excluded.updated_at
    ";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ':job_id'         => $jobId,
        ':file_name'      => $fileName,
        ':total_bytes'    => $total,
        ':uploaded_bytes' => $uploaded,
        ':speed'          => 0,
        ':status'         => $status
    ]);

    echo json_encode([
        'ok' => true
    ]);

} catch (\Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}