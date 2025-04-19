@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Logs de Tráfego</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.traffic-logs') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="user_id" class="form-label">Usuário</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} {{ $user->surname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Data Final</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pesquisar</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" 
                            placeholder="IP, País, User-Agent...">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Campanha</th>
                            <th>IP</th>
                            <th>País</th>
                            <th>Dispositivo</th>
                            <th>Destino</th>
                            <th>Data/Hora</th>
                            <th>URL Completa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->campaign && $log->campaign->user)
                                    {{ $log->campaign->user->name }} {{ $log->campaign->user->surname }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($log->campaign)
                                    {{ $log->campaign->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ $log->country ?: 'Desconhecido' }}</td>
                            <td>{{ $log->device_type ?: 'Desconhecido' }}</td>
                            <td>
                                <span class="badge {{ $log->destination == 'offer' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $log->destination }}
                                </span>
                            </td>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                    data-bs-target="#urlModal{{ $log->id }}">
                                    Ver URL
                                </button>
                                
                                <!-- Modal com URL completa -->
                                <div class="modal fade" id="urlModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">URL Completa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>URL:</strong> <code>{{ $log->request_url }}</code></p>
                                                <p><strong>Referrer:</strong> <code>{{ $log->referrer ?: 'N/A' }}</code></p>
                                                <p><strong>User Agent:</strong> <code>{{ $log->user_agent }}</code></p>
                                                <p><strong>Razão:</strong> {{ $log->reason ?: 'N/A' }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection