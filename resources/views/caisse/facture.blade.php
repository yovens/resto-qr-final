@extends('caisse.layouts.app')

@section('title', 'Reçu ' . ($paiement->numero_facture ?? 'FAC-' . str_pad($paiement->id, 5, '0', STR_PAD_LEFT)))

@php
    $fmt = fn ($n) => number_format((float) $n, 2, ',', ' ');
    $numero   = $paiement->numero_facture ?? 'FAC-' . str_pad($paiement->id, 5, '0', STR_PAD_LEFT);
    $commande = $paiement->commande;
    $items    = $commande->items ?? collect();

    $lignes = collect($items)->map(function ($item) {
        $pu = (float) ($item->prix ?? $item->prix_unitaire ?? $item->plat->prix ?? 0);
        return (object) ['nom' => $item->plat->nom ?? 'Article supprimé', 'qte' => (int) $item->quantite, 'pu' => $pu, 'total' => $pu * (int) $item->quantite];
    });
    $sousTotal = $lignes->sum('total');
    $montant   = (float) $paiement->montant;
    $ecart     = round($montant - $sousTotal, 2);
    $taux      = $sousTotal > 0 ? round($ecart / $sousTotal * 100) : 0;
    $table     = $commande->table->numero ?? $commande->restaurant_table_id ?? '—';
    $modeLabel = ['Carte' => 'Carte bancaire'][$paiement->mode_paiement] ?? $paiement->mode_paiement;
@endphp

@push('styles')
<style>
    .receipt { max-width: 640px; }
    .r-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 22px 24px; border-bottom: 1px solid var(--border); }
    .r-brand strong { display: block; font-size: 17px; font-weight: 800; }
    .r-brand small { color: var(--text-3); }
    .r-num { text-align: right; }
    .r-num .n { font-family: var(--mono, monospace); font-size: 18px; font-weight: 700; }
    .r-num .d { color: var(--text-2); font-size: 13px; }
    .r-paid { display: flex; align-items: center; gap: 10px; margin: 16px 24px 0; padding: 10px 14px; border-radius: var(--radius-sm); background: var(--st-ready-bg); color: #126b33; font-weight: 700; font-size: 13.5px; }
    .r-paid svg.lucide { width: 18px; height: 18px; }
    .r-meta { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 16px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
    .r-meta > div { padding: 12px 24px; border-right: 1px solid var(--border); }
    .r-meta > div:last-child { border-right: 0; }
    .r-meta span { display: block; font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 3px; }
    .r-meta strong { font-size: 14px; }
    .r-lines th:first-child, .r-lines td:first-child { padding-left: 24px; }
    .r-lines th:last-child, .r-lines td:last-child { padding-right: 24px; }
    .r-totals { margin-left: auto; max-width: 300px; padding: 12px 24px 20px; }
    .r-totals .row { display: flex; justify-content: space-between; padding: 4px 0; color: var(--text-2); }
    .r-totals .grand { border-top: 1px solid var(--border-strong); margin-top: 6px; padding-top: 10px; color: var(--text); font-size: 18px; font-weight: 800; }
    .r-foot { padding: 14px 24px; border-top: 1px dashed var(--border-strong); text-align: center; color: var(--text-3); font-size: 13px; }

    @media (max-width: 640px) {
        .r-head { flex-direction: column; } .r-num { text-align: left; }
        .r-meta { grid-template-columns: 1fr 1fr; }
        .r-meta > div:nth-child(2) { border-right: 0; }
        .r-meta > div:nth-child(-n+2) { border-bottom: 1px solid var(--border); }
    }

    @media print {
        .sidebar, .topbar, .page-head, .back-link, .no-print, .toasts-caisse { display: none !important; }
        .main { margin: 0 !important; } .content { padding: 0 !important; max-width: none; }
        body { background: #fff; } .receipt { border: 0; max-width: none; }
        @page { margin: 14mm; }
        body.ticket { font-size: 12px; }
        body.ticket .receipt { width: 72mm; margin: 0 auto; }
        body.ticket .r-head { flex-direction: column; align-items: center; text-align: center; padding: 4px 0 8px; gap: 4px; }
        body.ticket .r-num { text-align: center; }
        body.ticket .r-paid { margin: 8px 0 0; background: none; padding: 0; justify-content: center; }
        body.ticket .r-meta { grid-template-columns: 1fr 1fr; }
        body.ticket .r-meta > div { padding: 4px 0; border: 0 !important; }
        body.ticket .r-lines th, body.ticket .r-lines td { padding: 3px 0 !important; font-size: 11.5px; white-space: normal; }
        body.ticket .r-lines .col-pu { display: none; }
        body.ticket .r-totals { max-width: none; padding: 6px 0 8px; }
        body.ticket .r-foot { padding: 8px 0 0; }
    }
</style>
@endpush

@section('content')

<a href="{{ url('/caisse/paiements') }}" class="back-link no-print"><i data-lucide="arrow-left"></i> Paiements</a>
<div class="page-head">
    <div>
        <h1>Reçu {{ $numero }}</h1>
        <p>Table {{ $table }} · {{ $paiement->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="page-actions no-print">
        <a href="{{ url('/caisse/dashboard') }}" class="btn"><i data-lucide="layout-dashboard"></i> Retour à la caisse</a>
        <button type="button" class="btn" onclick="printAs('a4')"><i data-lucide="printer"></i> A4</button>
        <button type="button" class="btn btn-primary" onclick="printAs('ticket')"><i data-lucide="receipt"></i> Imprimer le ticket</button>
    </div>
</div>

<article class="card receipt">
    <header class="r-head">
        <div class="r-brand">
            <strong>Resto Kay-Y</strong>
            <small>Cuisine haïtienne traditionnelle</small>
        </div>
        <div class="r-num">
            <div class="n">{{ $numero }}</div>
            <div class="d num">{{ $paiement->created_at->format('d/m/Y · H:i') }}</div>
        </div>
    </header>

    <div class="r-paid"><i data-lucide="check-circle-2"></i> Payé · {{ $modeLabel }}</div>

    <div class="r-meta">
        <div><span>Table</span><strong>{{ $table }}</strong></div>
        <div><span>Commande</span><strong class="num">#{{ $paiement->commande_id }}</strong></div>
        <div><span>Mode</span><strong>{{ $modeLabel }}</strong></div>
        <div><span>Caissier</span><strong>{{ $paiement->caissier ?? optional($paiement->user)->name ?? '—' }}</strong></div>
    </div>

    <table class="table r-lines">
        <thead>
            <tr><th>Article</th><th class="right">Qté</th><th class="right col-pu">Prix unit.</th><th class="right">Montant</th></tr>
        </thead>
        <tbody>
            @forelse($lignes as $l)
                <tr>
                    <td><strong>{{ $l->nom }}</strong></td>
                    <td class="right num">{{ $l->qte }}</td>
                    <td class="right num col-pu muted">{{ $fmt($l->pu) }}</td>
                    <td class="right num">{{ $fmt($l->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Détail des articles indisponible.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="r-totals num">
        @if($lignes->isNotEmpty())
            <div class="row"><span>Sous-total</span><span>{{ $fmt($sousTotal) }}</span></div>
            @if($ecart > 0.009)
                <div class="row"><span>Service{{ $taux > 0 ? ' ('.$taux.' %)' : '' }}</span><span>{{ $fmt($ecart) }}</span></div>
            @elseif($ecart < -0.009)
                <div class="row"><span>Remise</span><span>− {{ $fmt(abs($ecart)) }}</span></div>
            @endif
        @endif
        <div class="row grand"><span>Total payé</span><span>{{ $fmt($montant) }} HTG</span></div>
    </div>

    <footer class="r-foot">Merci de votre visite · À bientôt chez Resto Kay-Y</footer>
</article>

@endsection

@push('scripts')
<script>
function printAs(format) {
    document.body.classList.toggle('ticket', format === 'ticket');
    window.print();
}
window.addEventListener('afterprint', () => document.body.classList.remove('ticket'));
</script>
@endpush