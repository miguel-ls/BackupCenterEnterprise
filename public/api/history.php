<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;


$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$pdo = $db->getConnection();

$page = max(
    1,
    (int)($_GET['page'] ?? 1)
);

$limit = (int)($_GET['limit'] ?? 10);

$clientId = (int)($_GET['client_id'] ?? 0);

/*
|--------------------------------------------------------------------------
| Solo permitimos estos tamaños
|--------------------------------------------------------------------------
*/

$allowedLimits = [5,10,20,50,100];

if (!in_array($limit, $allowedLimits, true)) {

    $limit = 5;

}

$offset = ($page - 1) * $limit;

if ($clientId > 0) {

    $stmtTotal = $pdo->prepare("
        SELECT COUNT(*)

        FROM execution_history eh

        INNER JOIN jobs j
            ON j.id = eh.job_id

        INNER JOIN connections cn
            ON cn.id = j.connection_id

        INNER JOIN clients c
            ON c.id = cn.client_id

        WHERE c.id = ?
    ");

    $stmtTotal->execute([$clientId]);

    $total = (int)$stmtTotal->fetchColumn();

} else {

    $total = (int)$pdo
        ->query("
            SELECT COUNT(*)
            FROM execution_history
        ")
        ->fetchColumn();

}

$sql = "
SELECT

    eh.id,
    eh.started_at,
    eh.finished_at,

    j.name AS job_name,

    c.business_name AS client_name,

    eh.files_found,
    eh.files_uploaded,
    eh.files_skipped,
    eh.files_failed,
    eh.duration_seconds,
    eh.status

FROM execution_history eh

INNER JOIN jobs j
    ON j.id = eh.job_id

INNER JOIN connections cn
    ON cn.id = j.connection_id

INNER JOIN clients c
    ON c.id = cn.client_id
";

if ($clientId > 0) {

    $sql .= "
        WHERE c.id = ?
    ";

}

$sql .= "
ORDER BY eh.id DESC
LIMIT ?
OFFSET ?
";

$stmt = $pdo->prepare($sql);

if ($clientId > 0) {

$stmt->bindValue(
    1,
    $clientId,
    \PDO::PARAM_INT
);

$stmt->bindValue(
    2,
    $limit,
    \PDO::PARAM_INT
);

$stmt->bindValue(
    3,
    $offset,
    \PDO::PARAM_INT
);

} else {

    $stmt->bindValue(
        1,
        $limit,
        \PDO::PARAM_INT
    );

    $stmt->bindValue(
        2,
        $offset,
        \PDO::PARAM_INT
    );

}

$stmt->execute();

echo json_encode([

    'success' => true,

    'page' => $page,

    'limit' => $limit,

    'total' => $total,

    'pages' => (int)ceil(
        $total / $limit
    ),

    'data' => $stmt->fetchAll(
        \PDO::FETCH_ASSOC
    )

]);