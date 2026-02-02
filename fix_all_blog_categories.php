<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Bcategory;
use App\Models\Language;

echo "=== Corrigindo categorias de blogs ===\n\n";

$en = Language::where('code', 'en')->first();
$es = Language::where('code', 'es')->first();
$pt = Language::where('code', 'pt')->first();

// Pegar categoria padrão para cada idioma
$catEN = Bcategory::where('language_id', $en->id)->first();
$catES = Bcategory::where('language_id', $es->id)->first();

// Corrigir blogs em inglês
$blogsEN = Blog::where('language_id', $en->id)->get();
foreach ($blogsEN as $blog) {
    $cat = Bcategory::find($blog->bcategory_id);
    if (!$cat || $cat->language_id != $en->id) {
        $blog->bcategory_id = $catEN->id;
        $blog->save();
        echo "✅ Blog EN #{$blog->id} corrigido\n";
    }
}

// Corrigir blogs em espanhol
$blogsES = Blog::where('language_id', $es->id)->get();
foreach ($blogsES as $blog) {
    $cat = Bcategory::find($blog->bcategory_id);
    if (!$cat || $cat->language_id != $es->id) {
        $blog->bcategory_id = $catES->id;
        $blog->save();
        echo "✅ Blog ES #{$blog->id} corrigido\n";
    }
}

echo "\n✅ Todas as categorias foram corrigidas!\n";
