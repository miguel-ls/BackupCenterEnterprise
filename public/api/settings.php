<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Core\Audit;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$pdo = $db->getConnection();

/*==================================================
TABLA
==================================================*/

$pdo->exec("
CREATE TABLE IF NOT EXISTS settings(

    id INTEGER PRIMARY KEY CHECK(id=1),

    scheduler_interval INTEGER DEFAULT 60,

    max_threads INTEGER DEFAULT 4,

    retry_count INTEGER DEFAULT 3,

    retention_days INTEGER DEFAULT 30,

    compression INTEGER DEFAULT 1,

    log_level TEXT DEFAULT 'INFO',

    log_path TEXT DEFAULT 'resources/logs',

    connection_timeout INTEGER DEFAULT 30

)
");

/*==================================================
REGISTRO INICIAL
==================================================*/

$count=$pdo->query("
SELECT COUNT(*)
FROM settings
")->fetchColumn();

if($count==0){

    $pdo->exec("
    INSERT INTO settings(id)
    VALUES(1)
    ");

}

/*==================================================
GET
==================================================*/

if($_SERVER['REQUEST_METHOD']=="GET"){

    $stmt=$pdo->query("
    SELECT *
    FROM settings
    WHERE id=1
    ");

    echo json_encode([

        "success"=>true,

        "data"=>$stmt->fetch(PDO::FETCH_ASSOC)

    ]);

    exit;

}

/*==================================================
PUT
==================================================*/

if($_SERVER['REQUEST_METHOD']=="PUT"){

    $data=json_decode(
        file_get_contents("php://input"),
        true
    );

    $stmt=$pdo->prepare("

        UPDATE settings

        SET

            scheduler_interval=?,
            max_threads=?,
            retry_count=?,
            retention_days=?,
            compression=?,
            log_level=?,
            log_path=?,
            connection_timeout=?

        WHERE id=1

    ");

    $stmt->execute([

        $data["scheduler_interval"],
        $data["max_threads"],
        $data["retry_count"],
        $data["retention_days"],
        $data["compression"],
        $data["log_level"],
        $data["log_path"],
        $data["connection_timeout"]

    ]);

    Audit::info(

        "SETTINGS",

        "UPDATE",

        "Configuración modificada",

        "admin"

    );

    echo json_encode([

        "success"=>true,

        "message"=>"Configuración actualizada."

    ]);

    exit;

}

echo json_encode([

    "success"=>false,

    "message"=>"Método no permitido"

]);