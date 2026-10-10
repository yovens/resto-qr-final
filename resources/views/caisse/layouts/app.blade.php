<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Caisse') · Resto Kay-Y</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    {{-- Même feuille de styles que l'administration --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body>

@php
    $user = auth()->user();
    $pretes = $countPretes ?? 0;
@endphp

<aside class="sidebar" id="sidebar">
    <a href="{{ url('/caisse/dashboard') }}" class="brand">
        <span class="brand-mark">KY</span>
        <span>Resto Kay-Y<small>Caisse</small></span>
    </a>

    <nav class="side-nav">
        <div class="side-label">Caisse</div>
        <a href="{{ url('/caisse/dashboard') }}" class="{{ request()->is('caisse/dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i> Tableau de bord
        </a>
        <a href="{{ url('/caisse/commandes') }}" class="{{ request()->is('caisse/commandes*') || request()->is('caisse/encaisser*') ? 'active' : '' }}">
            <i data-lucide="bell-ring"></i> À encaisser
            <span class="side-count {{ $pretes > 0 ? 'alert' : '' }}" id="commandesPretesBadge">{{ $pretes }}</span>
        </a>
        <a href="{{ url('/caisse/paiements') }}" class="{{ request()->is('caisse/paiements*') ? 'active' : '' }}">
            <i data-lucide="receipt"></i> Paiements
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

        <form class="search" action="{{ url('/caisse/commandes') }}" method="GET" role="search">
            <i data-lucide="search"></i>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="N° de commande ou de table…">
        </form>

        <div class="topbar-right">
            <span class="chip"><i data-lucide="clock"></i><span id="topClock" class="num">--:--</span></span>
            <div class="user">
                <div class="avatar">{{ $user ? mb_strtoupper(mb_substr($user->name, 0, 1)) : 'C' }}</div>
                <div class="user-meta">
                    <strong>{{ $user->name ?? 'Caissier' }}</strong>
                    <small>Caisse</small>
                </div>
            </div>
        </div>
    </header>

    <main class="content">
        @yield('content')
    </main>
</div>

<div class="toasts-caisse" id="toastContainer" aria-live="polite"></div>
<audio id="notifSound" preload="auto"><source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg"></audio>

<style>
    .toasts-caisse { position: fixed; top: 76px; right: 20px; z-index: 100; display: grid; gap: 8px; }
    .toast-c {
        min-width: 280px; max-width: 360px; padding: 12px 16px; border-radius: 9px;
        background: var(--surface); border: 1px solid var(--border); border-left: 4px solid var(--brand);
        box-shadow: 0 10px 30px rgba(14, 27, 34, .12); font-weight: 700; font-size: 13.5px;
        display: flex; align-items: center; gap: 10px; animation: toastIn .25s ease-out;
    }
    .toast-c.error { border-left-color: var(--danger); }
    @keyframes toastIn { from { opacity: 0; transform: translateY(-6px); } }
    .side-count:empty { display: none; }
</style>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    window.lucide && lucide.createIcons();

    // Horloge
    (function () {
        const el = document.getElementById('topClock');
        const tick = () => el.textContent = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        tick(); setInterval(tick, 15000);
    })();

    // Toasts (utilisables par toutes les pages caisse)
    window.caisseToast = function (message, type) {
        const box = document.getElementById('toastContainer');
        const t = document.createElement('div');
        t.className = 'toast-c' + (type === 'error' ? ' error' : '');
        t.textContent = message;
        box.appendChild(t);
        setTimeout(() => t.remove(), 4500);
    };

    // Son : débloqué au premier clic (règle des navigateurs)
    (function () {
        const s = document.getElementById('notifSound');
        let ready = false;
        document.addEventListener('pointerdown', () => { ready = true; }, { once: true });
        window.caisseDing = () => { if (ready && s) { s.currentTime = 0; s.play().catch(() => {}); } };
    })();

    // Compteur « À encaisser » dans le menu
    (function () {
        const badge = document.getElementById('commandesPretesBadge');
        if (!badge) return;
        async function refresh() {
            try {
                const res = await fetch(@json(route('caisse.commandes.count')), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const n = Number((await res.json()).count ?? 0);
                badge.textContent = n;
                badge.classList.toggle('alert', n > 0);
            } catch (e) {}
        }
        setInterval(refresh, 10000);
    })();

    @if(session('success')) caisseToast(@json(session('success'))); @endif
    @if(session('error')) caisseToast(@json(session('error')), 'error'); @endif
</script>

@stack('scripts')
</body>
</html>