@extends('front.layout')
@section('page-title', $seo->home_meta_title ?? 'Imóveis e Terrenos no Paraguai | Terras no Paraguay')

@section('meta-description', $seo->home_meta_description ?? 'Encontre os melhores imóveis no Paraguai. Mais de 50 propriedades: casas, apartamentos, terrenos e projetos. Invista com segurança!')

@section('meta-keywords', $seo->home_meta_keywords ?? 'imóveis paraguai, terrenos paraguai, casas venda paraguai, apartamentos paraguai, investir paraguai')
@section('content')

    {{-- HERO SECTION --}}
    @if (!empty($bs) && $bs->hero_section == 1)
        <section id="home" class="home-banner bg-img bg-cover header-next border-top"
            data-bg-image="{{ asset('assets/front/images/hero-bg.png') }}">
            <div class="container-fluid">
                <div class="row align-items-center gx-xl-5">
                    <div class="col-xl-6">
                        <div class="fluid-left">
                            <div class="content">
                                @if (!empty($be->hero_section_title) || !empty($be->hero_section_text))
                                    <h1 class="title" data-aos="fade-up" data-aos-delay="100">
                                        {{ $be->hero_section_title }}
                                    </h1>
                                    <p data-aos="fade-up" data-aos-delay="150">
                                        {{ $be->hero_section_text }}
                                    </p>
                                    <div class="btn-groups justify-content-center" data-aos="fade-up" data-aos-delay="200">
                                        @if ($be->hero_section_button_url)
                                            <a href="{{ $be->hero_section_button_url }}"
                                                title="{{ $be->hero_section_button_text }}"
                                                class="btn btn-lg btn-primary">{{ $be->hero_section_button_text }}</a>
                                        @endif
                                        @if ($be->hero_section_snd_btn_url)
                                            <a href="{{ $be->hero_section_snd_btn_url }}"
                                                title="{{ $be->hero_section_snd_btn_text }}"
                                                class="btn btn-lg btn-outline">{{ $be->hero_section_snd_btn_text }}</a>
                                        @endif
                                    </div>
                                @else
                                    <h1 class="title" data-aos="fade-up" data-aos-delay="100">
                                        {{ __('Encontre os Melhores Imóveis Aqui') }}
                                    </h1>
                                    <p data-aos="fade-up" data-aos-delay="150">
                                        {{ __('Descubra oportunidades únicas no mercado imobiliário paraguaio') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="hero-image" data-aos="fade-left" data-aos-delay="200">
                            <img src="{{ asset('assets/front/images/hero-img.png') }}" alt="Hero">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- FORMULÁRIO DE BUSCA --}}
    <section class="search-form" style="padding: 40px 0; background: #f8f9fa;">
        <div class="container">
            <div class="section-title title-center mb-4">
                <h2 class="title">Buscar Imóveis</h2>
            </div>
            <form method="GET" action="{{ route('front.properties') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control" placeholder="Buscar cidade ou título" value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-control">
                            <option value="">Tipo</option>
                            <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>Casa</option>
                            <option value="land" {{ request('type') == 'land' ? 'selected' : '' }}>Terreno</option>
                            <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartamento</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price_min" class="form-control" placeholder="Preço mínimo" value="{{ request('price_min') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price_max" class="form-control" placeholder="Preço máximo" value="{{ request('price_max') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- LISTAGEM DE IMÓVEIS --}}
     <section class="properties-list" style="padding: 60px 0;">
    <div class="container">
        <div class="section-title title-center mb-5">
            <h2 class="title">Imóveis Disponíveis</h2>
        </div>
        <div class="row">
    @forelse ($featured_properties as $property)
@php
       $content = $property->current_content;
       $cityContent = ($property->city && $property->city->cityContent) ? $property->city->cityContent->first() : null;
@endphp

@if($content)
<div class="col-md-4 mb-4">
    <div class="property-card shadow-sm" style="border-radius: 8px; overflow: hidden;">
        <img src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}"
             alt="{{ $content->title ?? 'Property' }}"
             style="width: 100%; height: 200px; object-fit: cover;">
        <div class="p-3">
            <h4 style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                {{ $content->title ?? 'Sem título' }}
            </h4>
            <p style="margin-bottom: 0.5rem; color: #666;">
                <i class="fas fa-map-marker-alt"></i> {{ $property->city_name ?? 'Localização não informada' }}
            </p>
            <strong style="color: #28a745; font-size: 1.3rem;">
                USD {{ number_format($property->price, 0, ',', '.') }}
            </strong>
            <div class="mt-3">
                @if(!empty($content->slug))
                    <a href="{{ route('front.property.detail', $content->slug) }}"
                       class="btn btn-primary btn-sm w-100">Ver Detalhes</a>
                @else
                    <a href="#" class="btn btn-secondary btn-sm w-100 disabled">Sem detalhes</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@empty
    <div class="col-12">
        <p class="text-center">Nenhum imóvel encontrado.</p>
    </div>
@endforelse
        </div>
    </div>
</section>

    {{-- AFTER HERO CUSTOM SECTIONS --}}
    @if (count($after_hero) > 0)
        @foreach ($after_hero as $customSec)
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

    {{-- PARTNERS SECTION --}}
    @if (!empty($bs) && !is_null($bs) && $bs->partners_section == 1)
        <section class="sponsor pt-120">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-center mb-50" data-aos="fade-up">
                            <span class="subtitle">{{ $bs->partner_title }} </span>
                            <h2 class="title">{{ $bs->partner_subtitle }} </h2>
                        </div>
                    </div>
                    <div class="col-12">
                        @if ($partners && $partners->isNotEmpty())
                            <div class="swiper sponsor-slider">
                                <div class="swiper-wrapper">
                                    @foreach ($partners as $partner)
                                        <div class="swiper-slide">
                                            <div class="item-single d-flex justify-content-center">
                                                <div class="sponsor-img">
                                                    <img class="lazyload blur-up"
                                                        src="{{ asset('assets/front/img/partners/' . $partner->image) }}"
                                                        alt="Sponsor">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination position-static mt-30" data-aos="fade-up"></div>
                            </div>
                        @else
                            <div>
                                <h4 class="text-center">{{ __('NO PARTNER FOUND') }}</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- WORK PROCESS SECTION - REMOVIDA SEÇÃO DE TEMPLATES --}}
    @if (!empty($bs) && $bs->work_process_section == 1)
    @endif

    {{-- INTRO SECTION --}}
    @if (!empty($bs) && $bs->intro_section == 1)
    @endif

    {{-- PRICING SECTION --}}
    @if (!empty($bs) && $bs->pricing_section == 1)
        <section class="pricing-area pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-center mb-50" data-aos="fade-up">
                            <span class="subtitle">{{ $bs->pricing_title }}</span>
                            <h2 class="title mb-2 mt-0">{{ $bs->pricing_subtitle }}</h2>
                            <p class="text">{{ $bs->pricing_text }}</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <x-front.sections.pricing :featured='true'/>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- TESTIMONIAL SECTION --}}
    @if (!empty($bs) && $bs->testimonial_section == 1)
        <section class="testimonial-area">
            <div class="container">
                <div class="row align-items-center gx-xl-5">
                    <div class="col-lg-6">
                        <div class="content mb-30" data-aos="fade-up">
                            <h2 class="title">{{ $bs->testimonial_title }}</h2>
                        </div>
                        @if ($testimonials && $testimonials->isNotEmpty())
                            <div class="swiper testimonial-slider mb-30" data-aos="fade-up">
                                <div class="swiper-wrapper">
@php
    $content = $property->contents ? $property->contents->first() : null;
    $cityContent = ($property->city && $property->city->cityContent) ? $property->city->cityContent->first() : null;
@endphp
                                    @foreach ($testimonials as $testimonial)
                                        <div class="swiper-slide">
                                            <div class="slider-item bg-primary-light">
                                                <div class="ratings justify-content-between">
                                                    <div class="rate">
                                                        <div class="rating-icon"
                                                            style="width:{{ $testimonial->rating * 20 }}%">
                                                        </div>
                                                    </div>
                                                    <span class="ratings-total">{{ $testimonial->rating }}
                                                        {{ __('star of') }}
                                                        {{ $totalTestomanials }}
                                                        {{ __('review') }}</span>
                                                </div>
                                                <div class="quote">
                                                    <p class="text mb-0">
                                                        {{ $testimonial->comment }}
                                                    </p>
                                                </div>
                                                <div class="client flex-wrap">
                                                    <div class="client-info d-flex align-items-center">
                                                        <div class="client-img">
                                                            <div class="lazy-container ratio ratio-1-1">
                                                                <img class="lazyload"
                                                                    data-src="{{ $testimonial->image }}"
                                                                    alt="Person Image">
                                                            </div>
                                                        </div>
                                                        <div class="content">
                                                            <h6 class="name">{{ $testimonial->name }}</h6>
                                                            <span class="designation">{{ $testimonial->occupation }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="icon"><i class="fas fa-quote-right"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @else
                            <h4 class="text-center">{{ __('NO TESTIMONIAL FOUND') }}</h4>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
{{-- SEÇÃO DE BLOG --}}
{{-- DEBUG: blogs = {{ isset($blogs) ? $blogs->count() : "NOT SET" }} | lang = {{ session("frontend_lang", "pt") }} --}}
@if(isset($blogs) && $blogs->count() > 0)
<section class="blog-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <div class="section-title title-center mb-5">
            <span class="subtitle">Últimas Notícias</span>
            <h2 class="title">Blog e Novidades</h2>
        </div>
        <div class="row">
{{--             <p>DEBUG: Lang = {{ session()->get("frontend_lang", "pt") }} | Blogs count = {{ count($blogs) }}</p> --}}
            @foreach($blogs as $blog)
@php
    $content = $property->contents ? $property->contents->first() : null;
    $cityContent = ($property->city && $property->city->cityContent) ? $property->city->cityContent->first() : null;
@endphp
            <div class="col-md-4 mb-4">
                <div class="blog-card shadow-sm" style="border-radius: 8px; overflow: hidden; background: white;">
                    @if($blog->main_image)
                    <img src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}" 
                         alt="{{ $blog->title }}" 
                         style="width: 100%; height: 200px; object-fit: cover;">
                    @endif
                    <div class="p-4">
                        <div class="mb-2" style="color: #666; font-size: 0.9rem;">
                            <i class="far fa-calendar"></i> {{ $blog->created_at->format('d/m/Y') }}
                        </div>
                        <h4 style="font-size: 1.3rem; margin-bottom: 1rem;">
                           {{ Str::limit($blog->title ?? 'Título indisponível', 60) }}
                        </h4>
                        <p style="color: #666; margin-bottom: 1.5rem;">
                            {{ Str::limit(strip_tags($blog->excerpt ?? $blog->content ?? 'Sem descrição disponível'), 100) }}
                        </p>
                        <a href="{{ route('front.blogdetails', ['slug' => $blog->slug, 'id' => $blog->id]) }}" 
                           class="btn btn-primary btn-sm">
                            Ler Mais <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('front.blogs') }}" class="btn btn-lg btn-outline-primary">
                Ver Todos os Posts
            </a>
        </div>
    </div>
</section>
@endif
@endsection
