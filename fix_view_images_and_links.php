<?php

$file = 'resources/views/front/property-seo-category.blade.php';
$content = file_get_contents($file);

// Corrigir caminho da imagem
$content = str_replace(
    "asset('assets/img/properties/' . \$property->featured_image)",
    "asset('assets/img/property/featureds/' . \$property->featured_image)",
    $content
);

// Corrigir link dos detalhes - usar o slug do content
$oldLink = "route('front.property.details', ['slug' => \$content->slug, 'id' => \$property->id])";
$newLink = "route('front.property.detail', \$content->slug)";

$content = str_replace($oldLink, $newLink, $content);

// Também corrigir se tiver sem o ID
$content = str_replace(
    "route('front.property.detail', ['slug' => \$content->slug, 'id' => \$property->id])",
    "route('front.property.detail', \$content->slug)",
    $content
);

file_put_contents($file, $content);

echo "✅ View corrigida!\n";
