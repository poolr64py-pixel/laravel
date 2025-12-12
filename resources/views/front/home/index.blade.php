@extends('front.layout')
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="display-4 fw-bold text-primary">🏠 Terra's No Paraguay</h1>
            <p class="lead text-muted">O melhor sistema de imóveis do Paraguai</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-home fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Imóveis</h5>
                    <p class="card-text">Encontre o imóvel perfeito para você</p>
                    <a href="{{ url('/properties') }}" class="btn btn-primary">Ver Imóveis</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-building fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Corretoras</h5>
                    <p class="card-text">Nossas imobiliárias parceiras</p>
                    <a href="/imoveis" class="btn btn-success">Ver Corretoras</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-map-marker-alt fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Regiões</h5>
                    <p class="card-text">Conheça as melhores regiões</p>
                    <a href="{{ url('/about') }}" class="btn btn-info">Ver Regiões</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
