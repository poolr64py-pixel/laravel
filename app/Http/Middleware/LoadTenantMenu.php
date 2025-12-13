<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadTenantMenu
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = getUser();
  // DEBUG temporário
        file_put_contents('/tmp/menu_debug.txt', date('Y-m-d H:i:s') . ' - User: ' . ($user ? $user->id : 'NULL') . "\n", FILE_APPEND);        
        if ($user && $user->id) {
            $tenantId = $user->id;
            
            // Carregar idioma da sessão ou padrão
            $langCode = session('lang');
            $langId = session('language_id');
            error_log("🔵 LoadTenantMenu: session lang=$langCode, language_id=$langId");
            $language = null;
            
            if ($langCode) {
                $language = \App\Models\User\Language::where('user_id', $tenantId)
                    ->where('code', $langCode)
                    ->first();
            }
            
            // Fallback para idioma padrão se não encontrar
            if (!$language) {
                $language = \App\Models\User\Language::where('user_id', $tenantId)
                    ->where('is_default', 1)
                    ->first();
            }
       file_put_contents('/tmp/menu_debug.txt', "Language: " . ($language ? $language->id : 'NULL') . "\n", FILE_APPEND);

// Carregar todos os idiomas disponíveis
$allLanguageInfos = \App\Models\User\Language::where('user_id', $tenantId)->get();
view()->share('allLanguageInfos', $allLanguageInfos);
            

            if ($language) {
                // Carregar menu
                error_log("🔵 Carregando menu para language_id=" . $language->id . " (code: " . $language->code . ")");
                $menu = \App\Models\User\Menu::where('language_id', $language->id)->first();
                if ($menu) {
                    $firstItem = !empty($menu->menus[0]) ? json_encode($menu->menus[0]) : "vazio";
                    error_log("🔵 Menu encontrado! Primeiro item: " . $firstItem);
                }
                $menuDatas = !empty($menu->menus) ? (is_string($menu->menus) ? json_decode($menu->menus) : $menu->menus) : [];
 file_put_contents('/tmp/menu_debug.txt', "Menu items: " . count($menuDatas) . "\n", FILE_APPEND);
                
                // Compartilhar com todas as views
                view()->share('menuDatas', $menuDatas);
                view()->share('menu', $menu);
                view()->share('language', $language);
                view()->share('currentLanguageInfo', $language);
             
// Carregar keywords (traduções) com fallbacks
$keywords = [];
if ($language && !empty($language->keywords)) {
    $keywords = is_string($language->keywords) ? json_decode($language->keywords, true) : $language->keywords;
}

// Adicionar fallbacks para keywords que podem faltar
$defaultKeywords = [
    'Add to Wishlist' => 'Add to Wishlist',
    'Saved' => 'Saved',
    'Remove from Wishlist' => 'Remove from Wishlist',
];

$keywords = array_merge($defaultKeywords, $keywords);
view()->share('keywords', $keywords);
            }
        }
        
        return $next($request);
    }
}
