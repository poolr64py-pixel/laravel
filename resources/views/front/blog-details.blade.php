@extends('front.layout')

@section('pagename')
    - {{ __('Blog Details') }}
@endsection

@section('meta-description', !empty($blog) ? $blog->meta_keywords : '')
@section('meta-keywords', !empty($blog) ? $blog->meta_description : '')

@section('og-meta')
    <meta property="og:image" content="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">
@endsection

@php
    // Detectar idioma atual
    $currentLangCode = session()->get('frontend_lang', 'pt');
    if (!in_array($currentLangCode, ['pt', 'en', 'es'])) {
        $currentLangCode = 'pt';
    }
    
    // Traduzir conteúdo se não for português
    if ($currentLangCode !== 'pt') {
        $translatedTitle = \App\Helpers\TranslateHelper::translate($blog->title, 'pt', $currentLangCode);
        $translatedContent = \App\Helpers\TranslateHelper::translate(strip_tags($blog->content), 'pt', $currentLangCode);
    } else {
        $translatedTitle = $blog->title;
        $translatedContent = $blog->content;
    }
@endphp

@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => $translatedTitle,
        'link' => __('Blog Details'),
    ])

    <div class="blog-details-area pt-120 pb-90">
        <div class="container">
            <div class="row justify-content-center gx-xl-5">
                <div class="col-lg-8">
                    <div class="blog-description mb-50">
                        <article class="item-single">
                            <div class="image">
                                <div class="lazy-container ratio-16-9">
                                    <img class="lazyload lazy-image"
                                        src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                        data-src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                        alt="{{ $translatedTitle }}">
                                </div>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                                    class="btn btn-lg btn-primary"><i class="fas fa-share-alt"></i>{{ __('Share') }}</a>
                            </div>
                            <div class="content">
                                <ul class="info-list">
                                    <li><i class="fal fa-user"></i>{{ __('Admin') }}</li>
                                    <li> <i class="fal fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($blog->created_at)->locale(app()->getLocale())->translatedFormat('d F, Y') }}
                                    </li>
                                    <li><i class="fal fa-tag"></i>{{ $blog->bcategory->name }}</li>
                                </ul>
                                <h4 class="title">{{ $translatedTitle }}</h4>
                                
                                <div class="summernote-content">
                                    @if($currentLangCode !== 'pt')
                                        {!! nl2br(e($translatedContent)) !!}
                                    @else
                                        {!! replaceBaseUrl($blog->content, 'summernote') !!}
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
                <div class="col-lg-4">
                    @includeIf('front.partials.blog-sidebar')
                </div>
            </div>
        </div>
    </div>

    {{-- share on social media modal --}}
    <div class="modal fade" id="socialMediaModal" tabindex="-1" role="dialog" aria-labelledby="socialMediaModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Share On') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="social-links">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                           target="_blank" class="btn btn-primary"><i class="fab fa-facebook-f"></i> Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($translatedTitle) }}" 
                           target="_blank" class="btn btn-info"><i class="fab fa-twitter"></i> Twitter</a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                           target="_blank" class="btn btn-primary"><i class="fab fa-linkedin-in"></i> LinkedIn</a>
                        <a href="https://wa.me/?text={{ urlencode($translatedTitle . ' ' . url()->current()) }}" 
                           target="_blank" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
