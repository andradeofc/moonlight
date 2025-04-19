@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Gerenciamento de Planos</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Cliques Permitidos</th>
                            <th>Domínios Permitidos</th>
                            <th>Usuários Ativos</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>R$ {{ number_format($plan->price, 2, ',', '.') }}</td>
                            <td>{{ $plan->clicks }}</td>
                            <td>{{ $plan->domains }}</td>
                            <td>{{ $plan->users_count }}</td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection