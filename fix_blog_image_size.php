<?php

$file = 'public/assets/front/css/style.css';
$content = file_get_contents($file);

// Procurar e substituir o CSS da imagem do card
$old = '.template-area .card .card-image img {
  width: 100%;
  transition: transform 10s ease-out;
}';

$new = '.template-area .card .card-image img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  object-position: center;
  transition: transform 10s ease-out;
}';

$content = str_replace($old, $new, $content);

file_put_contents($file, $content);

echo "✅ CSS atualizado!\n";
echo "Agora todas as imagens terão 300px de altura e serão cortadas proporcionalmente.\n";
