@extends('layouts.app')

@section('title', 'Escolha seu plano')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h1 class="display-4">Escolha seu plano</h1>
        <p class="lead">Selecione o plano que melhor atende às suas necessidades</p>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
        @foreach($plans as $plan)
            <div class="col">
                <div class="card mb-4 rounded-3 shadow-sm h-100">
                    <div class="card-header py-3 @if($loop->iteration == 2) bg-primary text-white @endif">
                        <h4 class="my-0 fw-normal">{{ $plan->name }}</h4>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h1 class="card-title pricing-card-title">R${{ number_format($plan->price, 2, ',', '.') }}<small class="text-muted fw-light">/mês</small></h1>
                        <p class="mt-3 mb-4">{{ $plan->description }}</p>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li><i class="fas fa-check text-success me-2"></i>{{ number_format($plan->clicks, 0, ',', '.') }} clicks</li>
                            <li><i class="fas fa-check text-success me-2"></i>{{ $plan->domains }} domínios</li>
                            <li><i class="fas fa-check text-success me-2"></i>Extra clicks: R${{ number_format($plan->extra_clicks_price, 4, ',', '.') }}</li>
                            <li><i class="fas fa-check text-success me-2"></i>Fontes de tráfego:</li>
                            <ul class="list-unstyled">
                                @foreach($plan->traffic_sources as $source)
                                    <li class="small">- {{ $source }}</li>
                                @endforeach
                            </ul>
                        </ul>
                        <a href="{{ route('plans.show', $plan) }}" class="w-100 btn btn-lg @if($loop->iteration == 2) btn-primary @else btn-outline-primary @endif mt-auto">Selecionar</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection