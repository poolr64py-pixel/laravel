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
// \Log::info('FrontendLocale middleware (web.php)', ['url' => $request->url(), 'session_lang' => session()->get('frontend_lang')]);
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
