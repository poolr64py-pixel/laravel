<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\AdminLanguage;

class AdminLang
{
    use AdminLanguage;

    public function handle($request, Closure $next)
    {
        if (session()->has('admin_lang')) {
            app()->setLocale(session()->get('admin_lang'));
        } else {
            $defaultLang = $this->defaultLang();
            if (!empty($defaultLang)) {
                session()->put('admin_lang', $defaultLang->code);
                app()->setLocale($defaultLang->code);
            }
        }

        \Log::info("✅ ADMINLANG - passou");
        return $next($request);
    }
}
