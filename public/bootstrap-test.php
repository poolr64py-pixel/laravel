<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "1. Autoload...<br>";
require __DIR__.'/../vendor/autoload.php';

echo "2. Bootstrap app...<br>";
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "3. Make kernel...<br>";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "4. Capture request...<br>";
$request = Illuminate\Http\Request::capture();

echo "5. Handle request...<br>";
try {
    $response = $kernel->handle($request);
    echo "6. ✓ Request processado!<br>";
    $response->send();
} catch (\Throwable $e) {
    echo "<pre>";
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
