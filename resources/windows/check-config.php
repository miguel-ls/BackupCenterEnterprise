<?php

$configPath = __DIR__ . '/config.json';

if (!file_exists($configPath)) {
    echo "config.json no encontrado\n";
    exit(1);
}

$config = json_decode(file_get_contents($configPath), true);
if (!is_array($config)) {
    echo "config.json inválido\n";
    exit(1);
}

echo "Config OK\n";
echo "server=" . ($config['server'] ?? '') . "\n";
echo "connectionId=" . ($config['connectionId'] ?? '') . "\n";
echo "installToken=" . ($config['installToken'] ?? '') . "\n";
