@php
    $cleanDescription = trim(substr(preg_replace('/\s+/', ' ', strip_tags($content->description ?? '')), 0, 300));
    $imageUrl = $project->featured_image ? asset('assets/img/project/featured/' . $project->featured_image) : asset('assets/img/default-project.jpg');
    $currency = $currencyInfo->base_currency_text ?? 'USD';
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": {{ json_encode($content->title, JSON_UNESCAPED_UNICODE) }},
  "description": {{ json_encode($cleanDescription, JSON_UNESCAPED_UNICODE) }},
  "url": "{{ url()->current() }}",
  "image": ["{{ $imageUrl }}"],
  "brand": {"@type": "Organization", "name": "{{ $bs->website_title ?? config('app.name') }}"},
  "offers": {
    "@type": "AggregateOffer",
    "priceCurrency": "{{ $currency }}",
    @if($project->price_from && $project->price_to)
    "lowPrice": "{{ number_format($project->price_from, 2, '.', '') }}",
    "highPrice": "{{ number_format($project->price_to, 2, '.', '') }}",
    @else
    "price": "{{ number_format($project->price ?? 0, 2, '.', '') }}",
    @endif
    "availability": "https://schema.org/InStock"
  }
}
</script>
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $content->title }}">
<meta property="og:description" content="{{ Str::limit($cleanDescription, 155) }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="{{ url()->current() }}">
