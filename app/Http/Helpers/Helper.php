<?php

use App\Constants\Constant;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\Language;
use App\Models\Page;
use App\Models\User;
use App\Models\User\Advertisement;
use App\Models\User\BasicSetting;
use App\Models\User\Language as UserLanguage;
use App\Models\User\Page as UserPage;
use App\Models\User\Project\Wishlist as ProjectWishlist;
use App\Models\User\Property\Wishlist;
use App\Models\User\UserSubdomain;
use App\Models\User\UserWebsite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\PaymentGateway;
use App\Models\User\UserPaymentGeteway;

if (!function_exists('email_collector_api')) {
    function email_collector_api($email)
    {
        $data = [
            'item_name'      => 'MultiEstate',
            'email'          => $email,
            'username'       => NULL,
            'item_id'        => NULL,
            'url'            => url('/'),
            'collector_key'  => 'rakoombaa', // authentication or API key
            'purchase_code'  => NULL
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://kreativdev.com/emailcollector/api/collect', // API endpoint
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => http_build_query($data), // Convert array to URL-encoded query string
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);
        $response = curl_exec($curl);
       
        curl_close($curl);
        return;
    }
}

if (!function_exists('collectionToPaginate')) {
    function collectionToPaginate(Collection $items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage();

        $paginatedItems = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $paginatedItems,
            $items->count(),
            $perPage,
            $page,
            array_merge([
                'path' => request()->url(),
                'query' => request()->query(),
            ], $options)
        );
    }
}

if (!function_exists('setEnvironmentValue')) {
    function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");

                // If the key does not exist, add it
                if ($keyPosition === false) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    $endOfLinePosition = strpos($str, "\n", $keyPosition);
                    if ($endOfLinePosition === false) {
                        $endOfLinePosition = strlen($str);
                    }

                    $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
            }
        }

        // Trim any extra spaces or new lines
        $str = rtrim($str, "\n");

        return file_put_contents($envFile, $str) !== false;
    }
}


if (!function_exists('replaceBaseUrl')) {
    function replaceBaseUrl($html)
    {
        $startDelimiter = 'src="';
        $endDelimiter = public_path('assets/front/img/summernote/');
        $startDelimiterLength = strlen($startDelimiter);
        $endDelimiterLength = strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($html, $startDelimiter, $startFrom))) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos($html, $endDelimiter, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $html = substr_replace($html, url('/'), $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }
        return $html;
    }
}

if (!function_exists('setAwsCredentials')) {
    function setAwsCredentials($key, $secret, $region, $bucket)
    {
        config([
            'filesystems.disks.s3.key' => $key,
            'filesystems.disks.s3.secret' => $secret,
            'filesystems.disks.s3.region' => $region,
            'filesystems.disks.s3.bucket' => $bucket,
        ]);
    }
}


if (!function_exists('convertUtf8')) {
    function convertUtf8($value)
    {
        if (!empty($value)) {
            return mb_detect_encoding($value, mb_detect_order(), true) === 'UTF-8' ? $value : mb_convert_encoding($value, 'UTF-8');
        } else {
            return null;
        }
    }
}


if (!function_exists('make_slug')) {
    function make_slug($string)
    {
        // $pattern = '/^\d+\s+/';
        $slug = preg_replace('/\s+/u', '-', trim($string));
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("?", "", $slug);
        return mb_strtolower($slug, 'UTF-8');
    }
}


if (!function_exists('make_input_name')) {
    function make_input_name($string)
    {
        return preg_replace('/\s+/u', '_', trim($string));
    }
}

if (!function_exists('hasCategory')) {
    function hasCategory($version)
    {
        if (strpos($version, "no_category") !== false) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('isDark')) {
    function isDark($version)
    {
        if (strpos($version, "dark") !== false) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('slug_create')) {
    function slug_create($val)
    {
        $slug = preg_replace('/\s+/u', '-', trim($val));
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("?", "", $slug);
        return mb_strtolower($slug, 'UTF-8');
    }
}

if (!function_exists('hex2rgb')) {
    function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }
}


if (!function_exists('getHref')) {
    function getHref($link)
    {
        $href = "#";
        
        // Verifica se a rota existe antes de tentar usá-la
        $routeExists = function($name) {
            return \Route::has($name);
        };
        
        if ($link["type"] == 'home') {
            $href = $routeExists('front.index') ? route('front.index') : '#';
        } else if ($link["type"] == 'listings') {
            $href = $routeExists('front.user.view') ? route('front.user.view') : '#';
        } else if ($link["type"] == 'about') {
            $href = $routeExists('front.user.aboutus') ? route('front.user.aboutus') : '#';
        } else if ($link["type"] == 'pricing') {
            $href = $routeExists('front.pricing') ? route('front.pricing') : '#';
        } else if ($link["type"] == 'faq') {
            $href = $routeExists('front.faq.view') ? route('front.faq.view') : '#';
        } else if ($link["type"] == 'blog') {
            $href = $routeExists('front.blogs') ? route('front.blogs') : '#';
        } else if ($link["type"] == 'contact') {
            $href = $routeExists('front.contact') ? route('front.contact') : '#';
        } else if ($link["type"] == 'custom') {
            if (empty($link["href"])) {
                $href = "#";
            } else {
                $href = $link["href"];
            }
        } else {
            $pageid = (int)$link["type"];
            $page = Page::find($pageid);
            if (!empty($page)) {
                $href = route('front.dynamicPage', [$page->slug]);
            }
        }
        return $href;
    }
}
if (!function_exists('getUserHref')) {
    function getUserHref($link)
    {
        $href = "#";
        $user = getUser();       
 if ($link->type == 'home') {
            $href = Route::has('frontend.user.index') ? route('frontend.user.index', getParam()) : '#';
        } else if ($link->type == 'about-us') {
            $href = route('frontend.aboutus', getParam());
        } else if ($link->type == 'properties') {
            $href = route('frontend.properties', getParam());
        } else if ($link->type == 'projects') {
            $href = route('frontend.projects', getParam());
        } else if ($link->type == 'agents') {
            $href = route('frontend.agents', getParam());
        } else if ($link->type == 'blog') {
            $href = route('frontend.blog', getParam());
        } else if ($link->type == 'contact') {
            $href = route('frontend.contact', getParam());
        } else if ($link->type == 'faq') {
            $href = route('frontend.faq', getParam());
        } else if ($link->type == 'custom') {
            if (empty($link->href)) {
                $href = "#";
            } else {
                $href = $link->href;
            }
        } else {
            $page_id = (int)$link->type;
            $content = App\Models\User\CustomPage\PageContent::query()
                ->where('user_id', $user->id)
                ->where('page_id', $page_id)
                ->first();
error_log('🔍 QUERY EXECUTADA - User encontrado: ' . ($user ? 'SIM (ID: ' . $user->id . ')' : 'NÃO'));           
 if (!empty($content)) {
                $href = route('frontend.dynamic_page', [getParam(), $content->slug]);
            } else {
                $href = "#";
            }
        }
        return $href;
    }
}


if (!function_exists('create_menu')) {
    function create_menu($arr)
    {
        echo '<ul class="sub-menu">';
        foreach ($arr["children"] as $el) {
            // determine if the class is 'submenus' or not
            $class = 'class="nav-item"';
            if (array_key_exists("children", $el)) {
                $class = 'class="nav-item submenus"';
            }
            // determine the href
            $href = getHref($el);
            echo '<li ' . $class . '>';
            echo '<a  href="' . $href . '" target="' . $el["target"] . '">' . $el["text"] . '</a>';
            if (array_key_exists("children", $el)) {
                create_menu($el);
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}


if (!function_exists('format_price')) {
    function format_price($value): string
    {
        if (session()->has('frontend_lang')) {
            $currentLang = Language::where('code', session()->get('frontend_lang'))
                ->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extended;
        if ($bex->base_currency_symbol_position == 'left') {
            return $bex->base_currency_symbol . $value;
        } else {
            return $value . $bex->base_currency_symbol;
        }
    }
}

if (!function_exists('tenantCurrencySymbol')) {
    function tenantCurrencySymbol($tenantId, $value): string
    {
        $bs = BasicSetting::where('user_id', $tenantId)->select('base_currency_symbol', 'base_currency_symbol_position')->first();

        if ($bs->base_currency_symbol_position == 'left') {
            return $bs->base_currency_symbol . $value;
        } else {
            return $value . $bs->base_currency_symbol;
        }
    }
}

// checks if 'current package has subdomain ?'

if (!function_exists('cPackageHasSubdomain')) {
    function cPackageHasSubdomain($user): bool
    {
         $currPackageFeatures = UserPermissionHelper::packagePermission($user->id);
    if (is_string($currPackageFeatures)) {
        if (is_string($currPackageFeatures)) {
            $currPackageFeatures = json_decode($currPackageFeatures, true);
        }
    }

        // if the current package does not contain subdomain
        if (empty($currPackageFeatures) || !is_array($currPackageFeatures) || !in_array('Subdomain', $currPackageFeatures)) {
            return false;
        }
        return true;
    }
}


// checks if 'current package has custom domain ?'
if (!function_exists('cPackageHasCdomain')) {
    function cPackageHasCdomain($user): bool
    {
        if (is_string($currPackageFeatures)) {
            $currPackageFeatures = json_decode($currPackageFeatures, true);
        }
    if (is_string($currPackageFeatures)) {
        $currPackageFeatures = json_decode($currPackageFeatures, true);
    }
        if (empty($currPackageFeatures) || !is_array($currPackageFeatures) || !in_array('Custom Domain', $currPackageFeatures)) {
            return false;
        }
        return true;
    }
}

if (!function_exists('getCdomain')) {

    function getCdomain($user)
    {
        $cdomains = $user->custom_domains()->where('status', 1);
        return $cdomains->count() > 0 ? $cdomains->orderBy('id', 'DESC')->first()->requested_domain : false;
    }
}
 if (!function_exists('getSubdomain')) {

     function getSubdomain($websiteId)
     {
         $website = UserWebsite::find($websiteId);
         $subdomains = $website->subdomains()->where('status', 1);
         return $subdomains->count() > 0 ? $subdomains->orderBy('id', 'DESC')->first()->requested_subdomain : false;
     }
 }

if (!function_exists('getUser')) {

function getUser()
{
    $parsedUrl = parse_url(url()->current());
    $host = $parsedUrl['host'];
    
    
    error_log('🔍 getUser() - host detectado: ' . $host . ' | HTTP_HOST: ' . ($_SERVER['HTTP_HOST'] ?? 'NULL'));

    // Se for www.domain.com ou domain.com (site principal), retorna null
    if ($host === 'www.' . config('app.website_host') || $host === config('app.website_host')) {
        \Log::info('DEBUG getUser - RETURNING NULL (main site)');
        return null;
    }

        // if the current URL contains the website domain
       
 if (strpos($host, config('app.website_host')) !== false) {
     error_log('✅ Host contém website_host'); 
           $host = str_replace('www.', '', $host);
            // if current URL is a path based URL
            if ($host == 'terrasnoparaguay.com') {
 error_log('📍 É path-based URL');             
   $path = explode('/', $parsedUrl['path']);
                $username = $path[1];
}
            // if the current URL is a subdomain
            else {
$hostArr = explode('.', $host);            
    $hostArr = explode('.', $host);
                $username = $hostArr[0];
 error_log('📍 Username extraído: ' . $username);
\Log::info('DEBUG - Username extracted', ['username' => $username, 'host' => $host]);           
 }
error_log('🔍 VERIFICAÇÃO - host: ' . $host . ' | username: ' . $username);
error_log('🔍 COMPARAÇÃO - esperado: ' . $username . '.terrasnoparaguay.com' . ' | recebido: ' . $host);
error_log('🔍 MATCH? ' . (($host == $username . '.' . 'terrasnoparaguay.com') ? 'SIM' : 'NÃO'));
            if (($host == $username . '.' . 'terrasnoparaguay.com') || ($host . '/' . $username == 'terrasnoparaguay.com' . '/' . $username)) {
error_log('✅ ENTROU NO IF! Fazendo query para username: ' . $username);            
    $user = User::where('username', $username)
                    ->where('online_status', 1)
                    ->where('status', 1)
                    ->whereHas('memberships', function ($q) {
                        $q->where('status', '=', 1)
                            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                            ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
                    })
                    ->first();
\Log::info('DEBUG - Returning user', ['user_id' => $user->id ?? 'null']);
               //if user expired
\Log::info('DEBUG - Returning user', ['user_id' => $user->id ?? 'null']);
       if (!$user) {
    error_log('❌ USER NÃO ENCONTRADO - Retornando 404 para username: ' . $username);
    abort(404);
}
error_log('✅ USER ENCONTRADO - ID: ' . $user->id . ' | Username: ' . $user->username);
                // if the current url is a subdomain
                if ($host != 'terrasnoparaguay.com') {
                    if (!cPackageHasSubdomain($user)) {
\Log::info('DEBUG getUser - RETURNING 404', [
    'reason' => 'explain why here'
]);                       
 return view('errors.404');
                    }
                }
error_log('🎯 getUser() RETORNANDO - User ID: ' . ($user ? $user->id : 'NULL') . ' | Username: ' . ($user ? $user->username : 'NULL'));
                return $user;
            }
        }

        // Always include 'www.' at the begining of host
        if (substr($host, 0, 4) == 'www.') {
            $host = $host;
        } else {
            $host = 'www.' . $host;
        }

        try {
            $user = User::where('online_status', 1)
                ->where('status', 1)
                ->whereHas('user_custom_domains', function ($q) use ($host) {
                    $q->where('status', '=', 1)
                        ->where(function ($query) use ($host) {
                            $query->where('requested_domain', '=', $host)
                                ->orWhere('requested_domain', '=', str_replace("www.", "", $host));
                        });
                    // fetch the custom domain , if it matches 'with www.' URL or 'without www.' URL
                })
                ->whereHas('memberships', function ($q) {
                    $q->where('status', '=', 1)
                        ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                        ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
                })->firstOrFail();
        } catch (\Exception $e) {
            return view('errors.404');
        }

        if (!cPackageHasCdomain($user)) {
            return view('errors.404');
        }

        return $user;
    }
}
if (!function_exists('checkWishList')) {
    function checkWishList($property_id, $customer_id)
    {
        $check = Wishlist::where('property_id', $property_id)
            ->where('customer_id', $customer_id)
            ->first();
        if ($check) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('checkProjectWishList')) {
    function checkProjectWishList($project_id, $customer_id)
    {
        $check = ProjectWishlist::where('project_id', $project_id)
            ->where('customer_id', $customer_id)
            ->first();
        if ($check) {
            return true;
        } else {
            return false;
        }
    }
}

// 


if (!function_exists('getParam')) {

     function getParam()
     {
         $parsedUrl = parse_url(url()->current());
         $host = str_replace("www.", "", $parsedUrl['host']);
//           if it is path based URL, then return {username}
         if (strpos($host, 'terrasnoparaguay.com') !== false && $host == 'terrasnoparaguay.com') {
             $path = explode('/', $parsedUrl['path']);
             return $path[1];
         }
         // if it is a subdomain / custom domain , then return the host (username.domain.ext / custom_domain.ext)
         return '';
     }
 }

if (!function_exists('detailsUrl')) {

    function detailsUrl($user)
    {
        $currentUrl = url('/');
        $url = str_replace('https:', '', $currentUrl);
        $url = str_replace('http:', '', $url);
        return $url . '/' . $user->username;
    }
}

if (!function_exists('showAd')) {
    function showAd($resolutionType)
    {
        $user = getUser();
        $ad = Advertisement::query()
            ->where('resolution_type', $resolutionType)
            ->where('user_id', $user->id)
            ->inRandomOrder()
            ->first();
        $bs = User\BasicSetting::query()
            ->where('user_id', $user->id)
            ->first();

        if (!is_null($ad)) {
            if ($resolutionType == 1) {
                $maxWidth = '300px';
                $maxHeight = '250px';
            } else if ($resolutionType == 2) {
                $maxWidth = '300px';
                $maxHeight = '600px';
            } else {
                $maxWidth = '728px';
                $maxHeight = '90px';
            }

            if ($ad->ad_type == 'banner') {
                return '<a href="' . url($ad->url) . '" target="_blank" onclick="adView(' . $ad->id . ')">
                            <img src="' .  asset(Constant::WEBSITE_ADVERTISEMENT_IMAGE . '/' . $ad->image) . '" alt="advertisement" style="max-width: 100%;">
                        </a>';
            } else {
                return "<script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=" . $bs->google_adsense_publisher_id . "'
        crossorigin='anonymous'></script>
        <ins class='adsbygoogle'
              style='display:block'
              data-ad-client='" . $bs->adsense_publisher_id . "'
              data-ad-slot='" . $ad->ad_slot . "'
              data-ad-format='auto'
              data-full-width-responsive='true'></ins>
        <script>
              (adsbygoogle = window.adsbygoogle || []).push({});
        </script>";
            }
        } else {
            return;
        }
    }

    if (!function_exists('createInputName')) {
        function createInputName($string)
        {
            $inputName = preg_replace('/\s+/u', '_', trim($string));

            return mb_strtolower($inputName);
        }
    }
}

if (!function_exists('get_href')) {
    function get_href($data)
    {
        $link_href = '';

        if ($data->type == 'home') {
            $link_href = Route::has('frontend.user.index') ? route('frontend.user.index', getParam()) : '#';
        } else if ($data->type == 'services') {
            $link_href = route('frontend.services', getParam());
        } else if ($data->type == 'products') {
            $link_href = route('frontend.shop.products', getParam());
        } else if ($data->type == 'cart') {
            $link_href = route('frontend.shop.cart', getParam());
        } else if ($data->type == 'blog') {
            $link_href = route('frontend.blog', getParam());
        } else if ($data->type == 'faq') {
            $link_href = route('frontend.faq', getParam());
        } else if ($data->type == 'contact') {
            $link_href = route('frontend.contact', getParam());
        } else if ($data->type == 'custom') {
            /**
             * this menu has created using menu-builder from the admin panel.
             * this menu will be used as drop-down or to link any outside url to this system.
             */
            if ($data->href == '') {
                $link_href = '#';
            } else {

                $link_href = $data->href;
            }
        } else {
            // this menu is for the custom page which has been created from the admin panel.
            $link_href = route('frontend.dynamic_page', [getParam(), 'slug' => $data->type]);
        }

        return $link_href;
    }
}

if (!function_exists('paytabInfo')) {
    function paytabInfo($type, $user_id = null)
    {
        $paytabs = PaymentGateway::where('keyword', 'paytabs')->first();
        $paytabsInfo = json_decode($paytabs->information, true);
        if ($paytabsInfo['country'] == 'global') {
            $currency = 'USD';
        } elseif ($paytabsInfo['country'] == 'sa') {
            $currency = 'SAR';
        } elseif ($paytabsInfo['country'] == 'uae') {
            $currency = 'AED';
        } elseif ($paytabsInfo['country'] == 'egypt') {
            $currency = 'EGP';
        } elseif ($paytabsInfo['country'] == 'oman') {
            $currency = 'OMR';
        } elseif ($paytabsInfo['country'] == 'jordan') {
            $currency = 'JOD';
        } elseif ($paytabsInfo['country'] == 'iraq') {
            $currency = 'IQD';
        } else {
            $currency = 'USD';
        }
        return [
            'server_key' => $paytabsInfo['server_key'],
            'profile_id' => $paytabsInfo['profile_id'],
            'url'        => $paytabsInfo['api_endpoint'],
            'currency'   => $currency,
        ];
    }
}
