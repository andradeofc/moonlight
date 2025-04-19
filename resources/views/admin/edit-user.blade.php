@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Usuário</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="surname" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control" id="surname" name="surname" value="{{ $user->surname }}" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="current_plan_id" class="form-label">Plano</label>
                        <select class="form-select" id="current_plan_id" name="current_plan_id">
                            <option value="">Sem plano</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ $user->current_plan_id == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="plan_expires_at" class="form-label">Data de Expiração</label>
                        <input type="date" class="form-control" id="plan_expires_at" name="plan_expires_at" 
                            value="{{ $user->plan_expires_at ? $user->plan_expires_at->format('Y-m-d') : '' }}">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Usuário Ativo</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" value="1"
                                {{ $user->is_admin ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">Privilégios de Administrador</label>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection