<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\PropertySeoController;

// Rotas de Imóveis - Carregadas com PRIORIDADE ALTA
Route::group(['middleware' => 'setlang'], function () {
    
    // Listagem geral
    Route::get('/imoveis', 'Front\FrontendController@allProperties')->name('front.properties');
    
    // Categorias SEO - URLs EXATAS
    Route::get('/imoveis/casas-asuncion', [PropertySeoController::class, 'category'])->defaults('slug', 'casas-asuncion');
    Route::get('/imoveis/apartamentos-asuncion', [PropertySeoController::class, 'category'])->defaults('slug', 'apartamentos-asuncion');
    Route::get('/imoveis/terrenos-asuncion', [PropertySeoController::class, 'category'])->defaults('slug', 'terrenos-asuncion');
    Route::get('/imoveis/casas-luque', [PropertySeoController::class, 'category'])->defaults('slug', 'casas-luque');
    Route::get('/imoveis/terrenos-luque', [PropertySeoController::class, 'category'])->defaults('slug', 'terrenos-luque');
    Route::get('/imoveis/quintas-luque', [PropertySeoController::class, 'category'])->defaults('slug', 'quintas-luque');
    Route::get('/imoveis/casas-san-bernardino', [PropertySeoController::class, 'category'])->defaults('slug', 'casas-san-bernardino');
    Route::get('/imoveis/quintas-san-bernardino', [PropertySeoController::class, 'category'])->defaults('slug', 'quintas-san-bernardino');
    
    // Detalhes de imóvel (ÚLTIMA - pega o resto)
    Route::get('/imoveis/{slug}', 'Front\FrontendController@propertyDetail')->name('front.property.detail');
});

// Aluguéis
Route::group(['middleware' => 'setlang'], function () {
    Route::get('/alugueis/casas-asuncion', [PropertySeoController::class, 'category'])->defaults('slug', 'alugueis-casas-asuncion');
    Route::get('/alugueis/apartamentos-asuncion', [PropertySeoController::class, 'category'])->defaults('slug', 'alugueis-apartamentos-asuncion');
    Route::get('/alugueis/casas-luque', [PropertySeoController::class, 'category'])->defaults('slug', 'alugueis-casas-luque');
    Route::get('/alugueis/apartamentos-luque', [PropertySeoController::class, 'category'])->defaults('slug', 'alugueis-apartamentos-luque');
});
