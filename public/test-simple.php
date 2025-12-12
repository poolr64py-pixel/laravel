<?php
echo "PHP funcionando!<br>";
echo "Host: " . ($_SERVER['HTTP_HOST'] ?? 'não definido') . "<br>";

try {
    require __DIR__.'/../vendor/autoload.php';
    echo "✓ Autoload OK<br>";
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✓ App OK<br>";
    
    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
    echo "✓ Kernel OK<br>";
    
    $kernel->bootstrap();
    echo "✓ Bootstrap OK<br>";
    
    echo "Config website_host: " . config('app.website_host') . "<br>";
    
} catch (\Throwable $e) {
    echo "<pre>ERRO: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "</pre>";
}
