@extends('admin.layouts.layout')

@section('title', 'Notifications & rappels')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $fmtQ = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', ' '), '0'), ',');

    // Le contrôleur peut envoyer un tableau vide si la table n'existe pas
    $employes    = collect($employes ?? []);
    $stockAlerts = collect($stockAlerts ?? []);

    $masse       = $employes->sum('salaire');
    $joursFin    = (int) now()->startOfDay()->diffInDays(now()->endOfMonth()->startOfDay(), true);
    $moisLabel   = ucfirst(now()->locale('fr')->isoFormat('MMMM YYYY'));
    $moisKey     = now()->format('Y-m');

    $roles = ['caissiere' => 'Caissière', 'serveur' => 'Serveur', 'serveuse' => 'Serveuse', 'cuisine' => 'Cuisinier', 'cuisinier' => 'Cuisinier'];
@endphp

@push('styles')
<style>
    .paid-row td { color: var(--text-3); }
    .paid-row td strong { color: var(--text-3); text-decoration: line-through; }
    .check {
        display: inline-flex; align-items: center; gap: 8px; cursor: pointer; user-select: none;
        font-weight: 700; font-size: 13px; color: var(--text-2);
        padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border);
    }
    .check input { width: 16px; height: 16px; accent-color: var(--brand); margin: 0; }
    .check:has(input:checked) { color: var(--st-ready); border-color: #bfe5cc; background: var(--st-ready-bg); }
    .device-note { display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-top: 1px solid var(--border); background: #fafbfc; color: var(--text-3); font-size: 12.5px; border-radius: 0 0 var(--radius) var(--radius); }
    .device-note svg.lucide { width: 15px; height: 15px; flex-shrink: 0; }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Notifications & rappels</h1>
        <p>{{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}</p>
    </div>
</div>

{{-- Indicateurs --}}
<section class="kpis">
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Alertes stock</span>
            <span class="kpi-icon" @if($stockAlerts->count()) style="background:#fdecec;color:var(--danger)" @endif><i data-lucide="alert-triangle"></i></span>
        </div>
        <div class="kpi-value num">{{ $stockAlerts->count() }}</div>
        <div class="kpi-foot"><span>{{ $stockAlerts->count() ? 'à réapprovisionner' : 'tout est au-dessus du seuil' }}</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Masse salariale</span>
            <span class="kpi-icon"><i data-lucide="banknote"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($masse) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>{{ $employes->count() }} employés · par mois</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Salaires cochés</span>
            <span class="kpi-icon"><i data-lucide="check-circle-2"></i></span>
        </div>
        <div class="kpi-value num"><span id="paidCount">0</span><span class="unit">/ {{ $employes->count() }}</span></div>
        <div class="kpi-foot"><span>{{ $moisLabel }}</span></div>
    </article>

    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Fin du mois</span>
            <span class="kpi-icon"><i data-lucide="calendar-clock"></i></span>
        </div>
        <div class="kpi-value num">{{ $joursFin }}<span class="unit">{{ $joursFin > 1 ? 'jours' : 'jour' }}</span></div>
        <div class="kpi-foot"><span>{{ $joursFin === 0 ? "c'est aujourd'hui" : 'avant le ' . now()->endOfMonth()->format('d/m') }}</span></div>
    </article>
</section>

{{-- Alertes de stock --}}
<article class="card" style="margin-bottom: 16px">
    <div class="card-head" style="padding-bottom: 12px">
        <div>
            <h2>Stock critique</h2>
            <span class="sub">Articles au seuil d'alerte ou en dessous</span>
        </div>
        <a href="/admin/stock" class="link">Voir le stock</a>
    </div>

    @if($stockAlerts->isEmpty())
        <div class="empty">
            <i data-lucide="check-circle-2" style="color: var(--st-ready)"></i><br>
            Aucune alerte : tous les articles sont au-dessus de leur seuil.
        </div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th class="right">En stock</th>
                        <th class="right">Seuil</th>
                        <th class="right">Manque</th>
                        <th class="right"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockAlerts->sortBy(fn ($i) => $i->seuil_alerte > 0 ? $i->quantite_actuelle / $i->seuil_alerte : 0) as $item)
                        <tr>
                            <td><strong>{{ $item->nom }}</strong></td>
                            <td class="right num">
                                <span class="status low">{{ $fmtQ($item->quantite_actuelle) }} {{ $item->unite }}</span>
                            </td>
                            <td class="right num muted">{{ $fmtQ($item->seuil_alerte) }} {{ $item->unite }}</td>
                            <td class="right num">{{ $fmtQ(max($item->seuil_alerte - $item->quantite_actuelle, 0)) }} {{ $item->unite }}</td>
                            <td class="right">
                                <a href="/admin/stock-mouvement?product={{ $item->id }}&type=entrant" class="btn"><i data-lucide="plus"></i> Entrée</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</article>

{{-- Salaires --}}
<article class="card">
    <div class="card-head" style="padding-bottom: 12px">
        <div>
            <h2>Salaires · {{ $moisLabel }}</h2>
            <span class="sub">Total à verser : {{ $fmt($masse) }} HTG</span>
        </div>
        <a href="/admin/employes" class="link">Voir les employés</a>
    </div>

    @if($employes->isEmpty())
        <div class="empty">Aucun employé enregistré.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Fonction</th>
                        <th>Téléphone</th>
                        <th class="right">Salaire</th>
                        <th class="right">Payé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employes->sortBy('nom') as $emp)
                        <tr data-emp="{{ $emp->id }}">
                            <td><strong>{{ $emp->prenom }} {{ $emp->nom }}</strong></td>
                            <td class="muted">{{ $roles[$emp->role] ?? ucfirst((string) $emp->role) }}</td>
                            <td class="num">
                                @if($emp->telephone)
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $emp->telephone) }}">{{ $emp->telephone }}</a>
                                @else <span class="muted">—</span> @endif
                            </td>
                            <td class="right num"><strong>{{ $fmt($emp->salaire) }}</strong> <span class="muted">HTG</span></td>
                            <td class="right">
                                <label class="check">
                                    <input type="checkbox" data-paid="{{ $emp->id }}">
                                    <span>Payé</span>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="device-note">
            <i data-lucide="info"></i>
            Aide-mémoire : les cases cochées sont gardées sur cet appareil uniquement et se remettent à zéro chaque mois. Elles ne remplacent pas un registre de paie.
        </div>
    @endif
</article>

@endsection

@push('scripts')
<script>
(function () {
    const KEY = 'resto.salaires.{{ $moisKey }}';
    const boxes = [...document.querySelectorAll('[data-paid]')];
    const counter = document.getElementById('paidCount');

    let paid = [];
    try { paid = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { paid = []; }

    function render() {
        boxes.forEach(b => {
            b.checked = paid.includes(b.dataset.paid);
            b.closest('tr').classList.toggle('paid-row', b.checked);
        });
        counter.textContent = boxes.filter(b => b.checked).length;
    }

    boxes.forEach(b => b.addEventListener('change', () => {
        paid = boxes.filter(x => x.checked).map(x => x.dataset.paid);
        try { localStorage.setItem(KEY, JSON.stringify(paid)); } catch (e) {}
        render();
    }));

    render();
})();
</script>
@endpush