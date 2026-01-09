<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Home --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toW3cString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    {{-- Propriedades --}}
    @foreach($properties as $property)
        @foreach($property->contents as $content)
            @if($content->slug)
            <url>
                <loc>{{ route('front.property.detail', $content->slug) }}</loc>
                <lastmod>{{ $property->updated_at->toW3cString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
            @endif
        @endforeach
    @endforeach
    
    {{-- Páginas estáticas --}}
    <url>
        <loc>{{ url('/imoveis') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>{{ url('/contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
   {{-- Política de Devolução --}}
    <url>
        <loc>{{ url('/politica-de-devolucao') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    
    <url>
        <loc>{{ url('/return-policy') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    
    <url>
        <loc>{{ url('/politica-de-devolucion') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    
    {{-- FAQ --}}
    @if(Route::has('front.faq.view'))
    <url>
        <loc>{{ route('front.faq.view') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endif
    
    {{-- Pricing --}}
    @if(Route::has('front.pricing'))
    <url>
        <loc>{{ route('front.pricing') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    @endif
</urlset>
