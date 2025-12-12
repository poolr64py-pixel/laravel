<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';
    
    public const HOME = '/home';

    public function boot()
    {
        Route::pattern('domain', '[a-z0-9.\-]+');
        parent::boot();
    }

    public function map()
    {
        $this->mapTenantFrontendRoutes();
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapWebRoutes()
    {
        $host = request()->getHost();
        $websiteHost = config('app.website_host', 'terrasnoparaguay.com');
        $cleanHost = str_replace('www.', '', $host);
        $cleanWebsiteHost = str_replace('www.', '', $websiteHost);
        
        // Só carregar rotas web se FOR o site principal
        if ($cleanHost === $cleanWebsiteHost) {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        }
    }

    protected function mapAdminRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }

    protected function mapTenantFrontendRoutes()
    {
        $host = request()->getHost();
        $websiteHost = config('app.website_host', 'terrasnoparaguay.com');
        $cleanHost = str_replace('www.', '', $host);
        $cleanWebsiteHost = str_replace('www.', '', $websiteHost);
        
        // Só carregar rotas de tenant se NÃO for o site principal
        if ($cleanHost !== $cleanWebsiteHost) {
            Route::middleware("web")
                ->namespace($this->namespace)
                ->group(base_path("routes/tenant_frontend.php"));
        }
    }
}

