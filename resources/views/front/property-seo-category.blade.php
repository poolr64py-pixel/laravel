@extends('front.layout')

@section('page-title', $pageConfig['title'])
@section('meta-description', $pageConfig['description'])

@section('pagename')
    - {{ $pageConfig['h1'] }}
@endsection

@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => $pageConfig['h1'],
        'link' => 'Imóveis',
    ])

    <div class="property-listing-area pt-120 pb-90">
        <div class="container">
            <!-- Texto SEO -->
            <div class="row mb-5">
                <div class="col-lg-12">
                    <h1 class="mb-3">{{ $pageConfig['h1'] }}</h1>
                    <p class="lead">{{ $pageConfig['description'] }}</p>
                </div>
            </div>

            <!-- Listagem de imóveis -->
            <div class="row">
                @forelse($properties as $property)
                    @php
                        $content = $property->contents->first();
                    @endphp
                    
                    @if($content)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $content->title }}"
                                 style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">{{ $content->title }}</h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt"></i> {{ $content->address }}
                                </p>
                                <p class="text-primary fw-bold">
                                    {{ $property->currency }} {{ number_format($property->price, 0, ',', '.') }}
                                </p>
                                <div class="property-features">
                                    @if($property->beds > 0)
                                        <span><i class="fas fa-bed"></i> {{ $property->beds }}</span>
                                    @endif
                                    @if($property->bath > 0)
                                        <span><i class="fas fa-bath"></i> {{ $property->bath }}</span>
                                    @endif
                                    @if($property->area > 0)
                                        <span><i class="fas fa-ruler-combined"></i> {{ $property->area }}m²</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ url('/imoveis/' . $content->slug) }}" 
                                   class="btn btn-primary btn-sm w-100">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Não encontramos imóveis nesta categoria no momento. 
                            <a href="{{ route('front.properties') }}">Ver todos os imóveis disponíveis</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if($properties->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    {{ $properties->links() }}
                </div>
            </div>
            @endif

            <!-- CTA WhatsApp -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="alert alert-success text-center">
                        <h4>Precisa de ajuda para encontrar o imóvel ideal?</h4>
                        <p>Fale conosco pelo WhatsApp</p>
                        <a href="https://wa.me/595994718400?text=Olá, vi os imóveis no site e gostaria de mais informações" 
                           class="btn btn-success btn-lg" 
                           target="_blank">
                            <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
