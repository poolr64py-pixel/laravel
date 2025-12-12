<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Models\User\Property\City;
use App\Models\User\Property\State;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Amenity;
use App\Models\User;
use App\Models\Language;
use App\Models\User\BasicSetting; 
use App\Models\User\HomePage\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\Tenant\Frontend\Language as FrontendLanguage;
use App\Models\User\HomePage\Section;
use App\Models\User\HomePage\SectionTitle;
class HomePageController extends Controller
{
use FrontendLanguage;   
 protected function getTenantCurrencyInfo($tenantId)
    {
        $baseCurrencyInfo = BasicSetting::where('user_id', $tenantId)
            ->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
            ->first();
        return $baseCurrencyInfo;
    }
protected function getCustomSectionContents($tenantId, $languageId)
{
    // Buscar informações das seções do banco
    $sectionsData = \App\Models\User\HomePage\Section::where('user_id', $tenantId)
        ->orderBy('id', 'asc')
        ->get();
    
    return $sectionsData;
}
public function index()
{
    $user = getUser();
    if (!$user || !$user->id) {
        return redirect('/');
    }
    
    $tenantId = $user->id;
    $language = $this->currentLang($tenantId);
    $queryResult['language'] = $language;
    
    $BS = BasicSetting::query()->where('user_id', $tenantId)->first();
    $queryResult['basicInfo'] = $BS;
    $queryResult['user'] = $user;
    
    // Homepage data
    $version = $BS->theme_version;
    $sectionInfo = HomePage::query()
        ->where('user_id', $tenantId)
        ->first();
    
    $queryResult['heroStatic'] = $sectionInfo;
    $queryResult['homePage'] = $sectionInfo;
    
    // Custom sections
    $customSecInfo = $this->getCustomSectionContents($tenantId, $language->id);
    $queryResult['sections'] = $customSecInfo;
    
     // Property related data - SIMPLIFICADO
    $queryResult['states'] = [];
    $queryResult['cities'] = [];
    $queryResult['all_cities'] = [];
    $queryResult['all_proeprty_categories'] = [];
    $queryResult['categories'] = [];
    $queryResult['propertyTypes'] = [];
    $queryResult['amenities'] = [];
    $queryResult['properties'] = [];
    $queryResult['featured_properties'] = [];
    $queryResult['min'] = 0;
    $queryResult['max'] = 999999999;
    $queryResult['partners'] = [];
    $queryResult['testimonials'] = [];
    $queryResult['blogs'] = [];
    $queryResult['agents'] = [];
    $queryResult['counters'] = [];
    $queryResult['workProcesses'] = [];
    $queryResult['currencyInfo'] = $this->getTenantCurrencyInfo($tenantId);
    
    // Sections
    $queryResult['secInfo'] = $sectionInfo;
    $queryResult['aboutSection'] = null;
    $queryResult['counterSection'] = null;
    $queryResult['testimonialSection'] = null;
    $queryResult['videoSection'] = null;
    $queryResult['whyChooseSection'] = null;
    $queryResult['newsletterSection'] = null;
    
    // Custom sections (after_*)
    $queryResult['homecusSec'] = [];
    $queryResult['after_hero'] = [];
    $queryResult['after_counter'] = [];
    $queryResult['after_featured_properties'] = [];
    $queryResult['after_about'] = [];
    $queryResult['after_property'] = [];
    $queryResult['after_why_choose_us'] = [];
    $queryResult['after_agent'] = [];
    $queryResult['after_cities'] = [];
    $queryResult['after_testimonial'] = [];
    $queryResult['after_newsletter'] = [];
    
    // Keywords
    $keywords = [
        'Select City' => 'Select City',
        'Select State' => 'Select State',
        'Select Category' => 'Select Category',
        'Search' => 'Search',
        'All' => 'All',
        'Featured' => 'Featured',
    ];
    
    if ($language && !empty($language->keywords)) {
        $langKeywords = json_decode($language->keywords, true);
        if (is_array($langKeywords)) {
            $keywords = array_merge($keywords, $langKeywords);
        }
    }
    
    $queryResult['keywords'] = $keywords;
      $queryResult['allLanguageInfos'] = \DB::table('user_languages')->where('user_id', $tenantId)->get();
    return view('tenant_frontend.home.index-v1', $queryResult);
}

    private function getTenantId(Request $request)
    {
        $host = $request->getHost();
        $websiteHost = env('WEBSITE_HOST', 'terrasnoparaguay.com');
        
        if ($host == $websiteHost) {
            return 1;
        }
        
        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            $username = $parts[0];
            $agent = DB::table('agents')
                ->join('users', 'agents.user_id', '=', 'users.id')
                ->where('users.username', $username)
                ->select('agents.user_id')
                ->first();
                
            if ($agent) {
                return $agent->user_id;
            }
        }
        
        return 1;
    }

    private function getLanguage(Request $request)
    {
        $tenantId = $this->getTenantId($request);
        $languageCode = $request->get('lang', 'pt');
        
        return Language::where('user_id', $tenantId)
            ->where('code', $languageCode)
            ->first() ?? Language::where('user_id', $tenantId)->first();
    }
}
