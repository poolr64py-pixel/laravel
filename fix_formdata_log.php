<?php

$file = 'resources/views/admin/blog/blog/edit.blade.php';
$content = file_get_contents($file);

// Procurar onde cria o FormData
$pattern = '/const formData = new FormData\(form\);/';

$replacement = 'const formData = new FormData(form);
        
        // DEBUG: Mostrar todos os dados sendo enviados
        console.log("=== DADOS DO FORMULÁRIO ===");
        for (let pair of formData.entries()) {
            console.log(pair[0] + " = " + pair[1]);
        }
        console.log("=========================");';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "✅ Debug FormData adicionado!\n";
