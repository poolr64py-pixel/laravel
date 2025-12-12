<?php

namespace App\View\Composers;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\BasicSetting;
use App\Models\Constant;
use Illuminate\View\View;

class TenantFrontendComposer
{
    public function compose(View $view)
    {
        \Log::info('TenantFrontendComposer::compose called');
        $this->fetchViewData($view);
    }

    public function fetchViewData($view)
    {
        try {
            $tenant = getUser();
            
            if (!$tenant || !$tenant->id) {
                return;
            }
            
            $tenantId = $tenant->id;
            
            $permissions = UserPermissionHelper::packagePermission($tenantId);
            if (is_string($permissions)) {
                $permissions = json_decode($permissions, true);
            }
            
            $basicInfo = \App\Models\User\BasicSetting::where('user_id', $tenantId)->first();
            
            \Log::info('TenantFrontendComposer executado', [
                'tenant_id' => $tenantId,
                'website_title' => $basicInfo->website_title ?? 'NULL'
            ]);
            
            $breadcrumb = !empty($basicInfo->breadcrumb) ? $basicInfo->breadcrumb : 'assets/tenant-front/images/default/breadcum.jpg';
            
            $language = \App\Models\User\Language::where('user_id', $tenantId)->where('is_default', 1)->first();
            $menu = \App\Models\User\Menu::where('language_id', $language->id)->first();
            $menuDatas = !empty($menu->menus) ? (is_string($menu->menus) ? json_decode($menu->menus) : $menu->menus) : [];
            
            \Log::info('Menu Debug', [
                'language_id' => $language->id ?? 'NULL',
                'menu_exists' => !empty($menu),
                'menuDatas_count' => count($menuDatas)
            ]);
            
            $view->with([
                'tenant' => $tenant,
                'permissions' => $permissions ?? [],
                'breadcrumb' => $breadcrumb,
                'basicInfo' => $basicInfo,
                'keywords' => [],
                'menuDatas' => $menuDatas,
                'menu' => $menu,
                'language' => $language,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('TenantFrontendComposer Error', ['error' => $e->getMessage()]);
        }
    }
}
