<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Remover TODAS as ocorrências de rotas imoveis
$content = preg_replace('/.*PropertySeoController.*/s', '', $content);
$content = preg_replace('/.*ROTAS SEO.*/s', '', $content);
$content = preg_replace('/.*front\.property\.detail.*/s', '', $content);
$content = preg_replace('/Route::get\(\'\/imoveis\/\{slug\}\'.*/s', '', $content);

// Encontrar onde adicionar (ANTES do catch-all /{slug})
$catchAllPattern = '/\/\/ Custom Page middleware.*?Route::group.*?routeAccess.*?\{.*?Route::get.*?\{slug\}.*?cpage.*?\}\);/s';

if (preg_match($catchAllPattern, $content, $matches)) {
    $catchAll = $matches[0];
    
    $newRoutes = <<<'ROUTES'
// === ROTAS DE IMÓVEIS (ANTES DO CATCH-ALL) ===
Route::group(['middleware' => 'setlang'], function () {
    // Listagem geral
    Route::get('/imoveis', 'Front\FrontendController@allProperties')->name('front.properties');
    
    // Categorias SEO (específicas - vêm primeiro)
    Route::get('/imoveis/casas-asuncion', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'casas-asuncion')->name('imoveis.casas-asuncion');
    Route::get('/imoveis/apartamentos-asuncion', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'apartamentos-asuncion')->name('imoveis.apartamentos-asuncion');
    Route::get('/imoveis/terrenos-asuncion', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'terrenos-asuncion')->name('imoveis.terrenos-asuncion');
    Route::get('/imoveis/casas-luque', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'casas-luque')->name('imoveis.casas-luque');
    Route::get('/imoveis/terrenos-luque', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'terrenos-luque')->name('imoveis.terrenos-luque');
    Route::get('/imoveis/quintas-luque', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'quintas-luque')->name('imoveis.quintas-luque');
    Route::get('/imoveis/casas-san-bernardino', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'casas-san-bernardino')->name('imoveis.casas-san-bernardino');
    Route::get('/imoveis/quintas-san-bernardino', [App\Http\Controllers\Front\PropertySeoController::class, 'category'])->defaults('slug', 'quintas-san-bernardino')->name('imoveis.quintas-san-bernardino');
    
    // Detalhes de imóvel específico (genérica - vem depois)
    Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')->name('front.property.detail');
});

ROUTES;
    
    $content = str_replace($catchAll, $newRoutes . "\n\n" . $catchAll, $content);
    
    file_put_contents($file, $content);
    echo "✅ Rotas reescritas com URLs específicas!\n";
} else {
    echo "❌ Não encontrou o catch-all\n";
}
