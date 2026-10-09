<?php

$token = 'fixvite3c64a9f2d7e14b90';

if (!hash_equals($token, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found.');
}

header('Content-Type: text/plain; charset=UTF-8');

$basePath = dirname(__DIR__);
$artisan = $basePath . '/artisan';

echo "Terpadusis Vite Cache Fix\n";
echo "=========================\n\n";
echo "Base path: {$basePath}\n\n";

if (!is_file($artisan)) {
    echo "[ERROR] artisan tidak ditemukan.\n";
    exit;
}

chdir($basePath);

$commands = [
    'php artisan view:clear',
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
];

foreach ($commands as $command) {
    echo ">>> {$command}\n";
    $output = [];
    $exitCode = 0;
    exec($command . ' 2>&1', $output, $exitCode);
    echo implode("\n", $output) . "\n";
    echo "Exit code: {$exitCode}\n\n";
}

echo "Selesai. Hapus file public_html/fix-vite-cache-terpadusis.php setelah berhasil.\n";
