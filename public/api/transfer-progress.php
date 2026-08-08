<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/Core/Database.php';

header('Content-Type: application/json');

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$jobId = $data['job_id'] ?? 0;
$fileName = $data['file_name'] ?? '';

$uploaded = $data['uploaded_bytes'] ?? 0;
$total = $data['total_bytes'] ?? 0;

$speed = $data['speed'] ?? 0;
$status = $data['status'] ?? 'uploading';


/*
|--------------------------------------------------------------------------
| Validación básica
|--------------------------------------------------------------------------
*/

if (!$jobId || !$fileName) {

    http_response_code(400);

    echo json_encode([
        "error" => "Invalid data"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Normalización de valores
|--------------------------------------------------------------------------
*/

$jobId = (int) $jobId;

$uploaded = max(
    0,
    (int) $uploaded
);

$total = max(
    0,
    (int) $total
);

$speed = max(
    0,
    (float) $speed
);


/*
|--------------------------------------------------------------------------
| Protección:
| uploaded_bytes nunca puede superar total_bytes
|--------------------------------------------------------------------------
*/

if ($total > 0) {

    $uploaded = min(
        $uploaded,
        $total
    );

}


/*
|--------------------------------------------------------------------------
| Estados permitidos
|--------------------------------------------------------------------------
*/

$allowedStatuses = [
    'uploading',
    'completed',
    'failed'
];


if (!in_array(
    $status,
    $allowedStatuses,
    true
)) {

    $status = 'uploading';

}


/*
|--------------------------------------------------------------------------
| Si está completado, debe estar al 100%
|--------------------------------------------------------------------------
*/

if ($status === 'completed' && $total > 0) {

    $uploaded = $total;

}


try {

    $db = \BackupCenter\Core\Database::getInstance();


    $db->execute(
        "
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
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            datetime('now')
        )

        ON CONFLICT(job_id, file_name)
        DO UPDATE SET

            uploaded_bytes =
                excluded.uploaded_bytes,

            total_bytes =
                excluded.total_bytes,

            speed =
                excluded.speed,

            status =
                excluded.status,

            updated_at =
                excluded.updated_at
        ",
        [
            $jobId,
            $fileName,
            $total,
            $uploaded,
            $speed,
            $status
        ]
    );


    echo json_encode([
        "ok" => true
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "error" => $e->getMessage()
    ]);

}