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
        $this->mapAdminRoutes();      // Admin PRIMEIRO
        // $this->mapTenantRoutes(); 
        $this->mapTenantFrontendRoutes();     
        $this->mapWebRoutes();         // Web segundo
        $this->mapApiRoutes();         // API terceiro
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
    
    // Verificar se é EXATAMENTE o domínio principal (com ou sem www)
    $isMainDomain = in_array($host, [
        $websiteHost,
        'www.' . $websiteHost
    ]);
    
    // Só carregar rotas web se FOR o site principal
    if ($isMainDomain) {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
}
protected function mapAdminRoutes()
{
    try {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    } catch (\Exception $e) {
    }
}
protected function mapTenantFrontendRoutes()
{
    $host = request()->getHost();
    $websiteHost = config('app.website_host', 'terrasnoparaguay.com');
    
    // Verificar se é EXATAMENTE o domínio principal (com ou sem www)
    $isMainDomain = in_array($host, [
        $websiteHost,
        'www.' . $websiteHost
    ]);
    
    
    // Carregar rotas tenant se NÃO for o domínio principal
    if (!$isMainDomain) {
        Route::domain($host)
            ->middleware("web")
            ->namespace($this->namespace)
            ->group(base_path("routes/tenant_frontend.php"));
    }
}
protected function mapTenantRoutes()
{
    Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/tenant.php'));
}

}

