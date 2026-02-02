<?php

$file = 'resources/views/front/layout.blade.php';
$content = file_get_contents($file);

// Remover se já existe
$content = preg_replace('/.*mobile-menu-fix\.js.*\n?/', '', $content);

// Adicionar DEPOIS do script.js
$content = str_replace(
    "asset('assets/front/js/script.js')",
    "asset('assets/front/js/script.js') }}\"></script>\n    <script src=\"{{ asset('assets/front/js/mobile-menu-fix.js')",
    $content
);

file_put_contents($file, $content);
echo "✅ Script reposicionado!\n";
