@extends('admin.layouts.layout')

@section('title', 'Tableau de bord')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $ticketMoyen  = $totalOrders > 0 ? $todaySales / $totalOrders : 0;
    $tablesActives = $activeTables
        ?? \App\Models\Commande::where('archived', false)->distinct('restaurant_table_id')->count('restaurant_table_id');

    // Variation vs hier : affichée seulement si le contrôleur fournit $yesterdaySales
    $delta = (isset($yesterdaySales) && $yesterdaySales > 0)
        ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
        : null;

    $objectif   = $dailyGoal ?? 100000;
    $progress   = $objectif > 0 ? min(($todaySales / $objectif) * 100, 100) : 0;

    $enCuisine  = $newOrders + $preparingOrders + $completedOrders;
    $topMax     = max(optional($topPlats->first())->total ?? 1, 1);

    $statusMap = [
        'nouvelle'       => ['new',    'Nouvelle'],
        'acceptee'       => ['prep',   'Acceptée'],
        'en_preparation' => ['prep',   'En préparation'],
        'prete'          => ['ready',  'Prête'],
        'servie'         => ['served', 'Servie'],
    ];
@endphp

@section('content')

<div class="page-head">
    <div>
        <h1>Tableau de bord</h1>
        <p>{{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} · Service en cours</p>
    </div>

    <div class="page-actions">
        <span class="chip"><i data-lucide="clock"></i><span id="liveClock" class="num">--:--</span></span>
        <form method="POST" action="/admin/commandes/cloturer-journee"
              onsubmit="return confirm('Clôturer la journée ? Toutes les commandes terminées seront archivées.');">
            @csrf
            <button type="submit" class="btn"><i data-lucide="archive"></i> Clôturer la journée</button>
        </form>
    </div>
</div>

{{-- Indicateurs --}}
<section class="kpis">
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Ventes du jour</span>
            <span class="kpi-icon"><i data-lucide="banknote"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($todaySales) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot">
            <span>vs hier</span>
            @if(!is_null($delta))
                <span class="delta {{ $delta >= 0 ? 'up' : 'down' }}">{{ $delta >= 0 ? '+' : '' }}{{ $delta }} %</span>
            @else
                <span>—</span>
            @endif
        </div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Commandes</span>
            <span class="kpi-icon"><i data-lucide="receipt-text"></i></span>
        </div>
        <div class="kpi-value num" id="kpiOrders">{{ $totalOrders }}</div>
        <div class="kpi-foot"><span>aujourd'hui</span><span><span id="kpiNew">{{ $newOrders }}</span> en attente</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Ticket moyen</span>
            <span class="kpi-icon"><i data-lucide="calculator"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($ticketMoyen) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>par commande</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Tables occupées</span>
            <span class="kpi-icon"><i data-lucide="armchair"></i></span>
        </div>
        <div class="kpi-value num">{{ $tablesActives }}</div>
        <div class="kpi-foot"><span>commandes non archivées</span></div>
    </article>
</section>

{{-- Ventes + Top plats --}}
<section class="grid-main">
    <article class="card">
        <div class="card-head">
            <div>
                <h2>Ventes</h2>
                <span class="sub">7 derniers jours, en HTG</span>
            </div>
            <a href="/admin/reports" class="link">Rapports</a>
        </div>
        <div class="card-body">
            <div class="chart-box"><canvas id="salesWeekChart"></canvas></div>
        </div>
    </article>

    <article class="card">
        <div class="card-head">
            <div>
                <h2>Plats les plus vendus</h2>
                <span class="sub">Aujourd'hui</span>
            </div>
            <a href="/admin/plats" class="link">Menu</a>
        </div>
        <div class="card-body">
            @if($topPlats->isEmpty())
                <div class="empty">Aucune vente enregistrée aujourd'hui.</div>
            @else
                <ol class="rank-list">
                    @foreach($topPlats->take(6) as $i => $item)
                        <li>
                            <span class="rank-n">{{ $i + 1 }}</span>
                            <div style="min-width:0">
                                <div class="rank-name">{{ $item->plat->nom ?? 'Plat supprimé' }}</div>
                                <div class="rank-bar"><span style="width: {{ round($item->total / $topMax * 100) }}%"></span></div>
                            </div>
                            <span class="rank-qty num">{{ $item->total }} <small>vendus</small></span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </article>
</section>

{{-- Commandes récentes + Cuisine + Objectif --}}
<section class="grid-main">
    <article class="card">
        <div class="card-head">
            <div>
                <h2>Commandes récentes</h2>
                <span class="sub">Mises à jour en temps réel</span>
            </div>
            <span class="live" id="liveState">Hors ligne</span>
        </div>
        <div class="card-body" style="padding: 12px 0 4px">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Table</th>
                            <th>Heure</th>
                            <th>Statut</th>
                            <th class="right">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="order-list-table">
                        @forelse($recentOrders as $commande)
                            @php [$cls, $label] = $statusMap[$commande->statut] ?? ['served', ucfirst(str_replace('_', ' ', $commande->statut))]; @endphp
                            <tr id="order-row-{{ $commande->id }}">
                                <td><strong class="num">#{{ $commande->id }}</strong></td>
                                <td>Table {{ $commande->table->numero ?? '—' }}</td>
                                <td class="muted num">{{ $commande->created_at->format('H:i') }}</td>
                                <td><span class="status {{ $cls }}">{{ $label }}</span></td>
                                <td class="right num"><strong>{{ $fmt($commande->total) }}</strong> <span class="muted">HTG</span></td>
                                <td class="right"><a href="{{ route('facture.show', $commande) }}" class="link">Facture</a></td>
                            </tr>
                        @empty
                            <tr class="empty-row"><td colspan="6" class="empty">Aucune commande pour le moment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </article>

    <div class="stack">
        <article class="card">
            <div class="card-head">
                <h2>En cuisine</h2>
                <a href="/cuisine" class="link">Ouvrir l'écran cuisine</a>
            </div>
            <div class="card-body">
                <div class="kitchen-bar" id="kitchenBar">
                    <span data-k="new"   style="background: var(--st-new);   width: {{ $enCuisine ? $newOrders / $enCuisine * 100 : 0 }}%"></span>
                    <span data-k="prep"  style="background: var(--st-prep);  width: {{ $enCuisine ? $preparingOrders / $enCuisine * 100 : 0 }}%"></span>
                    <span data-k="ready" style="background: var(--st-ready); width: {{ $enCuisine ? $completedOrders / $enCuisine * 100 : 0 }}%"></span>
                </div>
                <div class="kitchen-stats">
                    <div class="kitchen-stat">
                        <span class="k-label"><i style="background: var(--st-new)"></i>Nouvelles</span>
                        <strong class="num" id="kNew">{{ $newOrders }}</strong>
                    </div>
                    <div class="kitchen-stat">
                        <span class="k-label"><i style="background: var(--st-prep)"></i>En cours</span>
                        <strong class="num" id="kPrep">{{ $preparingOrders }}</strong>
                    </div>
                    <div class="kitchen-stat">
                        <span class="k-label"><i style="background: var(--st-ready)"></i>Prêtes</span>
                        <strong class="num" id="kReady">{{ $completedOrders }}</strong>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-head">
                <h2>Objectif du jour</h2>
                <span class="sub num">{{ $fmt($objectif) }} HTG</span>
            </div>
            <div class="card-body">
                <div class="goal-value">
                    <strong class="num">{{ $fmt($todaySales) }} HTG</strong>
                    <span class="num">{{ round($progress) }} %</span>
                </div>
                <div class="progress"><span style="width: {{ $progress }}%"></span></div>
                <div class="goal-foot">
                    <span>Réalisé</span>
                    <span class="num">Reste {{ $fmt(max($objectif - $todaySales, 0)) }} HTG</span>
                </div>
            </div>
        </article>
    </div>
</section>

<audio id="notifSound" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
</audio>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const salesData   = @json($salesChart ?? []);
    const factureBase = @json(url('/facture'));
    const css = getComputedStyle(document.documentElement);
    const v = (name) => css.getPropertyValue(name).trim();

    /* Horloge */
    const clock = document.getElementById('liveClock');
    const tick = () => clock.textContent = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    tick(); setInterval(tick, 15000);

    /* Graphique des ventes */
    const canvas = document.getElementById('salesWeekChart');
    if (canvas && window.Chart) {
        const ctx = canvas.getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(15, 155, 142, .18)');
        grad.addColorStop(1, 'rgba(15, 155, 142, 0)');

        Chart.defaults.font.family = v('--font');
        Chart.defaults.color = v('--text-3');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(d => d.date),
                datasets: [{
                    data: salesData.map(d => Number(d.total)),
                    borderColor: v('--brand'),
                    backgroundColor: grad,
                    fill: true,
                    borderWidth: 2,
                    tension: .35,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: v('--brand'),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
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
    }

    /* Temps réel */
    const sound = document.getElementById('notifSound');
    let audioReady = false;
    document.addEventListener('click', () => { audioReady = true; }, { once: true });
    const ding = () => { if (audioReady && sound) { sound.currentTime = 0; sound.play().catch(() => {}); } };

    const counters = { new: 'kNew', prep: 'kPrep', ready: 'kReady' };
    const bump = (key, by) => {
        const el = document.getElementById(counters[key]);
        if (el) el.textContent = Math.max(0, Number(el.textContent) + by);
        if (key === 'new') {
            const k = document.getElementById('kpiNew');
            if (k) k.textContent = Math.max(0, Number(k.textContent) + by);
        }
        const n = +document.getElementById('kNew').textContent,
              p = +document.getElementById('kPrep').textContent,
              r = +document.getElementById('kReady').textContent,
              t = n + p + r || 1;
        document.querySelector('#kitchenBar [data-k="new"]').style.width   = (n / t * 100) + '%';
        document.querySelector('#kitchenBar [data-k="prep"]').style.width  = (p / t * 100) + '%';
        document.querySelector('#kitchenBar [data-k="ready"]').style.width = (r / t * 100) + '%';
    };

    const setStatus = (id, cls, label) => {
        const badge = document.querySelector('#order-row-' + id + ' .status');
        if (badge) { badge.className = 'status ' + cls; badge.textContent = label; }
    };

    const td = (content, className) => {
        const cell = document.createElement('td');
        if (className) cell.className = className;
        if (content instanceof Node) cell.appendChild(content); else cell.textContent = content;
        return cell;
    };

    const addRow = (c) => {
        const body = document.getElementById('order-list-table');
        if (!body || document.getElementById('order-row-' + c.id)) return;
        body.querySelector('.empty-row')?.remove();

        const tr = document.createElement('tr');
        tr.id = 'order-row-' + c.id;
        tr.className = 'row-flash';

        const idEl = document.createElement('strong'); idEl.className = 'num'; idEl.textContent = '#' + c.id;
        const badge = document.createElement('span'); badge.className = 'status new'; badge.textContent = 'Nouvelle';
        const total = document.createElement('span');
        total.innerHTML = '<strong></strong> <span class="muted">HTG</span>';
        total.querySelector('strong').textContent = Math.round(Number(c.total)).toLocaleString('fr-FR');
        const link = document.createElement('a'); link.className = 'link'; link.href = factureBase + '/' + c.id; link.textContent = 'Facture';

        tr.append(
            td(idEl),
            td('Table ' + (c.table ? c.table.numero : '—')),
            td(new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }), 'muted num'),
            td(badge),
            td(total, 'right num'),
            td(link, 'right')
        );
        body.prepend(tr);

        const rows = body.querySelectorAll('tr');
        if (rows.length > 10) rows[rows.length - 1].remove();

        const kpi = document.getElementById('kpiOrders');
        if (kpi) kpi.textContent = Number(kpi.textContent) + 1;
    };

    function initEcho(tries = 0) {
        if (!window.Echo) {
            if (tries < 20) setTimeout(() => initEcho(tries + 1), 500);
            return;
        }
        const live = document.getElementById('liveState');
        live.classList.add('on'); live.textContent = 'En direct';

        window.Echo.channel('kitchen')
            .listen('.new-order', (e) => { ding(); addRow(e.commande); bump('new', 1); })
            .listen('.accepted',  (e) => { ding(); setStatus(e.commande.id, 'prep', 'En préparation'); bump('new', -1); bump('prep', 1); })
            .listen('.ready',     (e) => { ding(); setStatus(e.commande.id, 'ready', 'Prête'); bump('prep', -1); bump('ready', 1); });
    }
    initEcho();
})();
</script>
@endpush