<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

$deployToken = '__DEPLOY_TOKEN__';

if (!hash_equals($deployToken, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found.');
}

ini_set('display_errors', '1');
error_reporting(E_ALL);
set_time_limit(300);

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';

header('Content-Type: text/plain; charset=UTF-8');

echo "Terpadusis Hostinger Web Deploy Runner\n";
echo "=====================================\n\n";

if (!file_exists($envPath)) {
    http_response_code(500);
    echo "ERROR: File .env belum ada.\n";
    echo "Rename .env.terpadusis.example menjadi .env, lalu isi database terlebih dahulu.\n";
    exit;
}

require $basePath . '/vendor/autoload.php';

$app = require $basePath . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function runArtisan(string $command, array $parameters = []): void
{
    echo ">>> php artisan {$command}\n";

    try {
        $exitCode = Artisan::call($command, $parameters);
        echo Artisan::output();
        echo "Exit code: {$exitCode}\n\n";

        if ($exitCode !== 0) {
            throw new RuntimeException("Command {$command} gagal dengan exit code {$exitCode}.");
        }
    } catch (Throwable $exception) {
        http_response_code(500);
        echo "ERROR: {$exception->getMessage()}\n";
        exit;
    }
}

runArtisan('key:generate', ['--force' => true]);
runArtisan('migrate', ['--force' => true]);
runArtisan('db:seed', ['--class' => 'ProductionSeeder', '--force' => true]);
runArtisan('storage:link');
runArtisan('config:cache');
runArtisan('route:cache');
runArtisan('view:cache');

echo "Selesai.\n";
echo "PENTING: hapus file deploy runner ini dari public_html setelah deploy sukses.\n";
