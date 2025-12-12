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
        $user = getUser();
        
        // Se não houver usuário, redirecionar para home
        if (!$user || !$user->id) {
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
