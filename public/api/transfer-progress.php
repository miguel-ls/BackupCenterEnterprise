<?php

require __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Application;

header('Content-Type: application/json');

try {

    $app = new Application();
    $db = $app->database();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON');
    }

    $jobId     = $input['job_id'] ?? null;
    $fileName  = $input['file_name'] ?? null;
    $progress  = $input['progress'] ?? 0;
    $bytesSent = $input['bytes_sent'] ?? 0;
    $totalBytes= $input['total_bytes'] ?? 0;
    $speed     = $input['speed'] ?? 0;

    if (!$jobId || !$fileName) {
        throw new Exception('Missing data');
    }

    $db->execute("
        INSERT INTO transfer_progress 
        (job_id, file_name, progress, bytes_sent, total_bytes, speed, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
        ON CONFLICT(job_id, file_name) DO UPDATE SET
            progress = excluded.progress,
            bytes_sent = excluded.bytes_sent,
            total_bytes = excluded.total_bytes,
            speed = excluded.speed,
            updated_at = datetime('now')
    ", [
        $jobId,
        $fileName,
        $progress,
        $bytesSent,
        $totalBytes,
        $speed
    ]);

    echo json_encode([
        'status' => 'ok'
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