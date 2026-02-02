<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Remover a rota SEO do final
$content = preg_replace('/\/\/ Rotas SEO para categorias de imóveis.*?property\.seo\.category\'\);/s', '', $content);

// Encontrar a linha da rota antiga e adicionar a SEO ANTES
$oldRoute = "Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')->name('front.property.detail');";

$newRoutes = <<<'ROUTES'
// Rotas SEO para categorias de imóveis (PRIORIDADE ALTA - vem antes)
Route::get('/imoveis/{slug}', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])
    ->where('slug', 'casas-.*|apartamentos-.*|terrenos-.*|quintas-.*')
    ->name('property.seo.category');

// Rota genérica de detalhes (PRIORIDADE BAIXA - vem depois)
Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')->name('front.property.detail');
ROUTES;

$content = str_replace($oldRoute, $newRoutes, $content);

file_put_contents($file, $content);

echo "✅ Rotas reorganizadas!\n";
