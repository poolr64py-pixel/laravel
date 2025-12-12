<?php
use Illuminate\Support\Facades\URL;

if (!function_exists('getUserHref')) {

function getUserHref($menuData)
{
    // Se $menuData tiver a propriedade 'slug', usa como caminho relativo
    if (!empty($menuData->slug)) {
        // Pega o domínio atual (com subdomínio)
        $currentDomain = request()->getHost();
        $protocol = request()->secure() ? 'https://' : 'http://';
        
        return $protocol . $currentDomain . '/' . ltrim($menuData->slug, '/');
    }
    // fallback caso não exista slug
    return '#';
}
