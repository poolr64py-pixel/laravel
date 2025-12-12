<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->bootstrap();

echo "1. Testando getUser()...<br>";
try {
    $user = getUser();
    if ($user) {
        echo "✓ User: " . $user->username . " (ID: " . $user->id . ")<br>";
    } else {
        echo "✗ getUser() retornou NULL<br>";
    }
} catch (\Exception $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
