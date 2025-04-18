
@extends('layouts.app')

@section('title', 'Campaigns')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Campaigns</h1>
    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> New Campaign
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" id="campaign-search" placeholder="Search campaigns...">
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filter-source">
                    <option value="">All Traffic Sources</option>
                    <option value="facebook">Facebook</option>
                    <option value="google">Google</option>
                    <option value="tiktok">TikTok</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Traffic Source</th>
                        <th>Status</th>
                        <th>Total Requests</th>
                        <th>Connect Rate</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr data-source="{{ strtolower($campaign->traffic_source) }}">
                        <td>{{ $campaign->name }}</td>
                        <td>{{ is_object($campaign->domain) ? $campaign->domain->name : ($campaign->domain ?? 'Sem domínio') }}</td>
                        <td>{{ $campaign->traffic_source }}</td>
                        <td>
                            <span class="badge bg-{{ $campaign->is_active ? 'success' : 'danger' }}">
                                {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $campaign->traffic_logs_count ?? 0 }}</td>
                        <td>
                            @if(($campaign->traffic_logs_count ?? 0) > 0)
                                {{ number_format(($campaign->offer_logs_count / $campaign->traffic_logs_count) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No campaigns found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Search functionality
    const searchInput = document.getElementById('campaign-search');
    const filterSource = document.getElementById('filter-source');
    const tableRows = document.querySelectorAll('tbody tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const sourceFilter = filterSource.value.toLowerCase();
        
        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const rowSource = row.dataset.source;
            
            const matchesSearch = rowText.includes(searchTerm);
            const matchesSource = sourceFilter === '' || rowSource === sourceFilter;
            
            if (matchesSearch && matchesSource) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    filterSource.addEventListener('change', filterTable);
    
    // Confirmation for delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this campaign?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection