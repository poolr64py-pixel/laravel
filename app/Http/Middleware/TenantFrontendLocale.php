<?php
namespace App\Http\Middleware;

use App\Models\User\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class TenantFrontendLocale
{
    use TenantFrontendLanguage;
public function handle(Request $request, Closure $next): Response
{
    error_log('🔵 TenantFrontendLocale INICIO: ' . $request->url());
    
    // Pegar o user_id do domínio/tenant
    $user = getUser();
    $userId = $user ? $user->id : null;
    
    // Se não tiver usuário do domínio, tentar pegar da sessão
    if (!$userId && session()->has('user_id')) {
        $userId = session('user_id');
    }
    
    if (!$userId) {
        error_log('❌ TenantFrontendLocale - Sem user_id');
        return $next($request);
    }
    
    $locale = session('lang');
    error_log('📝 Sessão atual: lang=' . ($locale ?? 'NULL') . ', user_id=' . $userId);
    
    if (!$locale) {
        $defaultLanguage = $this->defaultLang($userId);
        if ($defaultLanguage) {
            $locale = $defaultLanguage->code;
            Session::put('lang', $locale);
            Session::save();
            error_log('✨ Usando idioma padrão: ' . $locale);
        }
    } else {
        $checkLanguage = $this->selectLang($userId, $locale);
        if (!$checkLanguage) {
            $defaultLang = $this->defaultLang($userId);
            $locale = $defaultLang->code;
            Session::put('lang', $locale);
            Session::save();
            error_log('⚠️ Idioma não existe, voltando ao padrão: ' . $locale);
        } else {
            error_log('✅ Mantendo idioma da sessão: ' . $locale);
        }
    }
    
    app()->setLocale($locale);
    error_log('🔵 TenantFrontendLocale FIM: locale_final=' . $locale);
    
    return $next($request);
}

}
