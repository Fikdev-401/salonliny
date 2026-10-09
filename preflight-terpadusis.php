<?php
if (!hash_equals('preflightdd3421159311434b94c5a009', (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found.');
}
header('Content-Type: text/plain; charset=UTF-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$base = dirname(__DIR__);
$checks = [
    'PHP version >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'base path exists' => is_dir($base),
    '../vendor/autoload.php exists' => file_exists($base . '/vendor/autoload.php'),
    '../artisan exists' => file_exists($base . '/artisan'),
    '../bootstrap/app.php exists' => file_exists($base . '/bootstrap/app.php'),
    '../.env exists' => file_exists($base . '/.env'),
    '../storage writable' => is_writable($base . '/storage'),
    '../bootstrap/cache writable' => is_writable($base . '/bootstrap/cache'),
];

$extensions = ['ctype','dom','fileinfo','filter','gd','json','mbstring','openssl','pdo','pdo_mysql','session','tokenizer','xml','zip'];

echo "Terpadusis Preflight Check\n";
echo "==========================\n";
echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '-') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? '-') . "\n";
echo "Expected Laravel base: " . $base . "\n\n";

foreach ($checks as $label => $ok) {
    echo ($ok ? '[OK]   ' : '[FAIL] ') . $label . "\n";
}

echo "\nExtensions:\n";
foreach ($extensions as $extension) {
    echo (extension_loaded($extension) ? '[OK]   ' : '[FAIL] ') . $extension . "\n";
}

echo "\nIf any path check fails, extract the deploy zip in the folder ABOVE public_html.\n";
echo "Delete this file after debugging.\n";
