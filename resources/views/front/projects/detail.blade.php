@extends('front.layout')

@php
    $content = $project->contents->first();
@endphp

@section('content')
<div class="page-title-area bg-primary-light">
    <div class="container">
        <div class="content text-center">
            <h2>{{ $content ? $content->title : 'Project' }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('front.projects') }}">Projetos</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($content ? $content->title : 'Project', 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="project-detail ptb-90">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
      <div class="project-gallery mb-4">
    @if($project->sliderImages && $project->sliderImages->count() > 0)
        <!-- Main Swiper Carousel -->
        <div class="swiper project-swiper-main mb-3" style="border-radius: 10px; overflow: hidden;">
            <div class="swiper-wrapper">
                @foreach($project->sliderImages as $image)
                    <div class="swiper-slide">
                        <img src="{{ asset('assets/img/projects/' . $image->image) }}"
                             alt="Gallery Image"
                             class="w-100"
                             style="max-height: 500px; object-fit: cover;">
                    </div>
                @endforeach
            </div>
            <!-- Navigation buttons -->
            <div class="swiper-button-next" style="color: #fff; background: rgba(0,0,0,0.5); width: 40px; height: 40px; border-radius: 50%;"></div>
            <div class="swiper-button-prev" style="color: #fff; background: rgba(0,0,0,0.5); width: 40px; height: 40px; border-radius: 50%;"></div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- Thumbnail Swiper -->
        <div class="swiper project-swiper-thumbs">
            <div class="swiper-wrapper">
                @foreach($project->sliderImages as $image)
                    <div class="swiper-slide" style="cursor: pointer;">
                        <img src="{{ asset('assets/img/projects/' . $image->image) }}"
                             class="img-fluid rounded"
                             style="height: 80px; object-fit: cover; width: 100%;">
                    </div>
                @endforeach
            </div>
        </div>

        <style>
            .project-swiper-thumbs .swiper-slide {
                opacity: 0.5;
                transition: opacity 0.3s;
            }
            .project-swiper-thumbs .swiper-slide-thumb-active {
                opacity: 1;
                border: 2px solid #007bff;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Thumbnail swiper
                var swiperThumbs = new Swiper('.project-swiper-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        320: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 }
                    }
                });

                // Main swiper
                var swiperMain = new Swiper('.project-swiper-main', {
                    spaceBetween: 10,
                    loop: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    thumbs: {
                        swiper: swiperThumbs,
                    },
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                    },
                });
            });
        </script>
    @else
        <!-- Fallback: featured image -->
        <div class="main-image mb-3">
            <img src="{{ asset('assets/img/projects/' . $project->featured_image) }}"
                 alt="{{ $content ? $content->title : 'Project' }}"
                 class="img-fluid w-100 rounded"
                 style="max-height: 500px; object-fit: cover;">
        </div>
    @endif
</div>
                <div class="project-info mb-4 p-4 bg-white rounded shadow-sm">
                    <h2 class="mb-3">{{ $content ? $content->title : 'Project' }}</h2>
                    
                    <div class="project-description">
                        <h4 class="mb-3">Descrição</h4>
                        <div class="content">
                            {!! $content ? $content->description : '' !!}
                        </div>
                    </div>
                </div>

                {{-- TOUR VIRTUAL 360° --}}
                @if(!empty($project->virtual_tour_url))
                <div class="tour-virtual-section mb-4 p-4 bg-white rounded shadow-sm">
                    <h4 class="mb-3">
                        <i class="fas fa-vr-cardboard"></i> {{ __("Tour Virtual 360°") }}
                    </h4>
                    <div style="position: relative; height: 500px; overflow: hidden; border-radius: 10px;">
                        <iframe
                            src="{{ $project->virtual_tour_url }}"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                            allowfullscreen
                            allow="xr-spatial-tracking; gyroscope; accelerometer">
                        </iframe>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="contact-card card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Interessado neste projeto?</h5>
                        <form action="#" method="POST" onsubmit="event.preventDefault(); sendWhatsApp(this); return false;">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            
                            <div class="mb-3">
                                <input type="text" name="name" class="form-control" 
                                       placeholder="Seu Nome" required>
                            </div>
                            
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" 
                                       placeholder="Seu Email" required>
                            </div>
                            
                            <div class="mb-3">
                                <input type="tel" name="phone" class="form-control" 
                                       placeholder="Seu Telefone">
                            </div>
                            
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4" 
                                          placeholder="Mensagem" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>

                <div class="share-card card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Compartilhar</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                               target="_blank" class="btn btn-primary flex-fill">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($content ? $content->title : 'Project' . ' - ' . url()->current()) }}" 
                               target="_blank" class="btn btn-success flex-fill">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject={{ urlencode($content ? $content->title : 'Project') }}&body={{ urlencode(url()->current()) }}" 
                               class="btn btn-secondary flex-fill">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($related_projects && $related_projects->count() > 0)
            <div class="related-projects mt-5">
                <h3 class="mb-4">Projetos Relacionados</h3>
                <div class="row">
                    @foreach($related_projects as $relatedProject)
                        @php
                            $relatedContent = $relatedProject->contents->first();
                        @endphp
                        @if($relatedContent)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div style="height:200px;overflow:hidden;">
                                    <a href="{{ route('front.project.detail', $relatedContent->slug) }}">
                                        <img src="{{ asset('assets/img/projects/' . $relatedProject->featured_image) }}" 
                                             class="card-img-top" 
                                             style="object-fit:cover;height:100%;width:100%;">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h6>
                                        <a href="{{ route('front.project.detail', $relatedContent->slug) }}" 
                                           class="text-dark text-decoration-none">
                                            {{ Str::limit($relatedContent->title, 40) }}
                                        </a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
          {{-- Mapa de Localização --}}
    @if($project->latitude && $project->longitude)
        <div class="project-location mt-5">
            <h3 class="mb-4">{{ __('Localização') }}</h3>
            <div class="map-container" style="position: relative; padding-bottom: 400px; height: 0; overflow: hidden; border-radius: 8px;">
                <iframe 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                    src="https://maps.google.com/maps?q={{ $project->latitude }},{{ $project->longitude }}&hl=pt&z=14&output=embed"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    @endif
        <script>
   function sendWhatsApp(form) {
    const name = form.querySelector('input[name="name"]').value;
    const email = form.querySelector('input[name="email"]').value;
    const phone = form.querySelector('input[name="phone"]').value;
    const message = form.querySelector('textarea[name="message"]').value;
    const title = document.querySelector('h2')?.textContent || 'Projeto';
    const url = window.location.href;
    const msg = `*Interesse em Projeto*\n\n*Projeto:* ${title}\n*Link:* ${url}\n\n*Nome:* ${name}\n*Email:* ${email}\n*Telefone:* ${phone}\n*Mensagem:* ${message}`;
    window.open(`https://wa.me/595994718400?text=${encodeURIComponent(msg)}`, '_blank');
}
</script>
@endsection
