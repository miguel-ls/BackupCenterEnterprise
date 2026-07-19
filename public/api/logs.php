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

// Los registros se escriben con la hora de Lima. Mantener la misma zona
// horaria en esta respuesta evita que la fecha de respaldo aparezca en UTC.
date_default_timezone_set('America/Lima');

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(10, (int)($_GET['limit'] ?? 50));

$directory = Paths::logs();

$rows = [];

if (is_dir($directory)) {

    $files = glob($directory . '/*.log');

    rsort($files);

    foreach ($files as $file) {

        $lines = @file($file, FILE_IGNORE_NEW_LINES);

        if (!$lines) {
            continue;
        }

        $entries = [];
        $current = null;

        foreach ($lines as $line) {

            // Los mensajes de WinSCP pueden abarcar varias líneas. Solo la
            // primera tiene fecha, por lo que se agrupan como un solo evento.
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \[([A-Z]+)\] (.*)$/', $line, $matches)) {

                if ($current !== null) {
                    $entries[] = $current;
                }

                $current = [
                    'date' => $matches[1],
                    'level' => $matches[2],
                    'message' => $matches[0]
                ];

                continue;
            }

            if ($current !== null) {
                $current['message'] .= PHP_EOL . $line;
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        $rows = array_merge($rows, array_reverse($entries));
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
