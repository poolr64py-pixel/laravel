<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Language;

$pt = Language::where('code', 'pt')->first();
$en = Language::where('code', 'en')->first();
$es = Language::where('code', 'es')->first();

// Pegar os 3 blogs mais recentes em PT
$ptBlogs = Blog::where('language_id', $pt->id)
    ->orderBy('id', 'desc')
    ->limit(3)
    ->get();

echo "=== Criando traduções faltantes ===\n\n";

foreach ($ptBlogs as $blog) {
    echo "Blog PT: {$blog->title}\n";
    
    // Criar versão em inglês
    $enBlog = $blog->replicate();
    $enBlog->language_id = $en->id;
    $enBlog->slug = $blog->slug . '-en';
    $enBlog->save();
    echo "  ✅ Criado em inglês (ID: {$enBlog->id})\n";
    
    // Criar versão em espanhol
    $esBlog = $blog->replicate();
    $esBlog->language_id = $es->id;
    $esBlog->slug = $blog->slug . '-es';
    $esBlog->save();
    echo "  ✅ Criado em espanhol (ID: {$esBlog->id})\n";
    
    echo "\n";
}

echo "✅ Traduções criadas com sucesso!\n";
echo "\n⚠️  IMPORTANTE: Os blogs foram duplicados com conteúdo em PORTUGUÊS.\n";
echo "Você precisa editar cada um para traduzir:\n";
echo "  - Título\n";
echo "  - Conteúdo\n";
echo "  - Meta keywords/description\n";
