@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Gerenciamento de Campanhas</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Usuário</th>
                            <th>Domínio</th>
                            <th>Status</th>
                            <th>Requisições</th>
                            <th>Taxa de Conversão</th>
                            <th>Criada em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->id }}</td>
                            <td>{{ $campaign->name }}</td>
                            <td>
                                @if($campaign->user)
                                    {{ $campaign->user->name }} {{ $campaign->user->surname }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($campaign->domain)
                                    {{ $campaign->domain->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($campaign->is_active)
                                    <span class="badge bg-success">Ativa</span>
                                @else
                                    <span class="badge bg-danger">Inativa</span>
                                @endif
                            </td>
                            <td>{{ $campaign->traffic_logs_count }}</td>
                            <td>{{ number_format($campaign->connect_rate * 100, 2) }}%</td>
                            <td>{{ $campaign->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection