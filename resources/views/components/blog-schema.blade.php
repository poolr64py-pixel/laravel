@php
    $cleanDescription = trim(substr(preg_replace('/\s+/', ' ', strip_tags($content->content ?? '')), 0, 500));
    $imageUrl = $blog->featured_image ? asset('assets/img/blog/' . $blog->featured_image) : asset('assets/img/default-blog.jpg');
    $authorName = $blog->author ?? $bs->website_title ?? config('app.name');
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": {{ json_encode($content->title, JSON_UNESCAPED_UNICODE) }},
  "description": {{ json_encode(Str::limit($cleanDescription, 300), JSON_UNESCAPED_UNICODE) }},
  "image": ["{{ $imageUrl }}"],
  "datePublished": "{{ $blog->created_at->toIso8601String() }}",
  "dateModified": "{{ $blog->updated_at->toIso8601String() }}",
  "author": {"@type": "Person", "name": {{ json_encode($authorName, JSON_UNESCAPED_UNICODE) }}},
  "publisher": {
    "@type": "Organization",
    "name": "{{ $bs->website_title ?? config('app.name') }}",
    "logo": {"@type": "ImageObject", "url": "{{ asset($bs->logo ?? 'assets/img/logo.png') }}"}
  },
  "mainEntityOfPage": {"@type": "WebPage", "@id": "{{ url()->current() }}"}
}
</script>
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $content->title }}">
<meta property="og:description" content="{{ Str::limit($cleanDescription, 155) }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="article:published_time" content="{{ $blog->created_at->toIso8601String() }}">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="{{ url()->current() }}">
