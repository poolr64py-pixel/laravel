<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\FrontendLanguage;

class FrontendLocale
{
  use FrontendLanguage;
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    // Pula rotas admin/user/agent APENAS no domínio principal (www.terrasnoparaguay.com)
    $host = $request->getHost();
    $isMainDomain = in_array($host, ['www.terrasnoparaguay.com', 'terrasnoparaguay.com']);
    
    if ($isMainDomain && ($request->is('admin/*') || $request->is('admin') || 
        $request->is('user/*') || $request->is('user') ||
        $request->is('agent/*') || $request->is('agent') ||
        $request->is('install/*') || $request->is('install'))) {
        \Log::info('FrontendLocale: Pulando rota admin no domínio principal');
        \Log::info("✅ FRONTEND LOCALE - passou sem redirect");
        return $next($request);
    }

   \Log::info('FrontendLocale START', [
        'url' => $request->url(),
        'route' => $request->route() ? $request->route()->getName() : 'NO ROUTE',
        'action' => $request->route() ? $request->route()->getActionName() : 'NO ACTION'
    ]);
    if (session()->has('frontend_lang')) {
      app()->setLocale(session()->get('frontend_lang'));
    } else {
      $defaultLang = $this->defaultLang();
      if (!empty($defaultLang)) {
        session()->put('frontend_lang', $defaultLang->code);
        app()->setLocale($defaultLang->code);
      }
    }
\Log::info('FrontendLocale END');   
 return $next($request);
  }
}
