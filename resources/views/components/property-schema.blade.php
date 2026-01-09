@php
    $propertyType = match(strtolower($property->type ?? 'residential')) {
        'commercial' => 'CommercialRealEstate',
        'apartment' => 'Apartment',
        'house' => 'House',
        default => 'RealEstateListing'
    };
    
    $offerType = strtolower($property->purpose ?? 'sale') === 'rent' ? 'https://schema.org/Rent' : 'https://schema.org/Sale';
    $cleanDescription = strip_tags($content->description ?? '');
    $cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
    $cleanDescription = trim(substr($cleanDescription, 0, 300));
    $imageUrl = $property->featured_image ? asset('assets/img/property/featureds/' . $property->featured_image) : asset('assets/img/default-property.jpg');
    $currency = $currencyInfo->base_currency_text ?? 'USD';
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "{{ $propertyType }}",
  "name": {{ json_encode($content->title, JSON_UNESCAPED_UNICODE) }},
  "description": {{ json_encode($cleanDescription, JSON_UNESCAPED_UNICODE) }},
  "url": "{{ url()->current() }}",
  "image": ["{{ $imageUrl }}"],
  "address": {
    "@type": "PostalAddress",
    "streetAddress": {{ json_encode($content->address ?? '', JSON_UNESCAPED_UNICODE) }},
    "addressLocality": {{ json_encode(optional(optional($property->city)->getContent($language->id))->name ?? '', JSON_UNESCAPED_UNICODE) }},
"addressRegion": {{ json_encode(optional(optional($property->state)->getContent($language->id))->name ?? '', JSON_UNESCAPED_UNICODE) }},
"addressCountry": {{ json_encode(optional(optional($property->country)->getContent($language->id))->name ?? '', JSON_UNESCAPED_UNICODE) }}
  },
  @if($property->latitude && $property->longitude)
  "geo": {"@type": "GeoCoordinates", "latitude": "{{ $property->latitude }}", "longitude": "{{ $property->longitude }}"},
  @endif
  "offers": {
    "@type": "Offer",
    "priceCurrency": "{{ $currency }}",
    "price": "{{ number_format($property->price, 2, '.', '') }}",
    "availability": "https://schema.org/InStock"
  },
  @if($property->beds)"numberOfBedrooms": "{{ $property->beds }}",@endif
  @if($property->bath)"numberOfBathroomsTotal": "{{ $property->bath }}",@endif
  @if($property->area)"floorSize": {"@type": "QuantitativeValue", "value": "{{ $property->area }}", "unitCode": "MTK"}@endif
}
</script>
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $content->title }}">
<meta property="og:description" content="{{ Str::limit($cleanDescription, 155) }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $content->title }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
<link rel="canonical" href="{{ url()->current() }}">
