@extends('layouts.app')

@section('title', $campaign->name)

@section('content')

@if(session('campaignUrl') && session('params'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <h4><i class="fas fa-check-circle me-2"></i> Campaign Created Successfully</h4>
    <p>Copy the campaign URL and parameters to use in your ads:</p>
    
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label">Campaign URL</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" value="{{ session('campaignUrl') }}" id="campaign-url-new" readonly>
                <button class="btn btn-outline-secondary copy-btn" data-clipboard-target="#campaign-url-new">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
        
        <div class="col-md-6">
            <label class="form-label">URL Parameters</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" value="{{ session('params') }}" id="campaign-params-new" readonly>
                <button class="btn btn-outline-secondary copy-btn" data-clipboard-target="#campaign-params-new">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
    
    @if(session('instructions'))
    <div class="alert alert-warning">
        <h5><i class="fas fa-lightbulb me-2"></i> {{ session('instructions') }}</h5>
        <p>Os parâmetros incluem variáveis dinâmicas do Facebook que serão automaticamente substituídas:</p>
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <li><code>@{{ "{{campaign.name}}" }}</code> → O nome da sua campanha</li>
                    <li><code>@{{ "{{domain}}" }}</code> → O domínio do clique</li>
                    <li><code>@{{ "{{placement}}" }}</code> → O local onde seu anúncio é exibido</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul>
                    <li><code>@{{ "{{adset.name}}" }}</code> → O nome do conjunto de anúncios</li>
                    <li><code>@{{ "{{ad.name}}" }}</code> → O nome do anúncio</li>
                    <li><code>@{{ "{{site_source_name}}" }}</code> → A fonte do site</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="mb-4">
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Campaigns
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>{{ $campaign->name }}</h1>
        <p class="text-muted">
            Created {{ $campaign->created_at->format('M d, Y') }} | 
            <span class="badge bg-{{ $campaign->is_active ? 'success' : 'danger' }}">
                {{ $campaign->is_active ? 'Active' : 'Inactive' }}
            </span>
        </p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
            <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-stats bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Requests</h5>
                        <h2 class="mb-0">{{ $stats['total'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-globe fa-3x opacity-50"></i>
                    </div>
                </div>
                <p class="card-text mt-3">Traffic sent to this campaign</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-stats bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Safe Page</h5>
                        <h2 class="mb-0">{{ $stats['safe'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-shield-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <p class="card-text mt-3">
                    {{ number_format(($stats['safe'] / max($stats['total'], 1)) * 100, 1) }}% of total traffic
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
                        <h2 class="mb-0">{{ $stats['offer'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
                <p class="card-text mt-3">
                    {{ number_format(($stats['offer'] / max($stats['total'], 1)) * 100, 1) }}% of total traffic
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Campaign Links</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Campaign URL</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ $campaignUrl }}" id="campaign-url" readonly>
                            <button class="btn btn-outline-secondary copy-btn" data-clipboard-target="#campaign-url">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">URL Parameters</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ $params }}" id="campaign-params" readonly>
                            <button class="btn btn-outline-secondary copy-btn" data-clipboard-target="#campaign-params">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Tip:</strong> Estes parâmetros já estão no formato correto para uso no Facebook Ads. Cole-os exatamente como estão.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Campaign Settings</h5>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                    <tr>
                        <th>Domain</th>
                        <td>{{ is_object($campaign->domain) ? $campaign->domain->name : $campaign->domain }}</td>
                    </tr>
                        <tr>
                            <th>Traffic Source</th>
                            <td>{{ $campaign->traffic_source }}</td>
                        </tr>
                        <tr>
                            <th>Safe Page</th>
                            <td>
                                <a href="{{ $campaign->safe_url }}" target="_blank">
                                    {{ $campaign->safe_url }} <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Safe Method</th>
                            <td>{{ $campaign->safe_method }}</td>
                        </tr>
                        <tr>
                            <th>Offer Page</th>
                            <td>
                                <a href="{{ $campaign->offer_url }}" target="_blank">
                                    {{ $campaign->offer_url }} <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Offer Method</th>
                            <td>{{ $campaign->offer_method }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Targeting</h5>
            </div>
            <div class="card-body">
                <h6>Countries</h6>
                <div class="mb-3">
                    @foreach(json_decode($campaign->countries) as $country)
                        <span class="badge bg-secondary me-1">{{ $country }}</span>
                    @endforeach
                </div>
                
                <h6>Devices</h6>
                <div class="mb-3">
                    @foreach(json_decode($campaign->devices) as $device)
                        <span class="badge bg-secondary me-1">{{ ucfirst($device) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Traffic Logs</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>IP</th>
                            <th>Country</th>
                            <th>Device</th>
                            <th>Destination</th>
                            <th>Reason</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ $log->country ?? 'Unknown' }}</td>
                            <td>{{ ucfirst($log->device_type) ?? 'Unknown' }}</td>
                            <td>
                                <span class="badge bg-{{ $log->destination == 'offer' ? 'success' : 'warning' }}">
                                    {{ ucfirst($log->destination) }}
                                </span>
                            </td>
                            <td>{{ $log->reason }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="logModalLabel{{ $log->id }}">Log Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>Request URL:</h6>
                                                <div class="border p-2 mb-3 bg-light overflow-auto">
                                                    <code>{{ $log->request_url ?? 'Not available' }}</code>
                                                </div>
                                                
                                                <h6>Reason:</h6>
                                                <div class="border p-2 mb-3">
                                                    {{ $log->reason }}
                                                </div>
                                                
                                                <h6>User Agent:</h6>
                                                <div class="border p-2 mb-3 bg-light overflow-auto">
                                                    <code>{{ $log->user_agent }}</code>
                                                </div>
                                                
                                                <h6>Referrer:</h6>
                                                <div class="border p-2">
                                                    {{ $log->referrer ?? 'None' }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No traffic logs yet</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clipboard = new ClipboardJS('.copy-btn');

        clipboard.on('success', function(e) {
            const btn = e.trigger;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-copy"></i>';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);

            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            console.error('Erro ao copiar:', e);
            alert('Erro ao copiar. Por favor, copie manualmente.');
        });
    });
</script>
@endsection
