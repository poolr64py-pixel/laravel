<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Language;

echo "==========================================\n";
echo "   LISTA COMPLETA DE BLOGS POR IDIOMA\n";
echo "==========================================\n\n";

$languages = Language::all();

foreach ($languages as $lang) {
    $blogs = Blog::where('language_id', $lang->id)
                 ->orderBy('id', 'desc')
                 ->get(['id', 'title', 'serial_number']);
    
    echo "========== {$lang->name} ({$lang->code}) - {$blogs->count()} blogs ==========\n";
    
    foreach ($blogs as $blog) {
        echo "  ID: {$blog->id} | S/N: {$blog->serial_number} | {$blog->title}\n";
    }
    echo "\n";
}

echo "Total geral: " . Blog::count() . " blogs\n";
