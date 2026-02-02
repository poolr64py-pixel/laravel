<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\UserProperty;
use App\Models\UserPropertyContent;
use App\Models\UserPropertyCategoryContent;
use App\Models\Language;
use Illuminate\Http\Request;

class PropertySeoController extends Controller
{
    /**
     * Página de categoria SEO (tipo + cidade)
     */
    public function category($slug)
    {
        $seoPages = config('seo_pages');
        
        // Buscar configuração da página
        $pageConfig = collect($seoPages)->get($slug);
        
        if (!$pageConfig) {
            abort(404);
        }
        
        // Pegar idioma atual
        $langCode = session()->get('frontend_lang', 'pt');
        $language = Language::where('code', $langCode)->first();
        
        if (!$language) {
            $language = Language::where('code', 'pt')->first();
        }
        
        // Buscar categoria
        $category = UserPropertyCategoryContent::where('slug', $pageConfig['category_slug'])
            ->where('language_id', $language->id)
            ->first();
        
        // Buscar imóveis
        $query = UserProperty::where('status', 1)
            ->where('city_id', $pageConfig['city_id']);
        
        if ($category) {
            $query->where('category_id', $category->category_id);
        }
        
        $properties = $query->with(['contents' => function($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->latest()->paginate(12);
        
        return view('front.property-seo-category', [
            'pageConfig' => $pageConfig,
            'properties' => $properties,
            'language' => $language,
        ]);
    }
}
