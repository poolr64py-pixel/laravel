<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TranslationService;

try {
    $translator = new TranslationService();
    
    echo "=== Testando DeepL API ===\n\n";
    
    $textoPT = "Olá! Este é um teste de tradução automática para o blog.";
    
    echo "Original (PT): $textoPT\n\n";
    
    $textoEN = $translator->translate($textoPT, 'en');
    echo "Inglês (EN): $textoEN\n\n";
    
    $textoES = $translator->translate($textoPT, 'es');
    echo "Espanhol (ES): $textoES\n\n";
    
    echo "✅ DeepL funcionando perfeitamente!\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
