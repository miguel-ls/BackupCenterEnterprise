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

$clientName = null;

if ($clientId > 0) {

    $stmtClient = $pdo->prepare("
        SELECT business_name
        FROM clients
        WHERE id = ?
    ");

    $stmtClient->execute([$clientId]);

    $clientName = $stmtClient->fetchColumn();

}

if ($clientName) {

    $stmtTotal = $pdo->prepare("
        SELECT COUNT(*)
        FROM execution_history
        WHERE client = ?
    ");

    $stmtTotal->execute([$clientName]);

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
        id,
        executed_at,
        client,
        files_found,
        files_uploaded,
        files_skipped,
        errors,
        duration,
        status
    FROM execution_history
";

if ($clientName) {

    $sql .= " WHERE client = ? ";

}

$sql .= "
    ORDER BY id DESC
    LIMIT ?
    OFFSET ?
";

$stmt = $pdo->prepare($sql);

if ($clientName) {

    $stmt->bindValue(
        1,
        $clientName,
        \PDO::PARAM_STR
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