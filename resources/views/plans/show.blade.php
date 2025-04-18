@extends('layouts.app')

@section('title', 'Detalhes do Plano')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $plan->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 class="display-4">R${{ number_format($plan->price, 2, ',', '.') }}</h1>
                        <p class="lead">{{ $plan->description }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Detalhes do plano:</h5>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Clicks incluídos
                                    <span class="badge bg-primary rounded-pill">{{ number_format($plan->clicks, 0, ',', '.') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Domínios permitidos
                                    <span class="badge bg-primary rounded-pill">{{ $plan->domains }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Preço por click adicional
                                    <span class="badge bg-primary rounded-pill">R${{ number_format($plan->extra_clicks_price, 4, ',', '.') }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Fontes de tráfego:</h5>
                            <ul class="list-group mb-4">
                                @foreach($plan->traffic_sources as $source)
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success me-2"></i>{{ $source }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p>Ao assinar este plano, você concorda com nossos <a href="#" target="_blank">Termos de Serviço</a>.</p>
                        <a href="{{ $paymentLink }}" class="btn btn-lg btn-primary" target="_blank">
                            <i class="fas fa-lock me-2"></i>Prosseguir para pagamento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection