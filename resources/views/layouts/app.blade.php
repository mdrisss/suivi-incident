<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tableau de bord') · {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .stat { font-size: 2rem; font-weight: 600; line-height: 1; }
        .timeline { border-left: 2px solid #dee2e6; padding-left: 1.25rem; }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-item::before { content: ''; position: absolute; left: -1.65rem; top: .35rem; width: .75rem; height: .75rem; border-radius: 50%; background: #0d6efd; }
        @media print {
            .no-print, nav.navbar { display: none !important; }
            body { background: #fff; }
            .card { box-shadow: none; border: 1px solid #dee2e6; }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}"><i class="bi bi-diagram-3"></i> Suivi Backbone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('incidents.*')) active @endif" href="{{ route('incidents.index') }}">Incidents</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @if(request()->routeIs('sites.*', 'equipements.*', 'liaisons.*')) active @endif" href="#" data-bs-toggle="dropdown">Inventaire</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('sites.index') }}"><i class="bi bi-building"></i> Sites</a></li>
                        <li><a class="dropdown-item" href="{{ route('equipements.index') }}"><i class="bi bi-hdd-network"></i> Équipements</a></li>
                        <li><a class="dropdown-item" href="{{ route('liaisons.index') }}"><i class="bi bi-share"></i> Liaisons</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('rapports.*')) active @endif" href="{{ route('rapports.hebdomadaire') }}">Rapport hebdo</a></li>
                @can('admin')
                    <li class="nav-item"><a class="nav-link @if(request()->routeIs('users.*')) active @endif" href="{{ route('users.index') }}">Utilisateurs</a></li>
                @endcan
            </ul>
            <span class="navbar-text me-3"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }} <small class="text-secondary">({{ auth()->user()->role->label() }})</small></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
            </form>
        </div>
    </div>
</nav>

<main class="container-fluid px-4 pb-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show no-print">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show no-print">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
