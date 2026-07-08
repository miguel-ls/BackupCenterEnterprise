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

use BackupCenter\Core\Paths;

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(10, (int)($_GET['limit'] ?? 50));

$directory = Paths::logs();

$rows = [];

if (is_dir($directory)) {

    $files = glob($directory . '/*.log');

    rsort($files);

    foreach ($files as $file) {

        $lines = @file($file);

        if (!$lines) {
            continue;
        }

        foreach (array_reverse($lines) as $line) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $level = 'INFO';

            if (stripos($line, 'ERROR') !== false) {

                $level = 'ERROR';

            } elseif (stripos($line, 'WARNING') !== false) {

                $level = 'WARNING';

            }

            $rows[] = [

                'date' => date(
                    'Y-m-d H:i:s',
                    filemtime($file)
                ),

                'level' => $level,

                'message' => $line

            ];
        }
    }
}

$total = count($rows);

$pages = max(1, (int)ceil($total / $limit));

$offset = ($page - 1) * $limit;

$data = array_slice(
    $rows,
    $offset,
    $limit
);

echo json_encode([

    'success' => true,

    'page' => $page,

    'limit' => $limit,

    'total' => $total,

    'pages' => $pages,

    'data' => array_values($data)

]);