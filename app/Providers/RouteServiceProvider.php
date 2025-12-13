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
          error_log("🔵 MAP() chamado - host: " . request()->getHost());
        $this->mapAdminRoutes();      // Admin PRIMEIRO
        $this->mapWebRoutes();         // Web segundo
        $this->mapApiRoutes();         // API terceiro
        $this->mapTenantFrontendRoutes(); // Tenant POR ÚLTIMO (wildcard)
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
    error_log("🔴 mapAdminRoutes() INICIO");
    try {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
        error_log("🔴 mapAdminRoutes() SUCESSO");
    } catch (\Exception $e) {
        error_log("🔴 mapAdminRoutes() ERRO: " . $e->getMessage());
        error_log("🔴 Arquivo: " . $e->getFile() . " Linha: " . $e->getLine());
    }
}
protected function mapTenantFrontendRoutes()
{
    $host = request()->getHost();
    $websiteHost = config('app.website_host', 'terrasnoparaguay.com');
    $cleanHost = str_replace('www.', '', $host);
    $cleanWebsiteHost = str_replace('www.', '', $websiteHost);
    
    // Apenas carregar se for SUBDOMÍNIO (não é o domínio principal)
    if ($cleanHost !== $cleanWebsiteHost) {
        error_log("✅ Tenant subdomínio: {$cleanHost}");
        Route::domain($host)
            ->middleware("web")
            ->namespace($this->namespace)
            ->group(base_path("routes/tenant_frontend.php"));
    }
}
}

