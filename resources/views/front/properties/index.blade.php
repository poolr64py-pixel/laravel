@extends('front.layout')
@section('meta-description', 'Navegue por nossa seleção completa de imóveis no Paraguai: casas, apartamentos, terrenos comerciais e residenciais para venda e aluguel.')

@section('pagename')
    - {{ __('Imóveis') }}
@endsection

@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('Imóveis'),
        'link' => __('Imóveis'),
    ])

<!-- Filtros de Busca -->
<section class="property-filter py-4" style="background: #f8f9fa;">
    <div class="container">
        <form method="GET" action="{{ route('front.properties') }}">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Buscar por cidade ou título" 
                           value="{{ request('q') }}">
                </div>

                <div class="col-lg-2 col-md-6">
                    <select name="type" class="form-control">
                        <option value="">Tipo</option>
                        <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>Casa</option>
                        <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartamento</option>
                        <option value="land" {{ request('type') == 'land' ? 'selected' : '' }}>Terreno</option>
                        <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Comercial</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <select name="purpose" class="form-control">
                        <option value="">Finalidade</option>
                        <option value="sale" {{ request('purpose') == 'sale' ? 'selected' : '' }}>Venda</option>
                        <option value="rent" {{ request('purpose') == 'rent' ? 'selected' : '' }}>Aluguel</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <input type="number" name="price_min" class="form-control" 
                           placeholder="Preço mín." 
                           value="{{ request('price_min') }}">
                </div>

                <div class="col-lg-2 col-md-6">
                    <input type="number" name="price_max" class="form-control" 
                           placeholder="Preço máx." 
                           value="{{ request('price_max') }}">
                </div>

                <div class="col-lg-1 col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="#" class="btn btn-sm btn-link" onclick="toggleFilters(event)">
                    Filtros Avançados <i class="fas fa-chevron-down"></i>
                </a>
                @if(request()->hasAny(['q', 'type', 'purpose', 'price_min', 'price_max']))
                    <a href="{{ route('front.properties') }}" class="btn btn-sm btn-secondary">
                        Limpar Filtros
                    </a>
                @endif
            </div>

            <div id="advanced-filters" style="display: none;" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="beds" class="form-control">
                            <option value="">Quartos</option>
                            <option value="1" {{ request('beds') == '1' ? 'selected' : '' }}>1+</option>
                            <option value="2" {{ request('beds') == '2' ? 'selected' : '' }}>2+</option>
                            <option value="3" {{ request('beds') == '3' ? 'selected' : '' }}>3+</option>
                            <option value="4" {{ request('beds') == '4' ? 'selected' : '' }}>4+</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="bath" class="form-control">
                            <option value="">Banheiros</option>
                            <option value="1" {{ request('bath') == '1' ? 'selected' : '' }}>1+</option>
                            <option value="2" {{ request('bath') == '2' ? 'selected' : '' }}>2+</option>
                            <option value="3" {{ request('bath') == '3' ? 'selected' : '' }}>3+</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="area_min" class="form-control" 
                               placeholder="Área mín. (m²)" 
                               value="{{ request('area_min') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="area_max" class="form-control" 
                               placeholder="Área máx. (m²)" 
                               value="{{ request('area_max') }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Resultados -->
<section class="property-list py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>{{ $total_properties }} imóveis encontrados</h5>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('front.properties') }}" class="d-inline-block">
                    @foreach(request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mais Recentes</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Mais Antigos</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
                    </select>
                </form>
            </div>
        </div>

        @if($properties->count() > 0)
            <div class="row">
                @foreach($properties as $property)
                  
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="property-card shadow-sm rounded overflow-hidden h-100">
                            <div class="property-image position-relative">
                                <a href="{{ $property->url }}">
                                    <img src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}"
                                         alt="{{ $property->current_content?->title ?? 'Property' }}" 
                                         class="img-fluid w-100"
                                         style="height: 250px; object-fit: cover;">
                                </a>
                                <div class="position-absolute top-0 start-0 p-2">
                                    @if($property->featured)
                                        <span class="badge bg-warning text-dark">⭐ Destaque</span>
                                    @endif
                                    <span class="badge bg-primary ms-1">{{ ucfirst($property->purpose) }}</span>
                                </div>
                            </div>
                            <div class="property-content p-3">
                                <div class="property-price mb-2">
                                    <h5 class="text-primary mb-0">USD {{ number_format($property->price, 0, ',', '.') }}</h5>
                                </div>
                                <h6 class="property-title">
                                    <a href="{{ route('front.property.detail', $property->current_content->slug) }}" class="text-decoration-none text-dark">
                                        {{ $property->current_content?->title ?? 'No title' }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $property->city_name ?? 'N/A' }}
                                </p>
                                <div class="property-features d-flex gap-3 pt-3 border-top">
                                    @if($property->beds)
                                        <span class="small"><i class="fas fa-bed text-primary"></i> {{ $property->beds }}</span>
                                    @endif
                                    @if($property->bath)
                                        <span class="small"><i class="fas fa-bath text-primary"></i> {{ $property->bath }}</span>
                                    @endif
                                    @if($property->area)
                                        <span class="small"><i class="fas fa-ruler-combined text-primary"></i> {{ $property->area }}m²</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-12">
                    {{ $properties->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-home fa-3x text-muted mb-3"></i>
                <h4>Nenhum imóvel encontrado</h4>
                <p>Tente ajustar os filtros de busca</p>
                <a href="{{ route('front.properties') }}" class="btn btn-primary">Ver Todos os Imóveis</a>
            </div>
        @endif
    </div>
</section>

<script>
function toggleFilters(e) {
    e.preventDefault();
    const filters = document.getElementById('advanced-filters');
    const icon = e.target.querySelector('i');
    if(filters.style.display === 'none') {
        filters.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        filters.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endsection
