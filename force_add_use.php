<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Procurar pelo namespace
$lines = explode("\n", $content);
$newLines = [];
$added = false;

foreach ($lines as $line) {
    $newLines[] = $line;
    
    // Adicionar logo após "use Validator;"
    if (strpos($line, 'use Validator;') !== false && !$added) {
        $newLines[] = 'use App\Services\TranslationService;';
        $added = true;
        echo "✅ Use statement adicionado após 'use Validator;'\n";
    }
}

if (!$added) {
    echo "❌ Não encontrou 'use Validator;'\n";
    echo "Vamos procurar outro lugar...\n";
    
    // Tentar adicionar após o namespace
    $newLines = [];
    foreach ($lines as $line) {
        $newLines[] = $line;
        if (strpos($line, 'namespace App\Http\Controllers\Admin;') !== false) {
            $newLines[] = '';
            $newLines[] = 'use App\Services\TranslationService;';
            $added = true;
            echo "✅ Use statement adicionado após namespace\n";
        }
    }
}

file_put_contents($file, implode("\n", $newLines));
