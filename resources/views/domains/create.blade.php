
@extends('layouts.app')

@section('title', 'Add Domain')

@section('content')
<div class="mb-4">
    <a href="{{ route('domains.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Domains
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Domain</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('domains.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Domain Name</label>
                    <div class="input-group">
                        <span class="input-group-text">https://</span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="yourdomain.com" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">Enter your domain without http:// or https://</div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i> Important Instructions</h5>
                <p>After adding your domain, you will need to configure your DNS settings:</p>
                <ol>
                    <li>Go to your domain provider's dashboard</li>
                    <li>Find the DNS management section</li>
                    <li>Add a CNAME record that will be provided after you submit this form</li>
                    <li>Wait for DNS propagation (can take up to 24-48 hours)</li>
                    <li>Verify your domain once the DNS changes have propagated</li>
                </ol>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add Domain
                </button>
            </div>
        </form>
    </div>
</div>
@endsection