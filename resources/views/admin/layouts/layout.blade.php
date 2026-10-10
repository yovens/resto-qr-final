<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') · Resto Kay-Y</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- app.js charge bootstrap.js qui initialise Laravel Echo (window.Echo) --}}
    @vite(['resources/js/app.js'])

    @stack('styles')
</head>
<body>

@php
    $navCuisine = $commandesCuisine ?? 0;
    $navStock   = $stockAlertsCount ?? 0;
    $navNotif   = $notificationCount ?? 0;
    $user       = auth()->user();
@endphp

<aside class="sidebar" id="sidebar">
    <a href="/admin/dashboard" class="brand">
        <span class="brand-mark">KY</span>
        <span>Resto Kay-Y<small>Administration</small></span>
    </a>

    <nav class="side-nav">
        <div class="side-label">Pilotage</div>
        <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i> Tableau de bord
        </a>
        <a href="/cuisine" class="{{ request()->is('cuisine*') ? 'active' : '' }}">
            <i data-lucide="chef-hat"></i> Cuisine
            @if($navCuisine > 0)<span class="side-count alert">{{ $navCuisine }}</span>@endif
        </a>

        <div class="side-label">Restaurant</div>
        <a href="/admin/plats" class="{{ request()->is('admin/plats*') ? 'active' : '' }}">
            <i data-lucide="book-open"></i> Menu
        </a>
        <a href="/admin/categories" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
            <i data-lucide="folder"></i> Catégories
        </a>
        <a href="/admin/tables" class="{{ request()->is('admin/tables*') ? 'active' : '' }}">
            <i data-lucide="armchair"></i> Tables & QR
        </a>
        <a href="/admin/stock" class="{{ request()->is('admin/stock*') ? 'active' : '' }}">
            <i data-lucide="package"></i> Stock
            @if($navStock > 0)<span class="side-count alert">{{ $navStock }}</span>@endif
        </a>
        <a href="/admin/suppliers" class="{{ request()->is('admin/suppliers*') ? 'active' : '' }}">
            <i data-lucide="truck"></i> Fournisseurs
        </a>

        <div class="side-label">Finances</div>
        <a href="/admin/ventes" class="{{ request()->is('admin/ventes*') ? 'active' : '' }}">
            <i data-lucide="receipt"></i> Ventes
        </a>
        <a href="/admin/reports" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
            <i data-lucide="bar-chart-3"></i> Rapports
        </a>

        <div class="side-label">Équipe</div>
        <a href="/admin/employes" class="{{ request()->is('admin/employes*') ? 'active' : '' }}">
            <i data-lucide="users"></i> Employés
        </a>
        <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
            <i data-lucide="shield-check"></i> Utilisateurs & rôles
        </a>
        <a href="/admin/profile" class="{{ request()->is('admin/profile*') ? 'active' : '' }}">
            <i data-lucide="settings"></i> Paramètres
        </a>
    </nav>

    <div class="side-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><i data-lucide="log-out"></i> Déconnexion</button>
        </form>
    </div>
</aside>
<div class="side-backdrop" onclick="document.body.classList.remove('nav-open')"></div>

<div class="main">
    <header class="topbar">
        <button class="menu-toggle" type="button" aria-label="Ouvrir le menu"
                onclick="document.body.classList.toggle('nav-open')">
            <i data-lucide="menu"></i>
        </button>

        <label class="search">
            <i data-lucide="search"></i>
            <input type="search" placeholder="Rechercher une commande, un plat, une table…">
            <kbd>/</kbd>
        </label>

        <div class="topbar-right">
            <a href="/admin/notifications" class="icon-btn" aria-label="Notifications">
                <i data-lucide="bell"></i>
                @if($navNotif > 0)<span class="dot">{{ $navNotif }}</span>@endif
            </a>

            <div class="user">
                <div class="avatar">{{ $user ? strtoupper(mb_substr($user->name, 0, 1)) : 'A' }}</div>
                <div class="user-meta">
                    <strong>{{ $user->name ?? 'Administrateur' }}</strong>
                    <small>{{ ucfirst($user->role ?? 'admin') }}</small>
                </div>
            </div>
        </div>
    </header>

    <main class="content">
        @yield('content')
    </main>
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();

    // Raccourci "/" pour la recherche
    document.addEventListener('keydown', function (e) {
        const field = document.querySelector('.search input');
        if (e.key === '/' && field && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            field.focus();
        }
    });
</script>

@stack('scripts')
</body>
</html>