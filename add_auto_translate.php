<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Adicionar use statement no topo
if (strpos($content, 'use App\Services\TranslationService;') === false) {
    $content = str_replace(
        'use Validator;',
        "use Validator;\nuse App\Services\TranslationService;",
        $content
    );
}

// Encontrar o final do método store() e adicionar tradução automática
$storeMethod = <<<'STORE_END'
        $blog = new Blog;
        $blog->create($input);
        
        // AUTO-TRADUÇÃO: Criar versões em outros idiomas
        try {
            $this->autoTranslateBlog($blog);
        } catch (\Exception $e) {
            \Log::error('Auto-translation failed: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.blog.index', ['language' => $request->language])->with('success', __('Added successfully!'));
STORE_END;

$content = preg_replace(
    '/\$blog = new Blog;\s+\$blog->create\(\$input\);\s+return redirect/',
    $storeMethod . "\n        return redirect",
    $content
);

// Adicionar método autoTranslateBlog no final da classe (antes do último })
$autoTranslateMethod = <<<'METHOD_END'

    /**
     * Traduzir blog automaticamente para outros idiomas
     */
    protected function autoTranslateBlog($sourceBlog)
    {
        $translator = new TranslationService();
        
        // Pegar idioma do blog original
        $sourceLang = Language::find($sourceBlog->language_id);
        
        if (!$sourceLang || $sourceLang->code !== 'pt') {
            return; // Só traduz se o original for em português
        }
        
        // Idiomas de destino
        $targetLangs = ['en', 'es'];
        
        foreach ($targetLangs as $langCode) {
            $targetLang = Language::where('code', $langCode)->first();
            
            if (!$targetLang) {
                continue;
            }
            
            // Verificar se já existe tradução
            $exists = Blog::where('language_id', $targetLang->id)
                ->where('slug', $sourceBlog->slug . '-' . $langCode)
                ->exists();
                
            if ($exists) {
                continue;
            }
            
            // Traduzir conteúdo
            $translated = $translator->translateBlog($sourceBlog, $langCode);
            
            // Criar novo blog traduzido
            $newBlog = new Blog();
            $newBlog->language_id = $targetLang->id;
            $newBlog->bcategory_id = $sourceBlog->bcategory_id;
            $newBlog->title = $translated['title'];
            $newBlog->slug = $sourceBlog->slug . '-' . $langCode;
            $newBlog->content = $translated['content'];
            $newBlog->main_image = $sourceBlog->main_image;
            $newBlog->serial_number = $sourceBlog->serial_number;
            $newBlog->meta_keywords = $translated['meta_keywords'];
            $newBlog->meta_description = $translated['meta_description'];
            $newBlog->save();
            
            \Log::info("Blog #{$sourceBlog->id} auto-translated to {$langCode} (#{$newBlog->id})");
        }
    }
METHOD_END;

// Adicionar antes do último }
$content = preg_replace(
    '/\n}\s*$/',
    $autoTranslateMethod . "\n}",
    $content
);

file_put_contents($file, $content);

echo "✅ Auto-tradução integrada no BlogController!\n";
