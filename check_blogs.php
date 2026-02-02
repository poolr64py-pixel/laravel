<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use App\Models\Language;

echo "=== Blogs por idioma ===\n";
$languages = Language::all();

foreach ($languages as $lang) {
    $count = Blog::where('language_id', $lang->id)->count();
    echo "{$lang->name} ({$lang->code}): {$count} blogs\n";
}

echo "\n=== Total de blogs: " . Blog::count() . " ===\n";

echo "\n=== Últimos 5 blogs ===\n";
$blogs = Blog::orderBy('id', 'desc')->limit(5)->get(['id', 'language_id', 'title']);
foreach ($blogs as $blog) {
    $lang = Language::find($blog->language_id);
    echo "ID: {$blog->id} | Lang: " . ($lang ? $lang->code : 'N/A') . " | Title: {$blog->title}\n";
}
