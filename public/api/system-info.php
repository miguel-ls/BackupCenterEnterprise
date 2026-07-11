<?php

use BackupCenter\Core\ApiController;
use BackupCenter\Core\ApiResponse;

require_once __DIR__ . '/bootstrap.php';

ApiController::boot();

ApiController::method('GET');

$data = [

    'hostname' => gethostname(),

    'php_version' => PHP_VERSION,

    'os' => PHP_OS_FAMILY,

    'time' => date('Y-m-d H:i:s'),

    'memory_usage' => round(memory_get_usage(true)/1024/1024,2),

    'memory_peak' => round(memory_get_peak_usage(true)/1024/1024,2),

    'disk_free' => round(disk_free_space(dirname(__DIR__,2))/1024/1024/1024,2),

    'disk_total' => round(disk_total_space(dirname(__DIR__,2))/1024/1024/1024,2)

];

ApiResponse::success($data);