<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$lang_id = 179;

echo "=== TESTE DE BUSCA ===\n\n";

// 1. Total de propriedades
$total = \App\Models\User\Property\Property::where('user_id', 148)
    ->where('status', 1)
    ->count();
echo "1. Total de propriedades: $total\n\n";

// 2. Com contents filtrados
$properties = \App\Models\User\Property\Property::where('user_id', 148)
    ->where('status', 1)
    ->with(['contents' => function($q) use ($lang_id) {
        $q->where('language_id', $lang_id);
    }])
    ->get();

echo "2. Propriedades com contents (lang $lang_id): " . $properties->count() . "\n";
foreach($properties as $p) {
    $contentsCount = $p->contents->count();
    echo "   ID: {$p->id} | Contents: $contentsCount";
    if($contentsCount > 0) {
        echo " | Título: " . $p->contents->first()->title;
    }
    echo "\n";
}

// 3. Busca
echo "\n3. Teste de busca:\n";
$search = "High-End";
$found = \App\Models\User\Property\Property::where('user_id', 148)
    ->where('status', 1)
    ->whereHas('contents', function($q) use ($search, $lang_id) {
        $q->where('language_id', $lang_id)
          ->where('title', 'like', "%{$search}%");
    })
    ->with(['contents' => function($q) use ($lang_id) {
        $q->where('language_id', $lang_id);
    }])
    ->get();

echo "   Busca por '$search': " . $found->count() . " resultado(s)\n";
foreach($found as $p) {
    if($p->contents->count() > 0) {
        echo "   - " . $p->contents->first()->title . "\n";
    }
}
