
@extends('layouts.app')

@section('title', 'Domains')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Domains</h1>
    <a href="{{ route('domains.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Domain
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>CNAME Record</th>
                        <th>Status</th>
                        <th>Campaigns</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($domains as $domain)
                    <tr>
                        <td>{{ $domain->name }}</td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" value="{{ $domain->cname_record }}" readonly>
                                <button class="btn btn-sm btn-outline-secondary copy-btn" data-clipboard-text="{{ $domain->cname_record }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            @if($domain->verified)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $domain->campaigns_count ?? 0 }}</td>
                        <td>{{ $domain->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('domains.show', $domain) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if(!$domain->verified)
                                <form action="{{ route('domains.verify', $domain) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Verify
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('domains.destroy', $domain) }}" method="POST" class="d-inline delete-form">
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
                        <td colspan="6" class="text-center">No domains found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
<script>
    // Initialize clipboard.js
    new ClipboardJS('.copy-btn');
    
    // Confirmation for delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this domain? This will also delete all campaigns using this domain.')) {
                e.preventDefault();
            }
        });
    });
    
    // Show notification when copying
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Original button content
            const originalContent = this.innerHTML;
            
            // Change to checkmark
            this.innerHTML = '<i class="fas fa-check"></i>';
            
            // Change button color
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-success');
            
            // Reset after 2 seconds
            setTimeout(() => {
                this.innerHTML = originalContent;
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-secondary');
            }, 2000);
        });
    });
</script>
@endsection