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
        $this->map();
    }

    public function map()
    {
        $this->mapAdminRoutes();
        $this->mapTenantFrontendRoutes();
        $this->mapWebRoutes();
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
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
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
        
        $isMainDomain = in_array($host, [
            $websiteHost,
            'www.' . $websiteHost
        ]);

        if (!$isMainDomain) {
            Route::domain($host)
                ->middleware("web")
                ->namespace($this->namespace)
                ->group(base_path("routes/tenant_frontend.php"));
        }
    }
}
