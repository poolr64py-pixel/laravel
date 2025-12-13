<?php
namespace App\Http\Middleware;

use App\Models\User\BasicSetting;
use Closure;

class UserMaintenance
{
    public function handle($request, Closure $next)
    {
        try {
            $user = getUser();
            error_log('🔧 UserMaintenance - User: ' . ($user ? $user->id : 'NULL'));
            
            if (!$user || !$user->id) {
                error_log('❌ UserMaintenance - Sem usuário, pulando middleware');
                return $next($request);
            }

            $userId = $user->id;
            error_log('✅ UserMaintenance - User ID: ' . $userId);

            $basicSetting = BasicSetting::where('user_id', $userId)->first();
            $maintenanceStatus = $basicSetting->maintenance_status ?? 0;

            if ($maintenanceStatus == 1) {
                $token = $basicSetting->bypass_token;
                if (session()->has('user-bypass-token') && session()->get('user-bypass-token') == $token) {
                    return $next($request);
                }
                $data['userBs'] = $basicSetting;
                return response()->view('errors.user-503', $data);
            }

            error_log('✅ UserMaintenance - Passando para próximo middleware');
            return $next($request);
        } catch (\Exception $e) {
            error_log('❌❌ UserMaintenance ERRO: ' . $e->getMessage());
            return $next($request);
        }
    }
}
