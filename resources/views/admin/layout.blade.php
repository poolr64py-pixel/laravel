<!DOCTYPE html>
<html lang="{{ !empty($currentLang) ? $currentLang->code : 'en' }}" @if (!empty($currentLang) && $currentLang->rtl == 1) dir="rtl" @endif>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <title>{{ $bs->website_title }} - {{ __('Admin') }}</title>
    <link rel="icon" href="{{ asset('assets/front/img/' . $bs->favicon) }}">
    @includeif('admin.partials.styles')
   

    @yield('styles')
    @if (!empty($currentLang) && $currentLang->rtl == 1)
        <!--====== RTL Style css ======-->
        <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-rtl.css') }}">
        <style>
            #editModal form input,
            #editModal form textarea,
            #editModal form select {
                direction: rtl;
            }

            #editModal form .note-editor.note-frame .note-editing-area .note-editable {
                direction: rtl;
                text-align: right;
            }
        </style>
    @else
        <style>
            .navbar-expand-lg .navbar-nav .dropdown-menu {
                left: auto;
                right: 0;
            }
        </style>
    @endif

</head>
<body @if (request()->cookie('admin-theme') == 'dark') data-background-color="dark" @endif>
    <div class="wrapper">

        {{-- top navbar area start --}}
        @includeif('admin.partials.top-navbar')
        {{-- top navbar area end --}}


        {{-- side navbar area start --}}
        @includeif('admin.partials.side-navbar')
        {{-- side navbar area end --}}


        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>
            @includeif('admin.partials.footer')
        </div>

    </div>

    @includeif('admin.partials.scripts')
    @yield('scripts')
    @stack('scripts')

 
    <div class="request-loader">
        <img src="{{ asset('assets/admin/img/loader.gif') }}" alt="">
    </div> 
<script>
$(document).ready(function() {
    setTimeout(function() {
        $('.sidebar').find('a').each(function() {
            var href = $(this).attr('href');
            if (href && href !== '#' && !$(this).attr('data-toggle')) {
                $(this).off('click');
                $(this).on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.href = $(this).attr('href');
                    return false;
                });
            }
        });
    }, 1000);
});
</script>
<script>
var imgupload = "{{ route('admin.summernote.upload') }}";
</script>
<script>console.log("jQuery carregado:", typeof jQuery);</script>
</body>

</html>
