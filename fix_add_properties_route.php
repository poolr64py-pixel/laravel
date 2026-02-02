<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Procurar onde estão as rotas SEO e adicionar a rota de listagem ANTES
$search = "// === ROTAS SEO (DEVEM VIR ANTES DO CATCH-ALL) ===
Route::group(['middleware' => 'setlang'], function () {
    // Rotas SEO para categorias de imóveis";

$replace = "// === ROTAS SEO (DEVEM VIR ANTES DO CATCH-ALL) ===
Route::group(['middleware' => 'setlang'], function () {
    // Rota de listagem geral de imóveis
    Route::get('/imoveis', 'Front\FrontendController@allProperties')->name('front.properties');
    
    // Rotas SEO para categorias de imóveis";

$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);
echo "✅ Rota front.properties adicionada!\n";
