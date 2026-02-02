<?php
$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Comentar a chamada de tradução
$content = str_replace(
    '$this->updateTranslations($blog);',
    '// $this->updateTranslations($blog); // TEMPORARIAMENTE DESABILITADO',
    $content
);

file_put_contents($file, $content);
echo "✅ Tradução desabilitada temporariamente\n";
