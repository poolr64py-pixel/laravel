<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Language;

// Encontrar blogs com language_id NULL
$blogsWithoutLang = Blog::whereNull('language_id')->get();

echo "=== Blogs sem idioma definido ===\n";
foreach ($blogsWithoutLang as $blog) {
    echo "ID: {$blog->id} | Title: {$blog->title}\n";
}

if ($blogsWithoutLang->count() > 0) {
    // Pegar idioma português
    $pt = Language::where('code', 'pt')->first();
    
    if ($pt) {
        Blog::whereNull('language_id')->update(['language_id' => $pt->id]);
        echo "\n✅ {$blogsWithoutLang->count()} blog(s) atualizado(s) para português!\n";
    }
} else {
    echo "\n✅ Nenhum blog sem idioma encontrado!\n";
}
