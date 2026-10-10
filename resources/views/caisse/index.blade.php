@extends('caisse.layouts.app')

@section('title', 'Tableau de bord')

@php
    $fmt = fn ($n) => number_format((float) $n, 2, ',', ' ');

    // Modes de paiement : clé API, libellé, couleur
    $modes = [
        ['key' => 'cashCount',     'label' => 'Espèces',  'color' => 'var(--st-ready)'],
        ['key' => 'cardCount',     'label' => 'Carte',    'color' => 'var(--st-new)'],
        ['key' => 'moncashCount',  'label' => 'MonCash',  'color' => 'var(--danger)'],
        ['key' => 'natcashCount',  'label' => 'NatCash',  'color' => 'var(--st-prep)'],
        ['key' => 'virementCount', 'label' => 'Virement', 'color' => 'var(--text-2)'],
    ];
    $modeCounts = [
        'cashCount'     => (int) ($cashCount ?? 0),
        'cardCount'     => (int) ($cardCount ?? 0),
        'moncashCount'  => (int) ($moncashCount ?? 0),
        'natcashCount'  => (int) ($natcashCount ?? 0),
        'virementCount' => (int) ($virementCount ?? 0),
    ];
    $modeTotal = max(array_sum($modeCounts), 0);

    $modeColor = collect($modes)->mapWithKeys(fn ($m) => [$m['label'] => $m['color']]);
@endphp

@push('styles')
<style>
    .ready-row td { vertical-align: middle; }
    .ready-row .btn-primary { height: 40px; }
    .table-pill { display: inline-flex; align-items: center; gap: 6px; font-weight: 800; }
    .table-pill svg.lucide { width: 16px; height: 16px; color: var(--text-3); }
    .wait { font-size: 12.5px; color: var(--text-3); }
    .wait.long { color: var(--danger); font-weight: 700; }

    .mode-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 12px; }
    .mode-list li { display: grid; grid-template-columns: 90px 1fr auto; align-items: center; gap: 12px; font-size: 13.5px; }
    .mode-list .name { display: flex; align-items: center; gap: 8px; font-weight: 700; }
    .mode-list .name i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .mode-list .bar { height: 6px; background: var(--bg); border-radius: 3px; overflow: hidden; }
    .mode-list .bar span { display: block; height: 100%; border-radius: 3px; transition: width .4s; }
    .mode-list .val { font-weight: 800; min-width: 70px; text-align: right; }
    .mode-list .val small { color: var(--text-3); font-weight: 600; }

    .mode-badge { display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 12.5px; }
    .mode-badge::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--c, var(--text-3)); }
    .updated-at { font-size: 12px; color: var(--text-3); }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Bonjour {{ \Illuminate\Support\Str::of(auth()->user()->name ?? '')->before(' ') }}</h1>
        <p>{{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} · <span class="updated-at" id="updatedAt">mis à jour à l'instant</span></p>
    </div>
</div>

{{-- Indicateurs --}}
<section class="kpis">
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Encaissé aujourd'hui</span>
            <span class="kpi-icon"><i data-lucide="banknote"></i></span>
        </div>
        <div class="kpi-value num"><span id="kCa">{{ $fmt($chiffreAffairesJour) }}</span><span class="unit">HTG</span></div>
        <div class="kpi-foot"><span><span id="kPayees">{{ $countPayeesJour }}</span> paiements</span></div>
    </article>
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">À encaisser</span>
            <span class="kpi-icon" style="background: var(--st-ready-bg); color: var(--st-ready)"><i data-lucide="bell-ring"></i></span>
        </div>
        <div class="kpi-value num" id="kPretes">{{ $countPretes }}</div>
        <div class="kpi-foot"><span>commandes prêtes</span></div>
    </article>
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">En cuisine</span>
            <span class="kpi-icon" style="background: var(--st-prep-bg); color: var(--st-prep)"><i data-lucide="chef-hat"></i></span>
        </div>
        <div class="kpi-value num" id="kAttente">{{ $countEnAttente }}</div>
        <div class="kpi-foot"><span>bientôt à encaisser</span></div>
    </article>
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Ticket moyen</span>
            <span class="kpi-icon"><i data-lucide="calculator"></i></span>
        </div>
        <div class="kpi-value num"><span id="kTicket">{{ $countPayeesJour > 0 ? number_format($chiffreAffairesJour / $countPayeesJour, 0, ',', ' ') : '0' }}</span><span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>aujourd'hui</span></div>
    </article>
</section>

{{-- Commandes prêtes : la priorité du caissier --}}
<article class="card" style="margin-bottom: 16px">
    <div class="card-head" style="padding-bottom: 12px">
        <div>
            <h2>Commandes prêtes à encaisser</h2>
            <span class="sub">La plus ancienne en premier · actualisation automatique</span>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Commande</th>
                    <th>Depuis</th>
                    <th class="right">Montant</th>
                    <th class="right"></th>
                </tr>
            </thead>
            <tbody id="readyRows">
                @forelse($commandesPretes->sortBy('created_at') as $commande)
                    <tr class="ready-row" data-id="{{ $commande->id }}">
                        <td><span class="table-pill"><i data-lucide="armchair"></i>{{ $commande->table->numero ?? $commande->restaurant_table_id }}</span></td>
                        <td class="num">#{{ $commande->id }}</td>
                        <td><span class="wait num" data-since="{{ \Carbon\Carbon::parse($commande->created_at)->timestamp }}">{{ \Carbon\Carbon::parse($commande->created_at)->format('H:i') }}</span></td>
                        <td class="right num"><strong>{{ $fmt($commande->total) }}</strong> <span class="muted">HTG</span></td>
                        <td class="right">
                            <a href="{{ route('caisse.encaisser', $commande->id) }}" class="btn btn-primary"><i data-lucide="wallet"></i> Encaisser</a>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5" class="empty">Aucune commande à encaisser pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</article>

<section class="grid-main" style="align-items: start">
    {{-- Derniers paiements --}}
    <article class="card">
        <div class="card-head" style="padding-bottom: 12px">
            <div>
                <h2>Derniers paiements</h2>
                <span class="sub">Aujourd'hui</span>
            </div>
            <a href="{{ url('/caisse/paiements') }}" class="link">Tout voir</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reçu</th>
                        <th>Commande</th>
                        <th>Mode</th>
                        <th>Heure</th>
                        <th class="right">Montant</th>
                    </tr>
                </thead>
                <tbody id="payRows">
                    @forelse($derniersPaiements as $p)
                        <tr>
                            <td class="num"><strong>FAC-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                            <td class="num muted">#{{ $p->commande_id }}</td>
                            <td><span class="mode-badge" style="--c: {{ $modeColor[$p->mode_paiement] ?? 'var(--text-3)' }}">{{ $p->mode_paiement }}</span></td>
                            <td class="num muted">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</td>
                            <td class="right num"><strong>{{ $fmt($p->montant) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Aucun paiement aujourd'hui.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="stack">
        {{-- Modes de paiement --}}
        <article class="card">
            <div class="card-head">
                <div>
                    <h2>Modes de paiement</h2>
                    <span class="sub">Nombre de paiements aujourd'hui</span>
                </div>
            </div>
            <div class="card-body">
                <ul class="mode-list" id="modeList">
                    @foreach($modes as $m)
                        @php $c = $modeCounts[$m['key']]; $pct = $modeTotal ? round($c / $modeTotal * 100) : 0; @endphp
                        <li data-key="{{ $m['key'] }}">
                            <span class="name"><i style="background: {{ $m['color'] }}"></i>{{ $m['label'] }}</span>
                            <span class="bar"><span style="width: {{ $pct }}%; background: {{ $m['color'] }}"></span></span>
                            <span class="val num"><span data-count>{{ $c }}</span> <small data-pct>{{ $pct }} %</small></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </article>

        {{-- Évolution --}}
        <article class="card">
            <div class="card-head">
                <div>
                    <h2>Encaissements</h2>
                    <span class="sub">Évolution récente, en HTG</span>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 200px"><canvas id="salesChart"></canvas></div>
            </div>
        </article>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const API_URL      = @json(route('caisse.api.dashboard'));
    const ENCAISSER    = @json(url('/caisse/encaisser'));
    const MODE_COLORS  = @json($modeColor);
    const css = getComputedStyle(document.documentElement);
    const v = (n) => css.getPropertyValue(n).trim();

    const money = (n) => Number(n || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const pad = (n) => String(n).padStart(5, '0');
    const el = (tag, cls, text) => { const n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; };
    const td = (child, cls) => { const c = el('td', cls); if (child instanceof Node) c.append(child); else c.textContent = child; return c; };
    const icons = () => window.lucide && lucide.createIcons();
    // Accepte "14:32", "10/10/2026 14:32" ou une date ISO
    const hhmm = (x) => {
        const s = String(x ?? '');
        if (/\d{4}-\d{2}-\d{2}T/.test(s) && Number.isFinite(Date.parse(s))) {
            return new Date(s).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
        const m = s.match(/(\d{1,2}):(\d{2})/);
        return m ? m[1].padStart(2, '0') + ':' + m[2] : '';
    };
    const toTs = (x) => { const t = Date.parse(x); return Number.isFinite(t) && /\d{4}-\d{2}-\d{2}/.test(String(x)) ? t / 1000 : null; };

    /* ---------- Graphique ---------- */
    const canvas = document.getElementById('salesChart');
    if (canvas && window.Chart) {
        Chart.defaults.font.family = v('--font');
        Chart.defaults.color = v('--text-3');
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json($salesLabels ?? []),
                datasets: [{ data: @json($salesData ?? []), backgroundColor: v('--brand'), borderRadius: 4, maxBarThickness: 28 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { displayColors: false, callbacks: { label: (c) => money(c.parsed.y) + ' HTG' } } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { beginAtZero: true, border: { display: false }, grid: { color: v('--border') }, ticks: { maxTicksLimit: 4, callback: (n) => n >= 1000 ? (n / 1000) + 'k' : n } }
                }
            }
        });
    }

    /* ---------- Temps d'attente des commandes prêtes ---------- */
    function tickWaits() {
        const now = Date.now() / 1000;
        document.querySelectorAll('[data-since]').forEach(s => {
            const min = Math.max(0, Math.floor((now - Number(s.dataset.since)) / 60));
            s.textContent = min < 1 ? "à l'instant" : min + ' min';
            s.classList.toggle('long', min >= 15);
        });
    }
    tickWaits(); setInterval(tickWaits, 30000);

    /* ---------- Rafraîchissement ---------- */
    // Commandes déjà affichées au chargement : pas de son pour elles
    const known = new Set([...document.querySelectorAll('#readyRows tr[data-id]')].map(tr => Number(tr.dataset.id)));

    function renderReady(list) {
        const body = document.getElementById('readyRows');
        body.innerHTML = '';
        if (!list.length) {
            const tr = el('tr', 'empty-row');
            const c = td('Aucune commande à encaisser pour le moment.', 'empty'); c.colSpan = 5;
            tr.append(c); body.append(tr);
            return [];
        }
        const fresh = [];
        list.forEach(c => {
            const tr = el('tr', 'ready-row'); tr.dataset.id = c.id;
            const tableNum = (c.table && c.table.numero) ? c.table.numero : c.restaurant_table_id;

            const pill = el('span', 'table-pill'); pill.innerHTML = '<i data-lucide="armchair"></i>'; pill.append(String(tableNum));
            const since = el('span', 'wait num', hhmm(c.created_at));
            const ts = toTs(c.created_at);
            if (ts) since.dataset.since = ts;
            const amount = el('span'); amount.append(el('strong', null, money(c.total)), ' ', el('span', 'muted', 'HTG'));
            const btn = el('a', 'btn btn-primary'); btn.href = ENCAISSER + '/' + c.id; btn.innerHTML = '<i data-lucide="wallet"></i>'; btn.append(' Encaisser');

            tr.append(td(pill), td('#' + c.id, 'num'), td(since), td(amount, 'right num'), td(btn, 'right'));
            body.append(tr);

            if (!known.has(Number(c.id))) { known.add(Number(c.id)); fresh.push({ id: c.id, table: tableNum }); }
        });
        return fresh;
    }

    function renderPayments(list) {
        const body = document.getElementById('payRows');
        body.innerHTML = '';
        if (!list.length) {
            const tr = el('tr'); const c = td("Aucun paiement aujourd'hui.", 'empty'); c.colSpan = 5; tr.append(c); body.append(tr);
            return;
        }
        list.forEach(p => {
            const mode = el('span', 'mode-badge', p.mode_paiement);
            mode.style.setProperty('--c', MODE_COLORS[p.mode_paiement] || v('--text-3'));
            const tr = el('tr');
            tr.append(
                td(el('strong', null, 'FAC-' + pad(p.id)), 'num'),
                td('#' + p.commande_id, 'num muted'),
                td(mode),
                td(hhmm(p.created_at), 'num muted'),
                td(el('strong', null, money(p.montant)), 'right num')
            );
            body.append(tr);
        });
    }

    function renderModes(rep) {
        if (!rep) return;
        const items = [...document.querySelectorAll('#modeList li')];
        const total = items.reduce((s, li) => s + Number(rep[li.dataset.key] || 0), 0);
        items.forEach(li => {
            const n = Number(rep[li.dataset.key] || 0);
            const pct = total ? Math.round(n / total * 100) : 0;
            li.querySelector('[data-count]').textContent = n;
            li.querySelector('[data-pct]').textContent = pct + ' %';
            li.querySelector('.bar span').style.width = pct + '%';
        });
    }

    async function refresh() {
        try {
            const res = await fetch(API_URL, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            const s = data.stats || {};

            document.getElementById('kCa').textContent      = money(s.chiffre);
            document.getElementById('kPretes').textContent  = s.pretes ?? 0;
            document.getElementById('kAttente').textContent = s.attente ?? 0;
            document.getElementById('kPayees').textContent  = s.payees ?? 0;
            document.getElementById('kTicket').textContent  = s.payees ? Math.round(s.chiffre / s.payees).toLocaleString('fr-FR') : '0';

            const fresh = renderReady(data.commandesPretes || []);
            renderPayments(data.derniersPaiements || []);
            renderModes(data.repatisyon);
            icons(); tickWaits();

            fresh.forEach(c => caisseToast('Commande #' + c.id + ' prête · table ' + c.table));
            if (fresh.length) caisseDing();

            document.getElementById('updatedAt').textContent = 'mis à jour à ' + new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            console.error('Actualisation impossible', e);
        }
    }

    setInterval(refresh, 10000);
})();
</script>
@endpush