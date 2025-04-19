@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Gerenciamento de Domínios</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Usuário</th>
                            <th>CNAME Record</th>
                            <th>Status</th>
                            <th>Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($domains as $domain)
                        <tr>
                            <td>{{ $domain->id }}</td>
                            <td>{{ $domain->name }}</td>
                            <td>
                                @if($domain->user)
                                    {{ $domain->user->name }} {{ $domain->user->surname }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $domain->cname_record }}</td>
                            <td>
                                @if($domain->verified)
                                    <span class="badge bg-success">Verificado</span>
                                @else
                                    <span class="badge bg-warning">Pendente</span>
                                @endif
                            </td>
                            <td>{{ $domain->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection