<?php
namespace App\Http\Middleware;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\User;
use App\Models\User\UserPermission;
use Closure;
use Illuminate\Http\Request;
class RouteAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $page)
    {
        // Não aplica RouteAccess em rotas admin, user login, agent, install
        if ($request->is('admin/*') || $request->is('admin') ||
            $request->is('login') || $request->is('login/*') ||
            $request->is('user/*') || $request->is('agent/*') ||
            $request->is('register') || $request->is('register/*') ||
            $request->is('imoveis') || $request->is('imoveis/*') ||
            $request->is('projetos') || $request->is('projetos/*') ||
            $request->is('contact') ||
            $request->is('install/*')) {
            return $next($request);
        }
        
        $user = getUser();
        error_log('🔍🔍 RouteAccess - Page: ' . $page . ' | User: ' . ($user ? 'ID=' . $user->id : 'NULL'));
error_log('🔍🔍 RouteAccess - Request URI: ' . $request->getRequestUri());        
        // Se não houver usuário, redirecionar para home
         if (!$user || !$user->id) {
    error_log('❌❌ RouteAccess: Sem usuário! Redirecionando para home. Page: ' . $page);
    error_log('❌❌ User: ' . ($user ? json_encode(['id' => $user->id, 'username' => $user->username]) : 'NULL'));
    return redirect('/');
}        
        $packagePermissions = UserPermissionHelper::packagePermission($user->id);
        
        \Log::info('RouteAccess Debug', [
            'user_id' => $user->id ?? 'NULL',
            'page' => $page,
            'permissions' => $packagePermissions,
            'has_permission' => in_array($page, $packagePermissions ?? [])
        ]);
        
        if (is_string($packagePermissions)) {
            $packagePermissions = json_decode($packagePermissions, true);
        }
        
        if (!in_array($page, $packagePermissions)) {
            return redirect('/');
        }
        
        return $next($request);
    }
}
