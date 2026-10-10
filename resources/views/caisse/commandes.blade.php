@extends('caisse.layouts.app')

@section('title', 'Commandes')

@php
    $fmt = fn ($n) => number_format((float) $n, 2, ',', ' ');
    $statusMap = [
        'nouvelle'       => ['new',    'Nouvelle',       'cuisine'],
        'acceptee'       => ['prep',   'Acceptée',       'cuisine'],
        'en_preparation' => ['prep',   'En préparation', 'cuisine'],
        'preparation'    => ['prep',   'En cuisine',     'cuisine'],
        'prete'          => ['ready',  'Prête',          'prete'],
    ];
    $nbPretes  = $commandes->where('statut', 'prete')->count();
    $nbCuisine = $commandes->count() - $nbPretes;
    $q = trim((string) request('q'));
@endphp

@push('styles')
<style>
    .seg-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .seg-tabs button { height: 34px; padding: 0 12px; border-radius: 17px; border: 1px solid var(--border); background: var(--surface); font-weight: 700; font-size: 13px; color: var(--text-2); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .seg-tabs button b { font-family: var(--mono, monospace); font-size: 12px; }
    .seg-tabs button.on { background: var(--text); border-color: var(--text); color: #fff; }
    .table-pill { display: inline-flex; align-items: center; gap: 6px; font-weight: 800; }
    .table-pill svg.lucide { width: 16px; height: 16px; color: var(--text-3); }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Commandes</h1>
        <p>{{ $nbPretes }} prête{{ $nbPretes > 1 ? 's' : '' }} à encaisser · {{ $nbCuisine }} en cuisine</p>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="seg-tabs" id="tabs">
            <button type="button" data-f="" class="{{ $nbPretes ? '' : 'on' }}">Toutes <b>{{ $commandes->count() }}</b></button>
            <button type="button" data-f="prete" class="{{ $nbPretes ? 'on' : '' }}">À encaisser <b>{{ $nbPretes }}</b></button>
            <button type="button" data-f="cuisine">En cuisine <b>{{ $nbCuisine }}</b></button>
        </div>
        <label class="search" style="margin-left:auto">
            <i data-lucide="search"></i>
            <input type="search" id="q" value="{{ $q }}" placeholder="N° commande ou table">
        </label>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Commande</th>
                    <th>Statut</th>
                    <th>Heure</th>
                    <th class="right">Total</th>
                    <th class="right"></th>
                </tr>
            </thead>
            <tbody id="rows">
                @forelse($commandes as $commande)
                    @php
                        [$cls, $label, $group] = $statusMap[$commande->statut] ?? ['served', ucfirst(str_replace('_', ' ', (string) $commande->statut)), 'autre'];
                        $tableNum = $commande->table->numero ?? $commande->restaurant_table_id;
                    @endphp
                    <tr data-group="{{ $group }}" data-search="{{ $commande->id }} table {{ $tableNum }}">
                        <td><span class="table-pill"><i data-lucide="armchair"></i>{{ $tableNum }}</span></td>
                        <td class="num">#{{ $commande->id }}</td>
                        <td><span class="status {{ $cls }}">{{ $label }}</span></td>
                        <td class="num muted">{{ $commande->created_at->isToday() ? $commande->created_at->format('H:i') : $commande->created_at->format('d/m H:i') }}</td>
                        <td class="right num"><strong>{{ $fmt($commande->total) }}</strong> <span class="muted">HTG</span></td>
                        <td class="right">
                            @if($commande->statut === 'prete')
                                <a href="{{ route('caisse.encaisser', $commande->id) }}" class="btn btn-primary"><i data-lucide="wallet"></i> Encaisser</a>
                            @else
                                <span class="muted" style="font-size:12.5px">En attente de la cuisine</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Aucune commande en cours. Les nouvelles commandes apparaîtront ici.</td></tr>
                @endforelse
                <tr id="noMatch" hidden><td colspan="6" class="empty">Aucune commande ne correspond.</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const tabs = document.querySelectorAll('#tabs button');
    const q    = document.getElementById('q');
    const rows = [...document.querySelectorAll('#rows tr[data-group]')];
    const none = document.getElementById('noMatch');
    let filter = document.querySelector('#tabs button.on')?.dataset.f || '';

    function apply() {
        const s = q.value.trim().toLowerCase().replace('#', '');
        let shown = 0;
        rows.forEach(r => {
            const ok = (!filter || r.dataset.group === filter) && (!s || r.dataset.search.includes(s));
            r.hidden = !ok; if (ok) shown++;
        });
        none.hidden = shown > 0 || rows.length === 0;
    }
    tabs.forEach(t => t.addEventListener('click', () => {
        filter = t.dataset.f;
        tabs.forEach(x => x.classList.toggle('on', x === t));
        apply();
    }));
    q.addEventListener('input', apply);
    apply();

    // Rafraîchit la liste toutes les 20 s (sauf si l'utilisateur tape une recherche)
    setInterval(() => { if (!q.value && document.visibilityState === 'visible') location.reload(); }, 20000);
})();
</script>
@endpush