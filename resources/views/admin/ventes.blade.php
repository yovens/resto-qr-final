@extends('admin.layouts.app')

@section('content')

<style>
    .stat-card { background: #27ae60; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2); margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; }
    .filter-bar { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .data-table th { padding: 15px; color: #7f8c8d; text-transform: uppercase; font-size: 0.8rem; }
    .data-table td { background: white; padding: 15px; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
    .data-table tr td:first-child { border-left: 1px solid #eee; border-radius: 10px 0 0 10px; }
    .data-table tr td:last-child { border-right: 1px solid #eee; border-radius: 0 10px 10px 0; }
    .chart-box { background: white; padding: 20px; border-radius: 15px; display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
</style>

<h1>📊 Historique des ventes</h1>

<div class="stat-card">
    <div style="color: #333333 !important;">
    <h3 style="margin:0; opacity: 0.8; color: #333333 !important;">
        Chiffre d'affaires total
    </h3>
    <h1 style="margin:5px 0 0 0; color: #000000 !important;">
        {{ number_format($total, 2) }} HTG
    </h1>
</div>
    <div style="font-size: 3rem; opacity: 0.2;">
        <i data-lucide="trending-up"></i>
    </div>
</div>

<script>
  lucide.createIcons();
</script>

<form method="GET" class="filter-bar">
    <select name="month" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
        <option value="">📅 Tous les mois</option>
        @for($i=1; $i<=12; $i++)
            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
    </select>

    <select name="year" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
        <option value="">📆 Toutes les années</option>
        @for($i=date('Y'); $i>=2023; $i--)
            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
    </select>

    <button type="submit" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 8px; cursor: pointer;">Filtrer</button>
</form>

<table class="data-table">
    <tr>
        <th>ID</th><th>Date</th><th>Table</th><th>Total</th><th>Statut</th><th>Détails</th>
    </tr>
    @foreach($commandes as $c)
    <tr>
        <td><strong>#{{ $c->id }}</strong></td>
        <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
        <td>Table {{ $c->table->numero ?? 'N/A' }}</td>
        <td>{{ number_format($c->total, 2) }} HTG</td>
        <td><span style="color: #27ae60; font-weight: bold;">{{ $c->statut }}</span></td>
        <td>
            <details style="cursor: pointer;">
                <summary>Voir plats</summary>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    @foreach($c->items as $item)
                        <li>{{ $item->plat->nom ?? 'Plat' }} x {{ $item->quantite }}</li>
                    @endforeach
                </ul>
            </details>
        </td>
    </tr>
    @endforeach
</table>

<h2 style="margin-top:40px;">📊 Ventes par mois</h2>
<div class="chart-box">
    @foreach($parMois as $m)
        <div style="background: #f8f9fe; padding: 15px; border-radius: 10px; text-align: center;">
            <div style="color: #7f8c8d; font-size: 0.8rem;">Mois {{ $m->mois }}</div>
            <div style="font-weight: bold; font-size: 1.1rem;">{{ number_format($m->total, 2) }} HTG</div>
        </div>
    @endforeach
</div>

@endsection