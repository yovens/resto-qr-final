@extends('admin.layouts.layout')

@section('title', 'Rapports')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $months = [
        '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',      '04' => 'Avril',
        '05' => 'Mai',     '06' => 'Juin',    '07' => 'Juillet',   '08' => 'Août',
        '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre',
    ];
    $selMonth = str_pad((string) $selectedMonth, 2, '0', STR_PAD_LEFT);

    // Les 12 mois de l'année, y compris ceux sans vente
    $stats = collect($monthlyStats)->keyBy(fn ($s) => str_pad((string) $s->mois, 2, '0', STR_PAD_LEFT));
    $rows = collect($months)->map(fn ($nom, $num) => (object) [
        'num'       => $num,
        'nom'       => $nom,
        'commandes' => (int)   ($stats[$num]->total_commandes ?? 0),
        'ventes'    => (float) ($stats[$num]->total_ventes ?? 0),
    ]);

    $totalCmdAnnee = $rows->sum('commandes');
    $ticketMois    = $commandesMoisCount > 0 ? $ventesMois / $commandesMoisCount : 0;
    $moisActifs    = $rows->where('ventes', '>', 0)->count();
    $moyenneMois   = $moisActifs > 0 ? $ventesAnnee / $moisActifs : 0;
    $meilleur      = $rows->sortByDesc('ventes')->first();
@endphp

@push('styles')
<style>
    .share { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
    .share-bar { width: 70px; height: 5px; border-radius: 3px; background: var(--bg); overflow: hidden; }
    .share-bar span { display: block; height: 100%; background: var(--brand); }
    .table tr.is-selected td { background: var(--brand-50); }
    .table tr.is-empty td { color: var(--text-3); }
    .table tfoot td { border-top: 1px solid var(--border-strong); font-weight: 800; padding: 12px 20px; }

    @media print {
        .sidebar, .topbar, .page-actions, .no-print { display: none !important; }
        .main { margin: 0 !important; }
        .content { padding: 0 !important; max-width: none; }
        body { background: #fff; }
        .card { break-inside: avoid; }
        .print-title { display: block !important; }
    }
</style>
@endpush

@section('content')

<div class="print-title" style="display:none; margin-bottom:12px">
    <strong style="font-size:18px">Resto Kay-Y — Rapport {{ $months[$selMonth] ?? '' }} {{ $selectedYear }}</strong><br>
    <span class="muted">Édité le {{ now()->format('d/m/Y à H:i') }}</span>
</div>

<div class="page-head">
    <div>
        <h1>Rapports</h1>
        <p>{{ $months[$selMonth] ?? '' }} {{ $selectedYear }} · vue mensuelle et annuelle</p>
    </div>

    <div class="page-actions">
        <form action="/admin/reports" method="GET" class="filters">
            <select name="month" class="select" onchange="this.form.submit()">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" @selected($selMonth === $num)>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" class="select" onchange="this.form.submit()">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" @selected($selectedYear == $yr)>{{ $yr }}</option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn">Afficher</button></noscript>
        </form>
        <button type="button" class="btn" onclick="window.print()"><i data-lucide="printer"></i> Imprimer</button>
    </div>
</div>

{{-- Indicateurs --}}
<section class="kpis">
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Ventes · {{ $months[$selMonth] ?? '' }}</span>
            <span class="kpi-icon"><i data-lucide="calendar"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($ventesMois) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>{{ $commandesMoisCount }} commandes</span><span class="num">{{ $fmt($ticketMois) }} HTG / cmd</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Ventes · {{ $selectedYear }}</span>
            <span class="kpi-icon"><i data-lucide="trending-up"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($ventesAnnee) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>{{ $totalCmdAnnee }} commandes</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Moyenne mensuelle</span>
            <span class="kpi-icon"><i data-lucide="calculator"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($moyenneMois) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>sur {{ $moisActifs }} {{ $moisActifs > 1 ? 'mois actifs' : 'mois actif' }}</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Chiffre d'affaires total</span>
            <span class="kpi-icon"><i data-lucide="wallet"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($totalVentesGlobal) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>depuis l'ouverture</span></div>
    </article>
</section>

{{-- Graphique --}}
<article class="card" style="margin-bottom: 16px">
    <div class="card-head">
        <div>
            <h2>Ventes mensuelles {{ $selectedYear }}</h2>
            <span class="sub">
                En HTG
                @if($meilleur && $meilleur->ventes > 0) · meilleur mois : {{ $meilleur->nom }} ({{ $fmt($meilleur->ventes) }} HTG) @endif
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-box" style="height: 300px"><canvas id="salesChart"></canvas></div>
    </div>
</article>

{{-- Résumé mensuel --}}
<article class="card">
    <div class="card-head" style="padding-bottom: 12px">
        <div>
            <h2>Résumé mensuel</h2>
            <span class="sub">Année {{ $selectedYear }}</span>
        </div>
    </div>

    @if($ventesAnnee <= 0)
        <div class="empty">Aucune commande payée enregistrée en {{ $selectedYear }}.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th class="right">Commandes</th>
                        <th class="right">Ticket moyen</th>
                        <th class="right">Chiffre d'affaires</th>
                        <th class="right">Part de l'année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                        @php $part = $ventesAnnee > 0 ? $r->ventes / $ventesAnnee * 100 : 0; @endphp
                        <tr class="{{ $r->num === $selMonth ? 'is-selected' : '' }} {{ $r->ventes <= 0 ? 'is-empty' : '' }}">
                            <td><strong>{{ $r->nom }}</strong></td>
                            <td class="right num">{{ $r->commandes ?: '—' }}</td>
                            <td class="right num">{{ $r->commandes ? $fmt($r->ventes / $r->commandes) : '—' }}</td>
                            <td class="right num">
                                @if($r->ventes > 0)<strong>{{ $fmt($r->ventes) }}</strong> <span class="muted">HTG</span>@else — @endif
                            </td>
                            <td class="right">
                                <div class="share">
                                    <span class="num muted">{{ $part > 0 ? round($part, 1).' %' : '' }}</span>
                                    <span class="share-bar"><span style="width: {{ $part }}%"></span></span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total {{ $selectedYear }}</td>
                        <td class="right num">{{ $totalCmdAnnee }}</td>
                        <td class="right num">{{ $totalCmdAnnee ? $fmt($ventesAnnee / $totalCmdAnnee) : '—' }}</td>
                        <td class="right num">{{ $fmt($ventesAnnee) }} <span class="muted">HTG</span></td>
                        <td class="right num">100 %</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</article>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const canvas = document.getElementById('salesChart');
    if (!canvas || !window.Chart) return;

    const labels   = @json($rows->map(fn ($r) => mb_substr($r->nom, 0, 3))->values());
    const values   = @json($rows->pluck('ventes')->values());
    const selected = @json(array_search($selMonth, array_keys($months)));

    const css = getComputedStyle(document.documentElement);
    const v = (n) => css.getPropertyValue(n).trim();

    Chart.defaults.font.family = v('--font');
    Chart.defaults.color = v('--text-3');

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: values.map((_, i) => i === selected ? v('--brand-600') : v('--brand')),
                borderRadius: 5,
                maxBarThickness: 42,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: v('--text'),
                    padding: 10,
                    displayColors: false,
                    callbacks: { label: (c) => c.parsed.y.toLocaleString('fr-FR') + ' HTG' }
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: v('--border') },
                    ticks: { maxTicksLimit: 5, callback: (n) => n >= 1000 ? (n / 1000) + 'k' : n }
                }
            }
        }
    });
})();
</script>
@endpush