<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 Iniciando duplicação e tradução de posts...\n\n";

// Idiomas alvo
$languages = [
    ['id' => 178, 'code' => 'es', 'name' => 'Espanhol'],
    ['id' => 176, 'code' => 'en', 'name' => 'Inglês']
];

// Buscar posts em português
$ptPosts = DB::table('blogs')
    ->where('language_id', 179)
    ->get();

echo "📊 Encontrados " . count($ptPosts) . " posts em português\n\n";

foreach ($ptPosts as $post) {
    echo "📝 Post: " . substr($post->title, 0, 50) . "...\n";
    
    foreach ($languages as $lang) {
        // Verificar se já existe tradução
        $exists = DB::table('blogs')
            ->where('slug', $post->slug . '-' . $lang['code'])
            ->where('language_id', $lang['id'])
            ->exists();
        
        if ($exists) {
            echo "   ⏭️  Já existe em {$lang['name']}\n";
            continue;
        }
        
        // Criar novo post
        $newPost = [
            'language_id' => $lang['id'],
            'bcategory_id' => $post->bcategory_id,
            'title' => $post->title . ' (' . $lang['code'] . ')',
            'slug' => $post->slug . '-' . $lang['code'],
            'main_image' => $post->main_image,
            'content' => $post->content,
            'meta_keywords' => $post->meta_keywords,
            'meta_description' => $post->meta_description,
            'serial_number' => $post->serial_number ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        DB::table('blogs')->insert($newPost);
        echo "   ✅ Criado em {$lang['name']}\n";
    }
    
    echo "\n";
}

echo "\n🎉 Concluído!\n";
