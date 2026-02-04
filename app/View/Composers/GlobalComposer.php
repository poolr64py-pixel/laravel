<?php

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;
use App\Models\Menu;
use App\Models\Social;
use App\Traits\FrontendLanguage;

class GlobalComposer
{
  use FrontendLanguage;
  
  public function compose(View $view)
  {
    // Pegar language_id da sessão
    $langId = session('language_id', 179); // Default: PT
    
    // Buscar idioma pelo ID
    $currentLang = Language::find($langId);
    
    // Fallback se não encontrar
    if (!$currentLang) {
        $currentLang = Language::where('is_default', 1)->first();
    }

    $view->with('bs', $currentLang ? $currentLang->basic_setting : null);
    $view->with('be', $currentLang ? $currentLang->basic_extended : null);
  }
}
