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
          error_log("ğŸ”µ MAP() chamado - host: " . request()->getHost());
        $this->mapAdminRoutes();      // Admin PRIMEIRO
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
    
    error_log("?? mapWebRoutes - host: {$host} | isMainDomain: " . ($isMainDomain ? 'SIM' : 'NÃO'));
    
    // Só carregar rotas web se FOR o site principal
    if ($isMainDomain) {
        error_log("? Carregando rotas WEB (site principal)");
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
}
protected function mapAdminRoutes()
{
    error_log("ğŸ”´ mapAdminRoutes() INICIO");
    try {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
        error_log("ğŸ”´ mapAdminRoutes() SUCESSO");
    } catch (\Exception $e) {
        error_log("ğŸ”´ mapAdminRoutes() ERRO: " . $e->getMessage());
        error_log("ğŸ”´ Arquivo: " . $e->getFile() . " Linha: " . $e->getLine());
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
    
    error_log("?? mapTenantFrontendRoutes - host: {$host} | isMainDomain: " . ($isMainDomain ? 'SIM' : 'NÃO'));
    
    // Carregar rotas tenant se NÃO for o domínio principal
    if (!$isMainDomain) {
        error_log("? Carregando rotas TENANT para: {$host}");
        Route::domain($host)
            ->middleware("web")
            ->namespace($this->namespace)
            ->group(base_path("routes/tenant_frontend.php"));
    }
}
}

