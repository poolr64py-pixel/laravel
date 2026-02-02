<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Encontrar a rota catch-all e adicionar ANTES dela
$catchAll = "// Custom Page middleware
Route::group(['middleware' => ['routeAccess:Custom Page']], function () {
    Route::get('/{slug}', 'Front\FrontendController@userCPage')
        ->name('front.user.cpage');
});";

$seoRoutes = "// === ROTAS SEO (DEVEM VIR ANTES DO CATCH-ALL) ===
Route::group(['middleware' => 'setlang'], function () {
    // Rotas SEO para categorias de imóveis
    Route::get('/imoveis/{slug}', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])
        ->where('slug', 'casas-.*|apartamentos-.*|terrenos-.*|quintas-.*')
        ->name('property.seo.category');
    
    // Rota genérica de detalhes de imóveis
    Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')
        ->name('front.property.detail');
});

// Custom Page middleware";

$content = str_replace($catchAll, $seoRoutes, $content);

file_put_contents($file, $content);
echo "✅ Rotas SEO movidas ANTES do catch-all!\n";
