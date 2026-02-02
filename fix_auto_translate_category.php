<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Corrigir o método autoTranslateBlog
$oldAutoTranslate = '            // Criar novo blog traduzido
            $newBlog = new Blog();
            $newBlog->language_id = $targetLang->id;
            $newBlog->bcategory_id = $sourceBlog->bcategory_id;';

$newAutoTranslate = '            // Criar novo blog traduzido
            // Pegar categoria equivalente no idioma de destino
            $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
            $targetCat = Bcategory::where("language_id", $targetLang->id)
                ->where("name", $sourceCat->name)
                ->first();
            
            if (!$targetCat) {
                // Se não existe, criar categoria no idioma de destino
                $targetCat = Bcategory::create([
                    "language_id" => $targetLang->id,
                    "name" => $sourceCat->name,
                    "status" => $sourceCat->status,
                    "serial_number" => $sourceCat->serial_number,
                ]);
            }
            
            $newBlog = new Blog();
            $newBlog->language_id = $targetLang->id;
            $newBlog->bcategory_id = $targetCat->id;';

$content = str_replace($oldAutoTranslate, $newAutoTranslate, $content);

// Corrigir o método updateTranslations (parte 1 - criação)
$oldUpdate1 = '                $newBlog = new Blog();
                $newBlog->language_id = $targetLang->id;
                $newBlog->bcategory_id = $sourceBlog->bcategory_id;';

$newUpdate1 = '                // Pegar categoria equivalente no idioma de destino
                $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
                $targetCat = Bcategory::where("language_id", $targetLang->id)
                    ->where("name", $sourceCat->name)
                    ->first();
                
                if (!$targetCat) {
                    // Se não existe, criar categoria no idioma de destino
                    $targetCat = Bcategory::create([
                        "language_id" => $targetLang->id,
                        "name" => $sourceCat->name,
                        "status" => $sourceCat->status,
                        "serial_number" => $sourceCat->serial_number,
                    ]);
                }
                
                $newBlog = new Blog();
                $newBlog->language_id = $targetLang->id;
                $newBlog->bcategory_id = $targetCat->id;';

$content = str_replace($oldUpdate1, $newUpdate1, $content);

// Corrigir o método updateTranslations (parte 2 - atualização)
$oldUpdate2 = '                $translatedBlog->title = $translated[\'title\'];
                $translatedBlog->content = $translated[\'content\'];
                $translatedBlog->bcategory_id = $sourceBlog->bcategory_id;';

$newUpdate2 = '                // Pegar categoria equivalente no idioma de destino
                $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
                $targetCat = Bcategory::where("language_id", $targetLang->id)
                    ->where("name", $sourceCat->name)
                    ->first();
                
                if (!$targetCat) {
                    $targetCat = Bcategory::create([
                        "language_id" => $targetLang->id,
                        "name" => $sourceCat->name,
                        "status" => $sourceCat->status,
                        "serial_number" => $sourceCat->serial_number,
                    ]);
                }
                
                $translatedBlog->title = $translated[\'title\'];
                $translatedBlog->content = $translated[\'content\'];
                $translatedBlog->bcategory_id = $targetCat->id;';

$content = str_replace($oldUpdate2, $newUpdate2, $content);

file_put_contents($file, $content);

echo "✅ Tradução automática corrigida para usar categorias corretas!\n";
