<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        // Super admin (role_id null) tem acesso total
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            
            // Se não tem role, é super admin - libera acesso
            if (empty($admin->role_id) || empty($admin->role)) {
                return $next($request);
            }
            
            // Se tem role, verifica permissões
            $permissions = json_decode($admin->role->permissions, true);
            if (!in_array($permission, $permissions)) {
            \Log::error("PERMISSÃO NEGADA", ["admin" => $admin->username, "permission" => $permission, "permissions" => $permissions]);
                return redirect()->route('admin.dashboard');
            }
        }
        return $next($request);
    }
}
