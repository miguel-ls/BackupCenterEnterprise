<?php

require __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Application;

header('Content-Type: application/json');

try {

    $app = new Application();
    $db  = $app->database();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON');
    }

    // ✅ CAMPOS CORRECTOS (como envía el agent)
    $jobId        = $input['job_id'] ?? null;
    $fileName     = $input['file_name'] ?? null;
    $uploaded     = $input['uploaded_bytes'] ?? 0;
    $total        = $input['total_bytes'] ?? 0;
    $speed        = $input['speed'] ?? 0;
    $status       = $input['status'] ?? 'uploading';

    if (!$jobId || !$fileName) {
        throw new Exception('Missing data');
    }

    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("
        INSERT INTO transfer_progress (
            job_id,
            file_name,
            uploaded_bytes,
            total_bytes,
            speed,
            status,
            updated_at
        )
        VALUES (
            :job_id,
            :file_name,
            :uploaded,
            :total,
            :speed,
            :status,
            datetime('now')
        )
        ON CONFLICT(job_id, file_name)
        DO UPDATE SET
            uploaded_bytes = excluded.uploaded_bytes,
            total_bytes    = excluded.total_bytes,
            speed          = excluded.speed,
            status         = excluded.status,
            updated_at     = datetime('now')
    ");

    $stmt->execute([
        ':job_id'   => $jobId,
        ':file_name'=> $fileName,
        ':uploaded' => $uploaded,
        ':total'    => $total,
        ':speed'    => $speed,
        ':status'   => $status
    ]);

    echo json_encode([
        'success' => true
    ]);

} catch (Throwable $e) {

    file_put_contents(
        __DIR__ . '/../../storage/logs/transfer-error.log',
        date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL,
        FILE_APPEND
    );

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}