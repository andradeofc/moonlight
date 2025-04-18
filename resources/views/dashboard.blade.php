@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Dashboard</h1>
    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> New Campaign
    </a>
</div>

<div class="row gx-4 gy-4 mb-4">

    <div class="col-md-4">
        <div class="card card-stats bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Requests</h5>
                        <h2 class="mb-0">{{ $totalRequests }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-globe fa-3x opacity-50"></i>
                    </div>
                </div>
                <p class="card-text mt-3">Total traffic sent to your campaigns</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-stats bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Safe Page</h5>
                        <h2 class="mb-0">{{ $safePageRequests }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-shield-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <p class="card-text mt-3">
                    {{ number_format(($safePageRequests / max($totalRequests, 1)) * 100, 1) }}% of total traffic
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-stats bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Offer Page</h5>
                        <h2 class="mb-0">{{ $offerPageRequests }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
                <p class="card-text mt-3">
                    {{ number_format(($offerPageRequests / max($totalRequests, 1)) * 100, 1) }}% of total traffic
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Traffic Overview</h5>
            </div>
            <div class="card-body">
            <div style="height: 350px;">
            <div style="height: 350px; overflow-x: auto;">
    <canvas id="trafficChart"></canvas>
</div>

</div>

            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Countries</h5>
            </div>
            <div class="card-body">
                <canvas id="countryChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Campaigns</h5>
                <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Domain</th>
                            <th>Traffic Source</th>
                            <th>Requests</th>
                            <th>Connect Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCampaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->name }}</td>
                            <td>
    <span class="badge bg-secondary">
        {{ is_object($campaign->domain) ? $campaign->domain->name : ($campaign->domain ?? 'Sem domínio') }}
    </span>
</td>

                            <td>{{ $campaign->traffic_source }}</td>
                            <td>{{ $campaign->requests_count }}</td>
                            <td>{{ number_format($campaign->connect_rate * 100, 1) }}%</td>
                            <td>
                            <span class="badge rounded-pill bg-{{ $campaign->is_active ? 'success' : 'danger' }} px-3 py-2 fw-semibold">

                                    {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View campaign details">

                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No campaigns yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const trafficCtx = document.getElementById('trafficChart').getContext('2d');

    const trafficChart = new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trafficData['dates']) !!},
            datasets: [
                {
                    label: 'Total Requests',
                    data: {!! json_encode($trafficData['total']) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.05)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Safe Page',
                    data: {!! json_encode($trafficData['safe']) !!},
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.05)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Offer Page',
                    data: {!! json_encode($trafficData['offer']) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.05)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        boxWidth: 12,
                        font: {
                            size: 13
                        }
                    }
                },
                tooltip: {
                    padding: 10,
                    backgroundColor: '#000',
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    cornerRadius: 4
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e9ecef'
                    },
                    ticks: {
                        stepSize: 5,
                        color: '#6c757d'
                    }
                }
            }
        }
    });

    // Gráfico de países
    const countryCtx = document.getElementById('countryChart').getContext('2d');
    const countryChart = new Chart(countryCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($countryData['labels']) !!},
            datasets: [{
                data: {!! json_encode($countryData['data']) !!},
                backgroundColor: [
                    '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545',
                    '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 13
                        }
                    }
                }
            }
        }
    });

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

</script>

@endsection