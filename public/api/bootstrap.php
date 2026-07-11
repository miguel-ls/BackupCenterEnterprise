<?php

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
*/

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if($_SERVER["REQUEST_METHOD"]==="OPTIONS"){

    http_response_code(200);

    exit;

}

/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/

session_name("BACKUPCENTER");

session_set_cookie_params([

    "lifetime"=>0,

    "path"=>"/",

    "httponly"=>true,

    "samesite"=>"Lax"

]);

if(session_status()===PHP_SESSION_NONE){

    session_start();

}

/*
|--------------------------------------------------------------------------
| AUTOLOAD
|--------------------------------------------------------------------------
*/

require_once __DIR__.'/../../vendor/autoload.php';

use BackupCenter\Core\Application;

/*
|--------------------------------------------------------------------------
| APPLICATION
|--------------------------------------------------------------------------
*/

$app = new Application();

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

$db = $app->database();

$pdo = $db->getConnection();