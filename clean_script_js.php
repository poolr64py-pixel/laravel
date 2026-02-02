<?php

$file = 'public/assets/front/js/script.js';
$content = file_get_contents($file);

// Remover TODOS os blocos relacionados a menu/dropdown
$patterns = [
    '/\/\* ===.*?FIX.*?MENU.*?===.*?\*\/.*?\}\)\(jQuery\);/s',
    '/\/\/ Fix para dropdown mobile.*?\}\);/s',
    '/\/\*.*?MENU.*?DROPDOWN.*?\*\/.*?\}\)\(jQuery\);/s',
    '/jQuery\(document\)\.ready\(function\(\$\) \{.*?navbar-nav.*?\}\);/s',
];

foreach ($patterns as $pattern) {
    $content = preg_replace($pattern, '', $content);
}

file_put_contents($file, $content);
echo "✅ Scripts antigos removidos!\n";
