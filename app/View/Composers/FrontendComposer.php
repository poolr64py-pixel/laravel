<?php

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;
use App\Models\Menu;
use App\Models\Social;
use App\Traits\FrontendLanguage;

class FrontendComposer
{
  use FrontendLanguage;
  
  public function compose(View $view)
  {
    // Pegar language_id da sessão (mesmo do GlobalComposer)
    $langId = session('language_id', 179); // Default: PT
    
    // Buscar idioma pelo ID
    $currentLang = Language::find($langId);
    
    // Fallback se não encontrar
    if (!$currentLang) {
        $currentLang = Language::where('is_default', 1)->first();
    }

    $menus = Menu::where('language_id', $langId)->first()->menus ?? json_encode([]);
    $rtl = $currentLang->rtl ?? 0;

    $view->with('bs', $currentLang ? $currentLang->basic_setting : null);
    $view->with('be', $currentLang ? $currentLang->basic_extended : null);
    $view->with('currentLang', $currentLang);
    $view->with('menus', $menus);
    $view->with('rtl', $rtl);
    $view->with('socials', Social::orderBy('serial_number', 'ASC')->get());
    $view->with('langs', $this->allLangs());
  }
}
