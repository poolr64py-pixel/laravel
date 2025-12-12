<?php

namespace App\Providers;

use App\View\Composers\AdminComposer;
use App\View\Composers\AgentComposer;
use App\View\Composers\FrontendComposer;
use App\View\Composers\TenantFrontendComposer;
use App\View\Composers\GlobalComposer;
use App\View\Composers\TenantComposer;
use App\View\Composers\TenantFrontLangBlade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//  // \Log::info('AppServiceProvider boot called');
        Paginator::useBootstrap();

        if (!app()->runningInConsole()) {

            $this->composeSpecificViews();
        }
         // Detectar domínio dinamicamente
if (request()->getHost()) {
    \URL::forceRootUrl('https://' . request()->getHost());
    \URL::forceScheme('https');

     
 }
}

    /**
     * Compose specific views.
     *
     * @return void
     */
    protected function composeSpecificViews()
    {
        View::composer('*', GlobalComposer::class);

//   // \Log::info('View compoer registered for tenant_frontend');       
 View::composer('admin.*', AdminComposer::class);
        View::composer('front.*', FrontendComposer::class);
        View::composer('user.*', TenantComposer::class);
        // this is  for where use TenantFontentLanguage
        View::composer([
            'user.partials.languages',
            'user.partials.side-navbar',
            'agent.partials.side-navbar',
            'agent.partials.languages',
        ], TenantFrontLangBlade::class);
        View::composer('agent.*', AgentComposer::class);
        View::composer(['tenant_frontend.*', 'components.tenant.frontend.*'], TenantFrontendComposer::class);
    }
}
