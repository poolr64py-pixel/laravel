<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Pegar menu atual
$menu = DB::table('menus')->first();

if (!$menu) {
    echo "❌ Menu não encontrado\n";
    exit;
}

// Decodificar JSON
$menus = json_decode($menu->menus, true);

// Encontrar o item "imoveis" e adicionar children
foreach ($menus as &$item) {
    if (strtolower($item['text']) === 'imoveis') {
        $item['children'] = [
            [
                'text' => 'Todos os Imóveis',
                'href' => 'https://www.terrasnoparaguay.com/imoveis',
                'icon' => 'empty',
                'target' => '_self',
                'title' => '',
                'type' => 'custom'
            ],
            [
                'text' => '─────────────',
                'href' => '#',
                'icon' => 'empty',
                'target' => '_self',
                'title' => '',
                'type' => 'custom'
            ],
            [
                'text' => 'Casas em Asunción',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/casas-asuncion',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Casas à venda em Asunción',
                'type' => 'custom'
            ],
            [
                'text' => 'Apartamentos em Asunción',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/apartamentos-asuncion',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Apartamentos em Asunción',
                'type' => 'custom'
            ],
            [
                'text' => 'Terrenos em Asunción',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/terrenos-asuncion',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Terrenos em Asunción',
                'type' => 'custom'
            ],
            [
                'text' => '─────────────',
                'href' => '#',
                'icon' => 'empty',
                'target' => '_self',
                'title' => '',
                'type' => 'custom'
            ],
            [
                'text' => 'Casas em Luque',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/casas-luque',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Casas à venda em Luque',
                'type' => 'custom'
            ],
            [
                'text' => 'Terrenos em Luque',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/terrenos-luque',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Terrenos em Luque',
                'type' => 'custom'
            ],
            [
                'text' => 'Quintas em Luque',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/quintas-luque',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Quintas em Luque',
                'type' => 'custom'
            ],
            [
                'text' => '─────────────',
                'href' => '#',
                'icon' => 'empty',
                'target' => '_self',
                'title' => '',
                'type' => 'custom'
            ],
            [
                'text' => 'Casas em San Bernardino',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/casas-san-bernardino',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Casas em San Bernardino',
                'type' => 'custom'
            ],
            [
                'text' => 'Quintas em San Bernardino',
                'href' => 'https://www.terrasnoparaguay.com/imoveis/quintas-san-bernardino',
                'icon' => 'empty',
                'target' => '_self',
                'title' => 'Quintas em San Bernardino',
                'type' => 'custom'
            ],
        ];
        
        echo "✅ Submenu adicionado ao item 'imoveis'\n";
        break;
    }
}

// Salvar de volta
DB::table('menus')
    ->where('id', $menu->id)
    ->update(['menus' => json_encode($menus)]);

echo "✅ Menu atualizado no banco de dados!\n";
echo "\nRecarregue o site e veja o dropdown de Imóveis!\n";
