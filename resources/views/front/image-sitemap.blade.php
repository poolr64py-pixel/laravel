{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach($properties as $property)
    @php
        $content = $property->contents->first();
    @endphp
    @if($content && $content->slug && $property->featured_image)
    <url>
        <loc>{{ route('front.property.detail', $content->slug) }}</loc>
        <image:image>
            <image:loc>{{ asset('assets/img/property/featureds/' . $property->featured_image) }}</image:loc>
            <image:title>{{ $content->title ?? 'Imóvel no Paraguai' }}</image:title>
            <image:caption>{{ Str::limit(strip_tags($content->description ?? ''), 200) }}</image:caption>
        </image:image>
        
        @php
            $gallery = DB::table('user_property_slider_images')
                ->where('property_id', $property->id)
                ->limit(10)
                ->get();
        @endphp
        @foreach($gallery as $img)
        <image:image>
            <image:loc>{{ asset('assets/img/property/slider-images/' . $img->image) }}</image:loc>
            <image:title>{{ $content->title }} - Foto {{ $loop->iteration }}</image:title>
        </image:image>
        @endforeach
    </url>
    @endif
@endforeach
</urlset>
