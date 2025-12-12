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
        if (Session::has('frontend_lang')) {
            $locale = Session::get('frontend_lang');
            App::setLocale($locale);
        }
        
        return $next($request);
    }
}
