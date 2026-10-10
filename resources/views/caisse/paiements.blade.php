@extends('caisse.layouts.app')

@section('title', 'Paiements')

@php
    $fmt = fn ($n) => number_format((float) $n, 2, ',', ' ');
    $modeColor = ['Espèces' => 'var(--st-ready)', 'Carte' => 'var(--st-new)', 'MonCash' => 'var(--danger)', 'NatCash' => 'var(--st-prep)', 'Virement' => 'var(--text-2)'];
    $isPaginated = $paiements instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $total = $isPaginated ? $paiements->total() : $paiements->count();
    $pageSum = collect($isPaginated ? $paiements->items() : $paiements)->sum('montant');
@endphp

@push('styles')
<style>
    .mode-badge { display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 12.5px; }
    .mode-badge::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--c, var(--text-3)); }
    .mode-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .mode-tabs button { height: 32px; padding: 0 12px; border-radius: 16px; border: 1px solid var(--border); background: var(--surface); font-weight: 700; font-size: 12.5px; color: var(--text-2); cursor: pointer; }
    .mode-tabs button.on { background: var(--text); border-color: var(--text); color: #fff; }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Paiements</h1>
        <p>{{ $total }} {{ $total > 1 ? 'paiements enregistrés' : 'paiement enregistré' }}</p>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="mode-tabs" id="modeTabs">
            <button type="button" class="on" data-mode="">Tous</button>
            @foreach(array_keys($modeColor) as $m)
                <button type="button" data-mode="{{ $m }}">{{ $m }}</button>
            @endforeach
        </div>
        <span class="count" id="pageSum">Cette page : {{ $fmt($pageSum) }} HTG</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Reçu</th>
                    <th>Date</th>
                    <th>Table</th>
                    <th>Mode</th>
                    <th class="right">Montant</th>
                    <th class="right"></th>
                </tr>
            </thead>
            <tbody id="payRows">
                @forelse($paiements as $p)
                    <tr data-mode="{{ $p->mode_paiement }}" data-amount="{{ (float) $p->montant }}">
                        <td class="num">
                            <strong>{{ $p->numero_facture ?? 'FAC-' . str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            <div class="muted" style="font-size:12px">Commande #{{ $p->commande_id }}</div>
                        </td>
                        <td class="num">{{ $p->created_at->format('d/m/Y') }} <span class="muted">{{ $p->created_at->format('H:i') }}</span></td>
                        <td>{{ $p->commande ? 'Table ' . ($p->commande->table->numero ?? $p->commande->restaurant_table_id) : '—' }}</td>
                        <td><span class="mode-badge" style="--c: {{ $modeColor[$p->mode_paiement] ?? 'var(--text-3)' }}">{{ $p->mode_paiement }}</span></td>
                        <td class="right num"><strong>{{ $fmt($p->montant) }}</strong> <span class="muted">HTG</span></td>
                        <td class="right"><a href="{{ route('caisse.facture', $p->id) }}" class="icon-action" title="Voir le reçu"><i data-lucide="file-text"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Aucun paiement enregistré.</td></tr>
                @endforelse
                <tr id="noMatch" hidden><td colspan="6" class="empty">Aucun paiement de ce type sur cette page.</td></tr>
            </tbody>
        </table>
    </div>

    @if($isPaginated && $paiements->hasPages())
        <div class="pager">
            <span>{{ $paiements->firstItem() }}–{{ $paiements->lastItem() }} sur {{ $paiements->total() }}</span>
            <div class="pager-links">
                @if($paiements->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $paiements->previousPageUrl() }}">‹</a>
                @endif
                @foreach($paiements->getUrlRange(max(1, $paiements->currentPage() - 2), min($paiements->lastPage(), $paiements->currentPage() + 2)) as $page => $url)
                    @if($page == $paiements->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($paiements->hasMorePages())
                    <a href="{{ $paiements->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
    const tabs = document.querySelectorAll('#modeTabs button');
    const rows = [...document.querySelectorAll('#payRows tr[data-mode]')];
    const sum  = document.getElementById('pageSum');
    const none = document.getElementById('noMatch');
    const money = (n) => n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    tabs.forEach(t => t.addEventListener('click', () => {
        tabs.forEach(x => x.classList.toggle('on', x === t));
        let total = 0, shown = 0;
        rows.forEach(r => {
            const ok = !t.dataset.mode || r.dataset.mode === t.dataset.mode;
            r.hidden = !ok;
            if (ok) { shown++; total += Number(r.dataset.amount); }
        });
        none.hidden = shown > 0 || rows.length === 0;
        sum.textContent = (t.dataset.mode ? t.dataset.mode + ' · ' : '') + 'cette page : ' + money(total) + ' HTG';
    }));
})();
</script>
@endpush