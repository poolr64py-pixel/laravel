<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Bcategory;

try {
    echo "=== Testando update de blog ===\n\n";
    
    $blog = Blog::find(119); // Um blog em português
    
    if (!$blog) {
        echo "❌ Blog não encontrado\n";
        exit;
    }
    
    echo "Blog atual:\n";
    echo "  ID: {$blog->id}\n";
    echo "  Título: {$blog->title}\n";
    echo "  Language ID: {$blog->language_id}\n";
    echo "  Category ID: {$blog->bcategory_id}\n\n";
    
    // Tentar atualizar
    $blog->title = "Teste de Atualização " . date('H:i:s');
    $blog->save();
    
    echo "✅ Blog atualizado com sucesso!\n";
    
    // Agora testar a tradução automática
    echo "\n=== Testando tradução automática ===\n";
    
    $controller = new \App\Http\Controllers\Admin\BlogController();
    $method = new ReflectionMethod($controller, 'updateTranslations');
    $method->setAccessible(true);
    $method->invoke($controller, $blog);
    
    echo "✅ Tradução automática executada!\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
