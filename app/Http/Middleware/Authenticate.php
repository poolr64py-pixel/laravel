<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
     protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        if (Request::is('admin') || Request::is('admin/*')) {
            return route('admin.login');
        } elseif (Request::route()->getPrefix() == '{username}/user' || (Request::route()->getPrefix() == '/user' && Request::getHost() != env('WEBSITE_HOST'))) {
            // Redirecionar para o tenant
            return 'https://imoveis.terrasnoparaguay.com/login';
        } elseif (Request::route()->getPrefix() == '{username}/agent' || (Request::route()->getPrefix() == '/agent' && Request::getHost() != env('WEBSITE_HOST'))) {
            return route('frontend.agent.login', getParam());
        } else {
            // Se for domínio principal
            $host = Request::getHost();
            if ($host == 'terrasnoparaguay.com' || $host == 'www.terrasnoparaguay.com') {
                return 'https://imoveis.terrasnoparaguay.com/login';
            }
            // Se for tenant
            return url('/' . Request::route()->parameter('username') . '/login');
        }
    }
}
}
