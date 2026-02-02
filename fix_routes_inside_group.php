<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Remover as rotas SEO de onde estão agora (fora do grupo)
$content = preg_replace(
    '/\/\/ Rotas SEO para categorias.*?property\.seo\.category\'\);.*?\/\/ Rota genérica de detalhes.*?front\.property\.detail\'\);/s',
    '',
    $content
);

// Encontrar onde está Route::get('/imoveis', ... dentro do grupo
// e adicionar as rotas SEO ANTES dela
$searchFor = "Route::get('/imoveis', 'Front\FrontendController@allProperties')->name('front.properties');";

$replacement = "// Rotas SEO para categorias de imóveis (PRIORIDADE ALTA - vem antes)
        Route::get('/imoveis/{slug}', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])
            ->where('slug', 'casas-.*|apartamentos-.*|terrenos-.*|quintas-.*')
            ->name('property.seo.category');
        
        // Rota genérica de listagem
        Route::get('/imoveis', 'Front\FrontendController@allProperties')->name('front.properties');
        
        // Rota genérica de detalhes (PRIORIDADE BAIXA - vem depois)
        Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')->name('front.property.detail');";

$content = str_replace($searchFor, $replacement, $content);

file_put_contents($file, $content);

echo "✅ Rotas movidas para dentro do grupo!\n";
