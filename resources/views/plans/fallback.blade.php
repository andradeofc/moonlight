<!-- resources/views/plans/fallback.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Escolha seu plano (Temporário)</h4>
                </div>
                <div class="card-body">
                    <p class="alert alert-info">
                        A funcionalidade de planos ainda está sendo implementada. 
                        Enquanto isso, você pode acessar o sistema normalmente.
                    </p>
                    
                    <div class="d-grid">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            Ir para o Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection