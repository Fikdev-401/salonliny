<?php

$token = 'logview7b9f4a1d63c24e8a';

if (!hash_equals($token, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found.');
}

header('Content-Type: text/plain; charset=UTF-8');

$basePath = dirname(__DIR__);
$logDir = $basePath . '/storage/logs';
$logFile = $logDir . '/laravel.log';

echo "Terpadusis Laravel Log Viewer\n";
echo "=============================\n\n";
echo "Base path: {$basePath}\n";
echo "Log path : {$logFile}\n\n";

if (!is_dir($logDir)) {
    echo "[ERROR] storage/logs tidak ditemukan.\n";
    exit;
}

echo "Log files:\n";
$files = glob($logDir . '/*') ?: [];
if ($files === []) {
    echo "- tidak ada file log\n";
}

foreach ($files as $file) {
    echo '- ' . basename($file) . ' (' . filesize($file) . " bytes)\n";
}

if (!is_file($logFile)) {
    echo "\n[ERROR] laravel.log tidak ditemukan.\n";
    exit;
}

$lines = file($logFile, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    echo "\n[ERROR] laravel.log tidak bisa dibaca.\n";
    exit;
}

echo "\n--- 250 baris terakhir laravel.log ---\n";
echo implode("\n", array_slice($lines, -250));
echo "\n";
