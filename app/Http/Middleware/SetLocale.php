<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        error_log('⚡ SetLocale INICIO');
        
        // Verificar se é tenant (subdomínio)
        $user = getUser();
        
        if ($user) {
            // É um tenant - usar tenant_frontend_lang
            if (Session::has('tenant_frontend_lang')) {
                $locale = Session::get('tenant_frontend_lang');
                App::setLocale($locale);
                error_log('⚡ SetLocale TENANT: locale=' . $locale);
            } else {
                error_log('⚡ SetLocale TENANT: sem sessão tenant_frontend_lang');
            }
        } else {
            // É o site principal - usar frontend_lang
            if (Session::has('frontend_lang')) {
                $locale = Session::get('frontend_lang');
                App::setLocale($locale);
                error_log('⚡ SetLocale MAIN: locale=' . $locale);
            } else {
                error_log('⚡ SetLocale MAIN: sem sessão frontend_lang');
            }
        }
        
        return $next($request);
    }
}
