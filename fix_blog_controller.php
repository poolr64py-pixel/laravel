<?php
// Script para corrigir o bug do upload de imagem

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Substituir o código bugado pelo correto
$bugado = '$ext = strtolower($img->getClientOriginalExtension());';
$correto = '$ext = strtolower($request->file(\'image\')->getClientOriginalExtension());';

$content = str_replace($bugado, $correto, $content);

// Também substituir onde usa $img para pegar a extensão na primeira linha
$bugado2 = '$filename = time() . \'.\' . $img->getClientOriginalExtension();';
$correto2 = '$img = $request->file(\'image\');
            $filename = time() . \'.\' . $img->getClientOriginalExtension();';

$content = str_replace($bugado2, $correto2, $content);

// Melhorar a detecção do tipo de imagem usando o conteúdo do arquivo
$old_code = '            $ext = strtolower($request->file(\'image\')->getClientOriginalExtension());
            if ($ext == "jpg" || $ext == "jpeg") {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($ext == "png") {
                $image = imagecreatefrompng($sourcePath);
            }';

$new_code = '            // Detectar tipo real do arquivo (não confiar apenas na extensão)
            $imageInfo = getimagesize($sourcePath);
            $mimeType = $imageInfo[\'mime\'] ?? null;
            
            if ($mimeType == \'image/jpeg\') {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($mimeType == \'image/png\') {
                $image = imagecreatefrompng($sourcePath);
            } elseif ($mimeType == \'image/gif\') {
                $image = imagecreatefromgif($sourcePath);
            }';

$content = str_replace($old_code, $new_code, $content);

file_put_contents($file, $content);
echo "✅ BlogController.php corrigido!\n";
