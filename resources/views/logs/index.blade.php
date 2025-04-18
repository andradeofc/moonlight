
@extends('layouts.app')

@section('title', 'Traffic Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Traffic Logs</h1>
    <div>
        <button class="btn btn-outline-secondary" id="refresh-btn">
            <i class="fas fa-sync-alt me-2"></i> Refresh
        </button>
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-filter me-2"></i> Filters
        </button>
    </div>
</div>

<div class="collapse mb-4" id="filterCollapse">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('logs.index') }}" method="GET" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="campaign_id" class="form-label">Campaign</label>
                            <select class="form-select" id="campaign_id" name="campaign_id">
                                <option value="">All Campaigns</option>
                                @foreach($campaigns as $id => $name)
                                    <option value="{{ $id }}" @if(request('campaign_id') == $id) selected @endif>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <select class="form-select" id="destination" name="destination">
                                <option value="">All Destinations</option>
                                <option value="safe" @if(request('destination') == 'safe') selected @endif>Safe Page</option>
                                <option value="offer" @if(request('destination') == 'offer') selected @endif>Offer Page</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select class="form-select" id="country" name="country">
                                <option value="">All Countries</option>
                                @foreach($countries as $code)
                                    <option value="{{ $code }}" @if(request('country') == $code) selected @endif>
                                        {{ $code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="device_type" class="form-label">Device</label>
                            <select class="form-select" id="device_type" name="device_type">
                                <option value="">All Devices</option>
                                <option value="mobile" @if(request('device_type') == 'mobile') selected @endif>Mobile</option>
                                <option value="desktop" @if(request('device_type') == 'desktop') selected @endif>Desktop</option>
                                <option value="tablet" @if(request('device_type') == 'tablet') selected @endif>Tablet</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control" id="ip_address" name="ip_address" 
                                   placeholder="Search by IP" value="{{ request('ip_address') }}">
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary me-2">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover">
    <thead>
        <tr>
            <th>Time</th>
            <th>Campaign</th>
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
            <td>
                <a href="{{ route('campaigns.show', $log->campaign_id) }}">
                    {{ $log->campaign->name ?? 'Unknown' }}
                </a>
            </td>
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
                                    <code>{{ $log->request_url }}</code>
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
            <td colspan="8" class="text-center">No logs found</td>
        </tr>
        @endforelse
    </tbody>
</table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
            </div>
            <div>
            {{ $logs->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto refresh
    const refreshBtn = document.getElementById('refresh-btn');
    
    refreshBtn.addEventListener('click', function() {
        location.reload();
    });
    
    // Toggle filter collapse based on if any filters are active
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.toString() && urlParams.get('page') === null) {
            document.getElementById('filterCollapse').classList.add('show');
        }
    });
</script>
@endsection