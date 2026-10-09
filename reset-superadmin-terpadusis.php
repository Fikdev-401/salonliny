<?php

$token = 'resetpw6f3c8f7f5c4c4cb2b8a93422';

if (!hash_equals($token, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found.');
}

header('Content-Type: text/plain; charset=UTF-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_time_limit(120);

$basePath = dirname(__DIR__);
$newPassword = 'Terpadu2026!';

echo "Reset Superadmin Terpadusis\n";
echo "===========================\n\n";

try {
    require $basePath . '/vendor/autoload.php';

    $app = require $basePath . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    $user = App\Models\User::query()
        ->where('username', 'superadmin')
        ->first();

    if (!$user) {
        throw new RuntimeException('User superadmin tidak ditemukan.');
    }

    $user->forceFill([
        'password' => Illuminate\Support\Facades\Hash::make($newPassword),
        'status' => 'aktif',
        'must_change_password' => false,
    ])->save();

    echo "Password superadmin berhasil direset.\n\n";
    echo "Username: superadmin\n";
    echo "Password: {$newPassword}\n\n";
    echo "PENTING: hapus file reset ini dari public_html setelah berhasil login.\n";
} catch (Throwable $exception) {
    http_response_code(500);
    echo "ERROR:\n";
    echo $exception->getMessage() . "\n";
    echo $exception->getFile() . ':' . $exception->getLine() . "\n";
}
