<?php
$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

$content = str_replace(
    '// $this->updateTranslations($blog); // TEMPORARIAMENTE DESABILITADO',
    '$this->updateTranslations($blog);',
    $content
);

file_put_contents($file, $content);
echo "✅ Tradução reabilitada\n";
