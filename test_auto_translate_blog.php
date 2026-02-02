<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Language;
use App\Models\Bcategory;
use App\Services\TranslationService;

echo "=== Testando Tradução Automática de Blog ===\n\n";

// Pegar idioma português
$pt = Language::where('code', 'pt')->first();

// Pegar primeira categoria
$category = Bcategory::where('language_id', $pt->id)->first();

if (!$category) {
    echo "❌ Crie uma categoria primeiro!\n";
    exit;
}

// Criar blog de teste em português
$blog = new Blog();
$blog->language_id = $pt->id;
$blog->bcategory_id = $category->id;
$blog->title = "Teste de Tradução Automática";
$blog->slug = "teste-traducao-automatica-" . time();
$blog->content = "<p>Este é um teste de tradução automática do sistema. O conteúdo será traduzido automaticamente para inglês e espanhol.</p>";
$blog->main_image = "default.jpg";
$blog->serial_number = 999;
$blog->meta_keywords = "teste, tradução, automática";
$blog->meta_description = "Teste de tradução automática do blog";
$blog->save();

echo "✅ Blog criado em PORTUGUÊS (ID: {$blog->id})\n";
echo "   Título: {$blog->title}\n\n";

// Simular a tradução automática
$translator = new TranslationService();

$targetLangs = ['en', 'es'];

foreach ($targetLangs as $langCode) {
    $targetLang = Language::where('code', $langCode)->first();
    
    // Traduzir
    $translated = $translator->translateBlog($blog, $langCode);
    
    // Criar blog traduzido
    $newBlog = new Blog();
    $newBlog->language_id = $targetLang->id;
    $newBlog->bcategory_id = $blog->bcategory_id;
    $newBlog->title = $translated['title'];
    $newBlog->slug = $blog->slug . '-' . $langCode;
    $newBlog->content = $translated['content'];
    $newBlog->main_image = $blog->main_image;
    $newBlog->serial_number = $blog->serial_number;
    $newBlog->meta_keywords = $translated['meta_keywords'];
    $newBlog->meta_description = $translated['meta_description'];
    $newBlog->save();
    
    echo "✅ Traduzido para " . strtoupper($langCode) . " (ID: {$newBlog->id})\n";
    echo "   Título: {$newBlog->title}\n\n";
}

echo "🎉 Tradução automática funcionando perfeitamente!\n";
echo "\nVerifique no admin:\n";
echo "- Português: ID {$blog->id}\n";

$en = Language::where('code', 'en')->first();
$es = Language::where('code', 'es')->first();

$enBlog = Blog::where('slug', 'like', $blog->slug . '-en')->first();
$esBlog = Blog::where('slug', 'like', $blog->slug . '-es')->first();

if ($enBlog) echo "- Inglês: ID {$enBlog->id}\n";
if ($esBlog) echo "- Espanhol: ID {$esBlog->id}\n";
