<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Encontrar onde está "// Custom Page middleware"
$searchFor = "// Custom Page middleware";

$routesToAdd = <<<'ROUTES'
// === ROTAS DE IMÓVEIS (ANTES DO CATCH-ALL) ===
Route::group(['middleware' => 'setlang'], function () {
    // Listagem geral
    Route::get('/imoveis', 'Front\FrontendController@allProperties')->name('front.properties');
    
    // Categorias SEO - URLs específicas
    Route::get('/imoveis/casas-asuncion', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'casas-asuncion');
    Route::get('/imoveis/apartamentos-asuncion', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'apartamentos-asuncion');
    Route::get('/imoveis/terrenos-asuncion', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'terrenos-asuncion');
    Route::get('/imoveis/casas-luque', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'casas-luque');
    Route::get('/imoveis/terrenos-luque', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'terrenos-luque');
    Route::get('/imoveis/quintas-luque', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'quintas-luque');
    Route::get('/imoveis/casas-san-bernardino', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'casas-san-bernardino');
    Route::get('/imoveis/quintas-san-bernardino', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'quintas-san-bernardino');
    
    // Detalhes de imóvel (genérica)
    Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')->name('front.property.detail');
});

// Custom Page middleware
ROUTES;

$content = str_replace($searchFor, $routesToAdd, $content);

file_put_contents($file, $content);
echo "✅ Rotas adicionadas antes do catch-all!\n";
