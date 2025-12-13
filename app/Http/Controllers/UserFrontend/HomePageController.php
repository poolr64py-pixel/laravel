<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Models\User\HomePage\HomePage;
use App\Models\User\Property\City;
use App\Models\User\Property\State;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Amenity;
use App\Models\User;
use App\Models\Language;
use App\Models\User\BasicSetting; 
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
   
// Carregar menu e idiomas
$language = \App\Models\User\Language::where('user_id', $tenantId)->where('is_default', 1)->first();
$menu = \App\Models\User\Menu::where('language_id', $language->id)->first();
$menuDatas = !empty($menu->menus) ? (is_string($menu->menus) ? json_decode($menu->menus) : $menu->menus) : [];

$queryResult['menuDatas'] = $menuDatas;
$queryResult['menu'] = $menu;
$queryResult['language'] = $language;
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
public function aboutus(Request $request)
{
    $user = getUser();
    if (!$user || !$user->id) {
        return redirect('/');
    }
    
    $tenantId = $user->id;
    $language = $this->currentLang($tenantId);
    $lang_id = $language->id;
    
    $data['language'] = $language;
    $data['basicInfo'] = BasicSetting::where('user_id', $tenantId)->first();
    $data['user'] = $user;
    
    // Sections info
    $data['secInfo'] = HomePage::where('user_id', $tenantId)->first();
    
    // About section
    $data['aboutInfo'] = \App\Models\User\HomePage\AboutSection::where('user_id', $tenantId)
        ->where('language_id', $lang_id)
        ->first();
    
    // Page heading and SEO
    $data['pageHeading'] = \App\Models\User\PageHeading::where('user_id', $tenantId)
        ->where('language_id', $lang_id)
        ->first();
        
    $data['seoInfo'] = \App\Models\User\SEO::where('user_id', $tenantId)
        ->where('language_id', $lang_id)
        ->first();
    
    // Additional sections
    $sections = ['about_info', 'why_choose_us', 'testimonial', 'counter'];
    foreach ($sections as $section) {
        $data["after_" . $section] = \App\Models\User\AdditionalSection::where('user_id', $tenantId)
            ->where('position', 'after_' . $section)
            ->where('page_type', 'about')
            ->orderBy('serial_number', 'asc')
            ->get();
    }
    
    // Section status
    $aboutSectionStatus = $data['basicInfo']->about_additional_section_status ?? null;
    if (!empty($aboutSectionStatus)) {
        $data['aboutSec'] = json_decode($aboutSectionStatus, true);
    } else {
        $data['aboutSec'] = [];
    }
    
    $data['aboutImg'] = null;
    $data['breadcrumb'] = null;
    
    // Work steps / Process
$data['workStepsSecInfo'] = null;
$data['steps'] = \App\Models\User\HomePage\WorkProcess::where('user_id', $tenantId)
    ->where('language_id', $lang_id)
    ->orderBy('serial_number', 'asc')
    ->get();
$data['workStepsSecImg'] = null;
$data['after_work_steps'] = collect([]);

// Why choose us
$data['whyChooseUsInfo'] = \App\Models\User\HomePage\WhyChooseUsSection::where('user_id', $tenantId)
    ->where('language_id', $lang_id)
    ->first();
$data['whyChooseUsImg'] = null;

// Testimonials
$data['testimonials'] = \App\Models\User\HomePage\Testimonial::where('user_id', $tenantId)
    ->where('language_id', $lang_id)
    ->orderBy('serial_number', 'asc')
    ->get();
$data['after_testimonial'] = collect([]);

// Counter
$data['counters'] = \App\Models\User\HomePage\CounterInformation::where('user_id', $tenantId)
    ->where('language_id', $lang_id)
    ->orderBy('serial_number', 'asc')
    ->get();
$data['after_counter'] = collect([]);
    return view('tenant_frontend.about', $data);
}
}
