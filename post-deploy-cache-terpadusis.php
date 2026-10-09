<?php

$deployToken = 'postcache5de4f9ad2c6446a0b0f413a8';

if (!hash_equals($deployToken, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found.');
}

header('Content-Type: text/plain; charset=UTF-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_time_limit(300);

$basePath = dirname(__DIR__);
$publicStorage = __DIR__ . '/storage';
$targetStorage = $basePath . '/storage/app/public';

echo "Terpadusis Post Deploy Runner\n";
echo "============================\n\n";
echo "Base path: {$basePath}\n\n";

try {
    require $basePath . '/vendor/autoload.php';

    $app = require $basePath . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
} catch (Throwable $exception) {
    http_response_code(500);
    echo "ERROR saat bootstrap Laravel:\n";
    echo $exception->getMessage() . "\n";
    echo $exception->getFile() . ':' . $exception->getLine() . "\n";
    exit;
}

echo "Storage public link check:\n";
if (is_link($publicStorage) || file_exists($publicStorage)) {
    echo "[OK] public_html/storage sudah ada.\n\n";
} elseif (function_exists('symlink')) {
    try {
        symlink($targetStorage, $publicStorage);
        echo "[OK] symlink public_html/storage berhasil dibuat via symlink().\n\n";
    } catch (Throwable $exception) {
        echo "[WARN] symlink gagal: {$exception->getMessage()}\n";
        echo "Upload/foto tetap bisa jalan, tapi file publik mungkin belum bisa diakses sampai link dibuat manual.\n\n";
    }
} else {
    echo "[WARN] function symlink() tidak tersedia.\n";
    echo "Upload/foto tetap bisa jalan, tapi file publik mungkin belum bisa diakses sampai link dibuat manual.\n\n";
}

function runDeployCommand(string $command, array $parameters = []): void
{
    echo ">>> php artisan {$command}\n";

    try {
        $exitCode = Illuminate\Support\Facades\Artisan::call($command, $parameters);
        echo Illuminate\Support\Facades\Artisan::output();
        echo "Exit code: {$exitCode}\n\n";

        if ($exitCode !== 0) {
            throw new RuntimeException("Command {$command} gagal dengan exit code {$exitCode}.");
        }
    } catch (Throwable $exception) {
        http_response_code(500);
        echo "ERROR saat menjalankan {$command}:\n";
        echo $exception->getMessage() . "\n";
        echo $exception->getFile() . ':' . $exception->getLine() . "\n";
        exit;
    }
}

runDeployCommand('config:cache');
runDeployCommand('route:cache');
runDeployCommand('view:cache');

echo "Selesai.\n";
echo "Hapus file ini dan deploy runner lama dari public_html.\n";
