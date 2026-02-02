<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Encontrar o método update() e adicionar tradução automática
$updatePattern = '/(\$blog->update\(\$input\);)\s+(Session::flash\(\'success\',)/';

$updateReplacement = '$1
        
        // AUTO-TRADUÇÃO: Atualizar traduções existentes
        try {
            $this->updateTranslations($blog);
        } catch (\Exception $e) {
            \Log::error(\'Auto-translation update failed: \' . $e->getMessage());
        }
        
        $2';

$content = preg_replace($updatePattern, $updateReplacement, $content);

// Adicionar método updateTranslations
$updateTranslationsMethod = <<<'METHOD_END'

    /**
     * Atualizar traduções existentes quando o blog original é editado
     */
    protected function updateTranslations($sourceBlog)
    {
        $translator = new TranslationService();
        
        // Pegar idioma do blog original
        $sourceLang = Language::find($sourceBlog->language_id);
        
        if (!$sourceLang || $sourceLang->code !== 'pt') {
            return; // Só atualiza traduções se o original for em português
        }
        
        // Idiomas de destino
        $targetLangs = ['en', 'es'];
        
        foreach ($targetLangs as $langCode) {
            $targetLang = Language::where('code', $langCode)->first();
            
            if (!$targetLang) {
                continue;
            }
            
            // Buscar tradução existente pelo slug base
            $slugBase = preg_replace('/-pt$/', '', $sourceBlog->slug);
            $translatedBlog = Blog::where('language_id', $targetLang->id)
                ->where('slug', 'like', $slugBase . '%')
                ->first();
            
            if (!$translatedBlog) {
                // Se não existe, criar nova tradução
                $translated = $translator->translateBlog($sourceBlog, $langCode);
                
                $newBlog = new Blog();
                $newBlog->language_id = $targetLang->id;
                $newBlog->bcategory_id = $sourceBlog->bcategory_id;
                $newBlog->title = $translated['title'];
                $newBlog->slug = $slugBase . '-' . $langCode;
                $newBlog->content = $translated['content'];
                $newBlog->main_image = $sourceBlog->main_image;
                $newBlog->serial_number = $sourceBlog->serial_number;
                $newBlog->meta_keywords = $translated['meta_keywords'];
                $newBlog->meta_description = $translated['meta_description'];
                $newBlog->save();
                
                \Log::info("Created translation for blog #{$sourceBlog->id} in {$langCode} (#{$newBlog->id})");
            } else {
                // Atualizar tradução existente
                $translated = $translator->translateBlog($sourceBlog, $langCode);
                
                $translatedBlog->title = $translated['title'];
                $translatedBlog->content = $translated['content'];
                $translatedBlog->bcategory_id = $sourceBlog->bcategory_id;
                $translatedBlog->main_image = $sourceBlog->main_image;
                $translatedBlog->serial_number = $sourceBlog->serial_number;
                $translatedBlog->meta_keywords = $translated['meta_keywords'];
                $translatedBlog->meta_description = $translated['meta_description'];
                $translatedBlog->save();
                
                \Log::info("Updated translation for blog #{$sourceBlog->id} in {$langCode} (#{$translatedBlog->id})");
            }
        }
    }
METHOD_END;

// Adicionar antes do último }
if (strpos($content, 'updateTranslations') === false) {
    $content = preg_replace(
        '/\n}\s*$/',
        $updateTranslationsMethod . "\n}",
        $content
    );
}

file_put_contents($file, $content);

echo "✅ Auto-tradução adicionada no UPDATE!\n";
