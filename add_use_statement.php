<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Verificar se já existe
if (strpos($content, 'use App\Services\TranslationService;') !== false) {
    echo "✅ Use statement já existe!\n";
    exit;
}

// Adicionar após os outros use statements
$pattern = '/use Validator;/';
$replacement = "use Validator;\nuse App\Services\TranslationService;";

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "✅ Use statement adicionado!\n";
