<?php

namespace App\View\Composers;

use App\Constants\Constant;
use App\Http\Helpers\UserPermissionHelper;
use Illuminate\View\View;
use App\Models\User\BasicSetting;
use App\Models\User\HomePage\Section;
use App\Models\User\Language;
use App\Models\User\Menu;
use App\Models\User\Social;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\DB;

class TenantFrontendComposer extends BaseComposer
{
  use TenantFrontendLanguage;
  public function compose(View $view)
  {
    $tenant = getUser();
    $tenantId = $tenant->id;

    // Set preferences
    $this->changePreferences($tenantId);


    // Get current language
    $userCurrentLang = $this->currentLang($tenantId);

    // Get necessary data for the view
    $data = $this->fetchViewData($tenant, $userCurrentLang);

    // Pass all data to the views
    $view->with($data);
  }


  private function fetchViewData($tenant, $currentLang)
  {

    $tenantId = $tenant->id;
    if (!empty($tenantId)) {
      $permissions = UserPermissionHelper::packagePermission($tenantId);
      $permissions = json_decode($permissions, true);
    }

    $basicInfo = BasicSetting::where('user_id', $tenantId)->first();
    $breadcrumb = !empty($basicInfo->breadcrumb) ?  Constant::WEBSITE_BREADCRUMB . '/' . $basicInfo->breadcrumb : 'assets/tenant-front/images/default/breadcum.jpg';
    return [
      'tenant' => $tenant,
      'permissions' => $permissions,
      'basicInfo' => $basicInfo,
      'allLanguageInfos' => $this->language->where('user_id', $tenantId)->get(),
      'currentLanguageInfo' => $currentLang,

      'keywords' => json_decode($currentLang->keywords ?? '[]', true),
      'breadcrumb' => $breadcrumb,
      'menuInfos' => Menu::where('language_id', $currentLang->id)->where('user_id', $tenantId)->pluck('menus')->first() ?? json_encode([]),
      'popupInfos' => $currentLang->announcementPopups()->where('status', 1)->orderBy('serial_number', 'asc')->get(),
      'cookieAlertInfo' => $currentLang->cookieAlertInfo()->first(),

      'footerSectionStatus' => Section::where('user_id', $tenantId)->value('footer_section_status'),
    ];
  }
}
