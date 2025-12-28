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
                    <li class="breadcrumb-item"><a href="{{ route('front.home') }}">Home</a></li>
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
                    <div class="main-image mb-3">
                        <a href="{{ asset('assets/img/projects/' . $project->featured_image) }}" data-lightbox="project-gallery">
                            <img src="{{ asset('assets/img/projects/' . $project->featured_image) }}" 
                                 alt="{{ $content ? $content->title : 'Project' }}" 
                                 class="img-fluid w-100 rounded"
                                 style="max-height: 500px; object-fit: cover; cursor: pointer;">
                        </a>
                    </div>
                    
                    @if($project->sliderImages && $project->sliderImages->count() > 0)
                        <div class="row g-2 mb-4">
                            @foreach($project->sliderImages as $image)
                                <div class="col-3">
                                    <a href="{{ asset('assets/img/projects/slider/' . $image->image) }}" data-lightbox="project-gallery">
                                        <img src="{{ asset('assets/img/projects/slider/' . $image->image) }}" 
                                             class="img-fluid rounded"
                                             style="height:120px;object-fit:cover;width:100%;cursor:pointer;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="project-info mb-4 p-4 bg-white rounded shadow-sm">
                    <h2 class="mb-3">{{ $content ? $content->title : 'Project' }}</h2>
                    
                    <div class="project-description">
                        <h4 class="mb-3">Descrição</h4>
                        <div class="content">
                            {!! nl2br(e($content ? $content->description : '')) !!}
                        </div>
                    </div>
                </div>
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
    
    const msg = `*Interesse em Projeto*\n\n*Nome:* ${name}\n*Email:* ${email}\n*Telefone:* ${phone}\n*Mensagem:* ${message}`;
    
    window.open(`https://wa.me/595994718400?text=${encodeURIComponent(msg)}`, '_blank');
}
</script>
@endsection
