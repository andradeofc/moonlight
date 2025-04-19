<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} Admin</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark text-white" id="sidebar-wrapper" style="width: 250px;">
            <div class="sidebar-heading p-3">Admin Panel</div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white">Dashboard</a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action bg-transparent text-white">Usuários</a>
                <a href="{{ route('admin.traffic-logs') }}" class="list-group-item list-group-item-action bg-transparent text-white">Logs de Tráfego</a>
                <a href="{{ route('admin.campaigns') }}" class="list-group-item list-group-item-action bg-transparent text-white">Campanhas</a>
                <a href="{{ route('admin.domains') }}" class="list-group-item list-group-item-action bg-transparent text-white">Domínios</a>
                <a href="{{ route('admin.plans') }}" class="list-group-item list-group-item-action bg-transparent text-white">Planos</a>
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white">Voltar ao site</a>
                <form method="POST" action="{{ route('logout') }}" class="list-group-item list-group-item-action bg-transparent text-white">
                    @csrf
                    <button type="submit" class="btn p-0 text-white">Logout</button>
                </form>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <div class="ms-auto">
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid p-4">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>
    
    @yield('scripts')
</body>
</html>