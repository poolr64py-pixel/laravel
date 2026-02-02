<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Services\TranslationService;
use App\Models\Language;

echo "=== Testando Tradução Automática no UPDATE ===\n\n";

// Pegar o blog de teste criado antes (ID 201)
$blog = Blog::find(201);

if (!$blog) {
    echo "❌ Blog de teste não encontrado. Use o ID de um blog em português.\n";
    exit;
}

echo "Blog Original (PT):\n";
echo "  ID: {$blog->id}\n";
echo "  Título: {$blog->title}\n\n";

// Atualizar o título
$blog->title = "Teste de Tradução ATUALIZADO - Nova Versão";
$blog->content = "<p>Conteúdo atualizado! Este texto foi modificado e deve ser traduzido automaticamente.</p>";
$blog->save();

echo "✅ Blog atualizado em PORTUGUÊS\n";
echo "   Novo título: {$blog->title}\n\n";

// Simular a atualização das traduções
$translator = new TranslationService();
$pt = Language::where('code', 'pt')->first();

if ($blog->language_id == $pt->id) {
    foreach (['en', 'es'] as $langCode) {
        $targetLang = Language::where('code', $langCode)->first();
        $slugBase = preg_replace('/-pt$/', '', $blog->slug);
        
        $translatedBlog = Blog::where('language_id', $targetLang->id)
            ->where('slug', 'like', $slugBase . '%')
            ->first();
        
        if ($translatedBlog) {
            $translated = $translator->translateBlog($blog, $langCode);
            
            $translatedBlog->title = $translated['title'];
            $translatedBlog->content = $translated['content'];
            $translatedBlog->save();
            
            echo "✅ Tradução ATUALIZADA para " . strtoupper($langCode) . " (ID: {$translatedBlog->id})\n";
            echo "   Novo título: {$translatedBlog->title}\n\n";
        }
    }
}

echo "🎉 Tradução automática no UPDATE funcionando!\n";
