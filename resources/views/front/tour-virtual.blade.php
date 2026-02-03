@extends('front.layout')

@section('pagename')
    - {{ __('Tour Virtual') }}
@endsection

@section('meta-keywords', 'tour virtual, visita 360, imóveis paraguai')
@section('meta-description', 'Tour virtual 360° dos nossos projetos no Paraguai')

@section('content')
    <div class="container-fluid p-0" style="height: 100vh;">
        @if($project && $project->virtual_tour_url)
            <iframe 
                src="{{ $project->virtual_tour_url }}" 
                style="width: 100%; height: 100%; border: 0;"
                allowfullscreen
                allow="xr-spatial-tracking; gyroscope; accelerometer">
            </iframe>
        @else
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <h1>{{ __('Tours Virtuais Disponíveis') }}</h1>
                        <p class="lead">{{ __('Selecione um projeto para visualizar') }}</p>
                        
                        @if($projects && $projects->count() > 0)
                            <div class="row mt-4">
                                @foreach($projects as $proj)
                                    @php
                                        $content = $proj->contents->first();
                                    @endphp
                                    @if($content)
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('front.tour.virtual', $content->slug) }}" class="btn btn-primary btn-lg btn-block">
                                                {{ $content->title }}
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{ __('Nenhum tour virtual disponível no momento') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
