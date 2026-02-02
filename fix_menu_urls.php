<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$menu = DB::table('menus')->first();
$menus = json_decode($menu->menus, true);

foreach ($menus as &$item) {
    if (strtolower($item['text']) === 'imoveis' && isset($item['children'])) {
        foreach ($item['children'] as &$child) {
            // Corrigir URLs - remover domínio completo, deixar só o path
            if (strpos($child['href'], 'https://www.terrasnoparaguay.com') === 0) {
                $child['href'] = str_replace('https://www.terrasnoparaguay.com', '', $child['href']);
            }
            // Garantir que começa com /
            if (!empty($child['href']) && $child['href'][0] !== '/' && $child['href'] !== '#') {
                $child['href'] = '/' . $child['href'];
            }
        }
    }
}

DB::table('menus')->where('id', $menu->id)->update(['menus' => json_encode($menus)]);
echo "✅ URLs corrigidas!\n";
