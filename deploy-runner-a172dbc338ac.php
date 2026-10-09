<?php

$deployToken = 'a172dbc338ac4d60b5903b690b5a6544d862fdcc8da14507';

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
$envPath = $basePath . '/.env';

echo "Terpadusis Hostinger Web Deploy Runner\n";
echo "=====================================\n\n";
echo "Base path: {$basePath}\n";
echo "PHP: " . PHP_VERSION . "\n\n";

if (!file_exists($envPath)) {
    http_response_code(500);
    echo "ERROR: File .env belum ada.\n";
    echo "Rename .env.terpadusis.example menjadi .env, lalu isi database terlebih dahulu.\n";
    exit;
}

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

runDeployCommand('key:generate', ['--force' => true]);
runDeployCommand('migrate', ['--force' => true]);
runDeployCommand('db:seed', ['--class' => 'ProductionSeeder', '--force' => true]);
runDeployCommand('storage:link');
runDeployCommand('config:cache');
runDeployCommand('route:cache');
runDeployCommand('view:cache');

echo "Selesai.\n";
echo "PENTING: hapus file deploy runner ini dari public_html setelah deploy sukses.\n";
