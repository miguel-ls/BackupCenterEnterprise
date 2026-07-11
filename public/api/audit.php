<?php

use BackupCenter\Core\ApiController;
use BackupCenter\Core\ApiResponse;

require_once __DIR__ . '/bootstrap.php';

ApiController::boot();

ApiController::method(['GET','DELETE']);

if($_SERVER['REQUEST_METHOD']=="DELETE"){

    $pdo->exec("
        DELETE FROM audit_log
    ");

    ApiResponse::success(
        null,
        "Auditoría eliminada."
    );
}

$page=max(
    1,
    (int)($_GET["page"] ?? 1)
);

$limit=max(
    20,
    (int)($_GET["limit"] ?? 50)
);

$offset=($page-1)*$limit;

$where=[];

$params=[];

if(!empty($_GET["user"])){

    $where[]="username=?";

    $params[]=$_GET["user"];

}

if(!empty($_GET["module"])){

    $where[]="module=?";

    $params[]=$_GET["module"];

}

if(!empty($_GET["success"])){

    $where[]="success=?";

    $params[]=(int)$_GET["success"];

}

if(!empty($_GET["from"])){

    $where[]="date(created_at)>=date(?)";

    $params[]=$_GET["from"];

}

if(!empty($_GET["to"])){

    $where[]="date(created_at)<=date(?)";

    $params[]=$_GET["to"];

}

$sqlWhere="";

if(count($where)>0){

    $sqlWhere=" WHERE ".implode(" AND ",$where);

}

$stmt=$pdo->prepare("

SELECT

    id,

    created_at,

    username,

    module,

    action,

    description,

    ip,

    hostname,

    duration,

    metadata,

    success

FROM audit_log

$sqlWhere

ORDER BY id DESC

LIMIT ?

OFFSET ?

");

foreach($params as $k=>$v){

    $stmt->bindValue(

        $k+1,

        $v

    );

}

$stmt->bindValue(

    count($params)+1,

    $limit,

    PDO::PARAM_INT

);

$stmt->bindValue(

    count($params)+2,

    $offset,

    PDO::PARAM_INT

);

$stmt->execute();

$count=$pdo->prepare("

SELECT COUNT(*)

FROM audit_log

$sqlWhere

");

foreach($params as $k=>$v){

    $count->bindValue(

        $k+1,

        $v

    );

}

$count->execute();

$total=(int)$count->fetchColumn();

ApiResponse::success([

    "page"=>$page,

    "limit"=>$limit,

    "pages"=>ceil($total/$limit),

    "total"=>$total,

    "items"=>$stmt->fetchAll(PDO::FETCH_ASSOC)

]);