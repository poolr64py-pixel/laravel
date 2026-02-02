<?php

$file = 'public/assets/front/js/script.js';
$content = file_get_contents($file);

// Remover todos os blocos de código de menu que adicionamos
$patterns = [
    // Remover código jQuery de menu
    '/\$\(document\)\.ready\(function\(\$\)\s*\{[^}]*navbar-nav.*?\}\);/s',
    '/jQuery\(document\)\.ready\(function\(\$\)\s*\{[^}]*menu.*?\}\);/s',
    '/\/\*.*?menu.*?\*\/.*?\}\)\(jQuery\);/si',
    '/\/\/.*?menu.*?\n.*?\}\);/s',
    
    // Remover fechamentos extras
    '/\}\s*\}\);[\s\n]*\}\);/s',
];

foreach ($patterns as $pattern) {
    $content = preg_replace($pattern, '', $content);
}

// Remover linhas vazias múltiplas
$content = preg_replace('/\n{3,}/', "\n\n", $content);

file_put_contents($file, $content);

echo "✅ script.js limpo!\n";
