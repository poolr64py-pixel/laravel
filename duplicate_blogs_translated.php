<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 Duplicando posts de português para outros idiomas...\n\n";

$ptPosts = [
    109, 111, 113, 115, 117, 119
];

$languages = [
    ['id' => 178, 'code' => 'es', 'name' => 'Español'],
    ['id' => 176, 'code' => 'en', 'name' => 'English']
];

foreach ($ptPosts as $postId) {
    $original = DB::table('blogs')->where('id', $postId)->first();
    
    if (!$original) {
        echo "❌ Post $postId não encontrado\n";
        continue;
    }
    
    echo "📝 Post: {$original->title}\n";
    
    foreach ($languages as $lang) {
        // Verificar se já existe
        $exists = DB::table('blogs')
            ->where('original_blog_id', $postId)
            ->where('language_id', $lang['id'])
            ->exists();
        
        if ($exists) {
            echo "   ⏭️  Já existe em {$lang['name']}\n";
            continue;
        }
        
        $newPost = [
            'original_blog_id' => $postId,
            'language_id' => $lang['id'],
            'bcategory_id' => $original->bcategory_id,
            'title' => $original->title,
            'slug' => $original->slug . '-' . $lang['code'],
            'main_image' => $original->main_image,
            'content' => $original->content,
            'tags' => $original->tags,
            'meta_keywords' => $original->meta_keywords,
            'meta_description' => $original->meta_description,
            'serial_number' => $original->serial_number,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        DB::table('blogs')->insert($newPost);
        echo "   ✅ Criado em {$lang['name']}\n";
    }
    echo "\n";
}

echo "🎉 Concluído! Agora você pode editar no admin para traduzir os títulos/conteúdos.\n";
