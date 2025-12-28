<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Host: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "Config website_host: " . config('app.website_host') . "<br>";

$host = str_replace("www.", "", $_SERVER['HTTP_HOST']);
echo "Host sem www: " . $host . "<br>";

if ($host === config('app.website_host')) {
    echo "<strong>Detectado: SITE PRINCIPAL</strong><br>";
} else {
    echo "<strong>Detectado: SUBDOMÍNIO</strong><br>";
    
    $hostArr = explode('.', $host);
    $username = $hostArr[0];
    echo "Username extraído: " . $username . "<br>";
    
    $user = \App\Models\User::where('username', $username)->first();
    if ($user) {
        echo "✓ USUÁRIO ENCONTRADO: " . $user->username . " (ID: " . $user->id . ")<br>";
    } else {
        echo "✗ USUÁRIO NÃO ENCONTRADO NO BANCO<br>";
    }
}
