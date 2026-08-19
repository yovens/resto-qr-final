@extends('admin.layouts.layout')

@section('title', 'Dashboard - Restaurant PRO')

@section('content')


<div class="dashboard-wrapper">
    
    {{-- ========================================= --}}
    {{-- 📊 HEADER & QUICK ACTIONS --}}
    {{-- ========================================= --}}
    <div class="dash-header-card">
        <div>
            <h1>📊 Dashboard Restaurant</h1>
            <p>Suivi en direct des activités, ventes et commandes du restaurant.</p>
        </div>
        <div class="dash-actions">
            <div class="clock-badge">
                <i class="fa-solid fa-clock"></i>
                <span id="liveClock">--:--:--</span>
            </div>
            <button class="btn-refresh" onclick="location.reload()">
                <i class="fa-solid fa-rotate-right"></i> Actualiser
            </button>
            <form method="POST" action="/admin/commandes/cloturer-journee" onsubmit="return confirm('Êtes-vous sûr de vouloir clôturer la journée ? Toutes les commandes seront archivées.');">
                @csrf
                <button type="submit" class="btn-close-day">
                    🌙 Clôturer la journée
                </button>
            </form>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- 💰 KPI CARDS (8 CARTES PRO) --}}
    {{-- ========================================= --}}
    <div class="kpi-grid">
        <div class="kpi-card revenue">
            <div class="kpi-icon">💰</div>
            <div class="kpi-info">
                <span>Ventes du jour</span>
                <h2>{{ number_format($todaySales, 2) }} HTG</h2>
                <small>+18% vs hier</small>
            </div>
        </div>

        <div class="kpi-card orders">
            <div class="kpi-icon">📦</div>
            <div class="kpi-info">
                <span>Total Commandes</span>
                <h2>{{ $totalOrders }}</h2>
                <small>Actives aujourd'hui</small>
            </div>
        </div>

        <div class="kpi-card prep">
            <div class="kpi-icon">🔥</div>
            <div class="kpi-info">
                <span>En préparation</span>
                <h2>{{ $preparingOrders }}</h2>
                <small>En cuisine</small>
            </div>
        </div>

        <div class="kpi-card ready">
            <div class="kpi-icon">✅</div>
            <div class="kpi-info">
                <span>Commandes Prêtes</span>
                <h2>{{ $completedOrders }}</h2>
                <small>À servir / livrer</small>
            </div>
        </div>

        <div class="kpi-card new-ord">
            <div class="kpi-icon">🔔</div>
            <div class="kpi-info">
                <span>Nouvelles</span>
                <h2>{{ $newOrders }}</h2>
                <small>En attente</small>
            </div>
        </div>

        <div class="kpi-card ticket">
            <div class="kpi-icon">🏷️</div>
            <div class="kpi-info">
                <span>Ticket Moyen</span>
                <h2>{{ $totalOrders > 0 ? number_format($todaySales / max($totalOrders, 1), 2) : '0.00' }} HTG</h2>
                <small>Par commande</small>
            </div>
        </div>

        <div class="kpi-card top-dish">
            <div class="kpi-icon">🍔</div>
            <div class="kpi-info">
                <span>Top Plat</span>
                <h2>{{ optional($topPlats->first())->plat->nom ?? 'N/A' }}</h2>
                <small>{{ optional($topPlats->first())->total ?? 0 }} ventes</small>
            </div>
        </div>

        <div class="kpi-card tables-card">
            <div class="kpi-icon">🪑</div>
            <div class="kpi-info">
                <span>Tables Actives</span>
                <h2>{{ \App\Models\Commande::where('archived',false)->distinct('restaurant_table_id')->count() }}</h2>
                <small>Occupées</small>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- 📈 OBJECTIF DU JOUR & WEATHER WIDGET --}}
    {{-- ========================================= --}}
    <div class="secondary-grid">
        <div class="goal-box">
            <div class="goal-header">
                <span>🎯 Objectif du jour (100 000 HTG)</span>
                <strong>{{ round(min(($todaySales/100000)*100, 100)) }}%</strong>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ min(($todaySales/100000)*100, 100) }}%;"></div>
            </div>
            <div class="goal-footer">
                <span>{{ number_format($todaySales, 2) }} HTG réalisés</span>
                <span>Restant : {{ number_format(max(100000 - $todaySales, 0), 2) }} HTG</span>
            </div>
        </div>

       <div class="weather-box">
    <div class="w-icon" id="weatherIcon">☀️</div>
    <div>
        <h3 id="weatherTemp">--°C</h3>
        <p>Les Cayes • <span style="color: #10b981; font-weight: bold;">🟢 En Ligne</span></p>
    </div>
</div>
    </div>

    {{-- ========================================= --}}
    {{-- 📉 GRAPHIQUES DE VENTES --}}
    {{-- ========================================= --}}
    <div class="charts-grid">
        <div class="chart-card">
            <h3>📈 Évolution des ventes (7 derniers jours)</h3>
            <canvas id="salesWeekChart" style="max-height: 280px;"></canvas>
        </div>
        <div class="chart-card">
            <h3>🍕 Répartition des statuts de commandes</h3>
            <canvas id="orderStatusChart" style="max-height: 280px;"></canvas>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- 🔥 TOP PLATS DU JOUR --}}
    {{-- ========================================= --}}
    <div class="section-container">
        <h2>🍔 Top Plats du jour</h2>
        <div class="top-plats-grid">
            @forelse($topPlats as $item)
                <div class="plat-card-item">
                    <div class="plat-badge-icon">🍽️</div>
                    <div class="plat-details">
                        <h4>{{ $item->plat->nom ?? 'Plat supprimé' }}</h4>
                        <small>🔥 {{ $item->total }} ventes enregistrées</small>
                    </div>
                </div>
            @empty
                <p style="color: #666; grid-column: span 5; text-align: center; padding: 20px;">Aucune vente enregistrée aujourd’hui</p>
            @endforelse
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- 📜 HISTORIQUE & COMMANDES RÉCENTES --}}
    {{-- ========================================= --}}
    <div class="section-container" style="margin-top: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">📜 Historique et Commandes Récentes</h2>
            <span class="live-badge-pulse"><span class="pulse-dot"></span> Live Pusher Actif</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Table</th>
                        <th>Client / Info</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date & Heure</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="order-list-table">
                    @foreach($recentOrders as $commande)
                    <tr id="order-row-{{ $commande->id }}">
                        <td><strong>#{{ $commande->id }}</strong></td>
                        <td>Table {{ $commande->table->numero ?? 'N/A' }}</td>
                        <td>{{ $commande->client ?? 'Client standard' }}</td>
                        <td><strong>{{ number_format($commande->total, 2) }} HTG</strong></td>
                        <td>
                            @if($commande->statut == 'nouvelle')
                                <span class="badge-status new">Nouvelle</span>
                            @elseif($commande->statut == 'en_preparation')
                                <span class="badge-status prep">En préparation</span>
                            @else
                                <span class="badge-status ready">Prête</span>
                            @endif
                        </td>
                        <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                      <td>
    <a href="{{ route('facture.show', $commande) }}" class="btn-view">
        👁 Voir Facture
    </a>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- 🔔 AUDIO NOTIF --}}
<audio id="notifSound" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
</audio>
@endsection

@push('styles')

@endpush

@push('scripts')
<script>
    window.salesChartData = {!! json_encode($salesChart) !!};
    window.orderStatsData = {
        completed: {{ $completedOrders }},
        preparing: {{ $preparingOrders }},
        new: {{ $newOrders }}
    };
</script>

@vite(['resources/js/dashboard.js'])

@endpush