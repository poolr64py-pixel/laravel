<?php

$file = 'public/assets/front/css/style.css';
$content = file_get_contents($file);

// Aumentar a altura da imagem para 400px
$content = preg_replace(
    '/(\.template-area \.card \.card-image img \{[^}]*height:\s*)300px/s',
    '${1}400px',
    $content
);

// Adicionar altura mínima no card-image também
$old = '.template-area .card .card-image img {';
$new = '.template-area .card .card-image {
  height: 400px;
  overflow: hidden;
}

.template-area .card .card-image img {';

if (strpos($content, '.template-area .card .card-image {') === false) {
    $content = str_replace($old, $new, $content);
}

file_put_contents($file, $content);

echo "✅ CSS atualizado com altura de 400px!\n";
