@extends('front.layout')
@section('meta-description', 'Conheça nossos projetos imobiliários no Paraguai. Lançamentos, empreendimentos em construção e oportunidades de investimento.')

@section('pagename')
    - {{ __('Projetos') }}
@endsection

@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('Projetos'),
        'link' => __('Projetos'),
    ])

<section class="property-filter py-4" style="background: #f8f9fa;">
    <div class="container">
        <form method="GET" action="{{ route('front.projects') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Buscar projeto..." 
                           value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('front.projects') }}" class="btn btn-secondary w-100">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="project-list py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h5>{{ $total_projects }} projetos encontrados</h5>
            </div>
        </div>

        @if($projects->count() > 0)
            <div class="row">
                @foreach($projects as $project)
                    @php
                        $content = $project->contents->first();
                    @endphp
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div style="height:250px;overflow:hidden;">
                                <a href="{{ route('front.project.detail', $content->slug) }}">
                                    <img src="{{ asset('assets/img/projects/' . $project->featured_image) }}" 
                                         alt="{{ $content->title }}"
                                         class="card-img-top" 
                                         style="object-fit:cover;height:100%;width:100%;">
                                </a>
                            </div>
                            <div class="card-body">
                                <h5>
                                    <a href="{{ route('front.project.detail', $content->slug) }}" 
                                       class="text-dark text-decoration-none">
                                        {{ $content->title }}
                                    </a>
                                </h5>
                                <p class="text-muted small">
                                    {{ Str::limit(strip_tags($content->description), 120) }}
                                </p>
                                <a href="{{ route('front.project.detail', $content->slug) }}" 
                                   class="btn btn-primary btn-sm">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    {{ $projects->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h4>Nenhum projeto encontrado</h4>
                <a href="{{ route('front.projects') }}" class="btn btn-primary mt-3">
                    Ver Todos os Projetos
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
