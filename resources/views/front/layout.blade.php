<!DOCTYPE html>
<html lang="en" @if ($rtl == 1) dir="rtl" @endif>

<head>
    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    {{-- SEO Básico --}}
    <title>@yield('page-title', 'Imóveis e Terrenos no Paraguai | Terras no Paraguay - Casas, Apartamentos e Projetos')</title>
    <meta name="description" content="@yield('meta-description', 'Encontre os melhores imóveis e terrenos no Paraguai. Casas, apartamentos, terrenos e projetos para venda e aluguel. Oportunidades únicas com a Terras no Paraguay!')">
    <meta name="keywords" content="@yield('meta-keywords', 'imóveis paraguai, terrenos paraguai, casas paraguai, apartamentos paraguai, investir paraguai')">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- Hreflang para SEO multi-idioma --}}
    <link rel="alternate" hreflang="pt" href="{{ url('/changelanguage/pt') }}">
    <link rel="alternate" hreflang="en" href="{{ url('/changelanguage/en') }}">
    <link rel="alternate" hreflang="es" href="{{ url('/changelanguage/es') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">    
   {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
@if(isset($currentContent) && isset($property))
    {{-- Página de propriedade específica --}}
    <meta property="og:title" content="{{ $currentContent->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($currentContent->description ?? ''), 200) }}">
    <meta property="og:image" content="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}">
    <meta property="og:type" content="article">
@else
    {{-- Home e outras páginas --}}
    <meta property="og:title" content="@yield('page-title', 'Imóveis e Terrenos no Paraguai | Terras no Paraguay')">
    <meta property="og:description" content="@yield('meta-description', 'Encontre os melhores imóveis no Paraguai. Casas, apartamentos, terrenos e projetos com a Terras no Paraguay!')">
    <meta property="og:image" content="{{ asset('assets/front/img/logo.png') }}">
    <meta property="og:type" content="website">
@endif
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ session('frontend_lang') == 'pt' ? 'pt_BR' : (session('frontend_lang') == 'en' ? 'en_US' : 'es_ES') }}">
<meta property="og:locale:alternate" content="pt_BR">
<meta property="og:locale:alternate" content="en_US">
<meta property="og:locale:alternate" content="es_ES">
<meta property="og:site_name" content="Terras no Paraguay">    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
      @if(isset($currentContent))
    <meta name="twitter:title" content="{{ $currentContent->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($currentContent->description ?? ''), 200) }}">
@else
    <meta name="twitter:title" content="@yield('page-title', 'Imóveis e Terrenos no Paraguai | Terras no Paraguay')">
    <meta name="twitter:description" content="@yield('meta-description', 'Encontre os melhores imóveis no Paraguai')">
@endif
     @if(isset($currentContent))
        <meta name="twitter:image" content="{{ asset('assets/img/property/featureds/' . ($property->featured_image ?? 'default.jpg')) }}">
    @else
        <meta name="twitter:image" content="{{ asset('assets/front/img/logo.png') }}">
    @endif
      <!--====== Title ======-->
<link rel="icon" href="{{ !empty($bs) && !empty($bs->favicon) ? asset('assets/front/img/' . $bs->favicon) : asset('assets/front/img/favicon.png') }}">    



    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.min.css') }}">
    <!-- Fontawesome Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/fonts/fontawesome/css/all.min.css') }}">
    <!-- Kreativ Icon -->
    <link rel="stylesheet" href="{{ asset('assets/front/fonts/icomoon/style.css') }}">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/magnific-popup.min.css') }}">
    <!-- Swiper Slider -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/swiper-bundle.min.css') }}">
    <!-- AOS Animation CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/aos.min.css') }}">
    <!-- Nice Select -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/floating-whatsapp.css') }}">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/summernote-content.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/style.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/cookie-alert.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    @endif
    <!-- base color change -->

    @yield('styles')

    @if (!empty($bs) && ($bs->is_whatsapp == 1 || $bs->is_tawkto == 1))
        <style>
            .go-top {
                right: auto;
                left: 30px;
            }
        </style>
    @endif

    <style>
        :root {
            --color-primary: #{{ !empty($bs) && !empty($bs->base_color) ? $bs->base_color : '000000' }};
            --color-primary-shade: #{{ !empty($bs) && !empty($bs->base_color2) ? $bs->base_color2 : "000000" }};
            --bg-light: #{{ !empty($bs) && !empty($bs->base_color2) ? $bs->base_color2 : "000000" }}14;
        }
    </style>

    {{-- Google Analytics --}}
    @if(!empty($bs->google_analytics_id))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $bs->google_analytics_id }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag("js", new Date());
      gtag("config", "{{ $bs->google_analytics_id }}");
    </script>
    @endif
    <link rel="stylesheet" href="{{ asset('assets/front/css/properties-custom.css') }}">
      {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "RealEstateAgent",
        "name": "Terras no Paraguay",
        "description": "Imóveis e terrenos no Paraguai - Casas, apartamentos e projetos",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('assets/front/img/logo.png') }}",
        "telephone": "+595-994-718400",
        "email": "{{ $bs->support_email ?? 'contato@terrasnoparaguay.com' }}",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "PY",
            "addressLocality": "Asunción"
        },
        "sameAs": [
            "https://www.facebook.com/terrasnoparaguay",
            "https://www.instagram.com/terrasnoparaguay"
        ],
        "areaServed": {
            "@type": "Country",
            "name": "Paraguay"
        }
    }
    </script>    
</head>

<body>


    @if (!empty($bs) && $bs->preloader_status == 1)
        <div id="preLoader">
            <div class="loader">

                <img src="{{ asset('assets/front/img/' . $bs->preloader) }}" alt="">
            </div>
        </div>
    @endif

    @if (!request()->routeIs('user.login') && !request()->routeIs('front.register.view'))
        @include('front.partials.header')
    @endif

    @yield('content')
    @if (!request()->routeIs('user.login') && !request()->routeIs('front.register.view'))
        {{-- footer section --}}
        @includeIf('front.partials.footer')
    @endif
    <div class="go-top"><i class="fal fa-angle-double-up"></i></div>

    @if ($be?->cookie_alert_status == 1)
        <div class="cookie">
            @include('front.cookie-alert.index')
        </div>
    @endif

    <!-- Magic Cursor -->
    <div class="cursor"></div>
    <!-- Magic Cursor -->

    {{-- WhatsApp Chat Button --}}
    <div id="WAButton"></div>

    <script>
        "use strict";
        var showMore = "{{ __('Show More') }} +";
        var showLess = "{{ __('Show Less') }} -";
        var demo_mode = "{{ env('DEMO_MODE') }}";
    </script>

    <!-- Jquery JS -->
    <script src="{{ asset('/assets/front/js/jquery.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('/assets/front/js/bootstrap.min.js') }}"></script>
    <!-- Nice Select JS -->
    <script src="{{ asset('/assets/front/js/jquery.nice-select.min.js') }}"></script>
    {{-- svg loader  --}}
    <script src="{{ asset('/assets/front/js/svg-loader.min.js') }}"></script>
    <!-- Magnific Popup JS -->
    <script src="{{ asset('/assets/front/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Toastr JS -->
    <script src=" {{ asset('/assets/tenant-front/js/floating-whatsapp.js') }}"></script>
    <script src="{{ asset('/assets/front/js/toastr.min.js') }}"></script>
    <!-- Swiper Slider JS -->
    <script src="{{ asset('/assets/front/js/swiper-bundle.min.js') }}"></script>
    <!-- Lazysizes -->
    <script src="{{ asset('/assets/front/js/lazysizes.min.js') }}"></script>
    <!-- AOS JS -->
    <script src="{{ asset('/assets/front/js/aos.min.js') }}"></script>
    <!-- Main script JS -->
    <script src="{{ asset('/assets/front/js/script.js') }}"></script>

    <script>
        "use strict";
        var rtl = {{ $rtl }};
    </script>

    @yield('scripts')

    @yield('vuescripts')


    @if (session()->has('success'))
        <script>
            "use strict";
            toastr['success']("{{ __(session('success')) }}");
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            "use strict";
            toastr['error']("{{ __(session('error')) }}");
        </script>
    @endif

    @if (session()->has('warning'))
        <script>
            "use strict";
            toastr['warning']("{{ __(session('warning')) }}");
        </script>
    @endif
    <script>
        "use strict";

        function handleSelect(elm) {
            window.location.href = window.location.origin + "/changelanguage/" + elm.value;
        }
    </script>

    {{-- whatsapp init code --}}

    @if (!empty($bs) && $bs->is_whatsapp == 1)
        <script type="text/javascript">
            "use strict";
            var whatsapp_popup = {{ !empty($bs) ? $bs->whatsapp_popup : 0 }};
            var whatsappImg = "{{ asset('assets/front/img/whatsapp.svg') }}";

            $(function() {
                $('#WAButton').floatingWhatsApp({
                    phone: "{{ !empty($bs) ? $bs->whatsapp_number : "" }}",
                    headerTitle: "{{ !empty($bs) ? $bs->whatsapp_header_title : "" }}",
                    popupMessage: `{!! !empty($bs->whatsapp_popup_message) ? nl2br($bs->whatsapp_popup_message) : '' !!}`,
                    showPopup: whatsapp_popup == 1 ? true : false,
                    buttonImage: '<img src="' + whatsappImg + '" />',
                    position: "right"

                });
            });
        </script>
    @endif

    @if (!empty($bs) && $bs->is_tawkto == 1)
        @php
            $directLink = str_replace('tawk.to', 'embed.tawk.to', $bs->tawkto_chat_link);
            $directLink = str_replace('chat/', '', $directLink);
        @endphp
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            "use strict";
            var Tawk_API = Tawk_API || {},
                Tawk_LoadStart = new Date();
            (function() {
                var s1 = document.createElement("script"),
                    s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = '{{ $directLink }}';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        </script>
        <!--End of Tawk.to Script-->
    @endif

    <script>console.log("CARREGANDO MENU FIX...");</script>
    <script src="{{ asset('assets/front/js/mobile-menu-fix.js') }}"></script>
    <script src="{{ asset('assets/front/js/menu-mobile-simple.js') }}"></script>
</body>

</html>
