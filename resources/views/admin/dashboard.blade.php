@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Administrativo</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Usuários Ativos</h5>
                    <h2>{{ $activeUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Tráfego (24h)</h5>
                    <h2>{{ $traffic24h }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Campanhas Ativas</h5>
                    <h2>{{ $activeCampaigns }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Domínios Verificados</h5>
                    <h2>{{ $verifiedDomains }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Usuários por Plano</h5>
                </div>
                <div class="card-body">
                    <canvas id="planChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Tráfego nos Últimos 7 Dias</h5>
                </div>
                <div class="card-body">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Top Usuários por Tráfego</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Campanha</th>
                                    <th>Total de Requisições</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $item)
                                <tr>
                                    <td>{{ $item->user_name }}</td>
                                    <td>{{ $item->campaign_name }}</td>
                                    <td>{{ $item->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Gráfico de usuários por plano
    const planCtx = document.getElementById('planChart').getContext('2d');
    const planChart = new Chart(planCtx, {
        type: 'pie',
        data: {
            labels: [
                @foreach($usersByPlan as $item)
                '{{ $item->plan_name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($usersByPlan as $item)
                    {{ $item->total }},
                    @endforeach
                ],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#e74a3b'
                ]
            }]
        }
    });
    
    // Gráfico de tráfego por dia
    const trafficCtx = document.getElementById('trafficChart').getContext('2d');
    const trafficChart = new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($trafficByDay as $item)
                '{{ $item['date'] }}',
                @endforeach
            ],
            datasets: [{
                label: 'Requisições',
                data: [
                    @foreach($trafficByDay as $item)
                    {{ $item['count'] }},
                    @endforeach
                ],
                borderColor: '#4e73df',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection