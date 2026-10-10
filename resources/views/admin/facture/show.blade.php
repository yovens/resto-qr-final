@extends('admin.layouts.layout')

@section('title', 'Facture #' . $commande->id)

@php
    $fmt = fn ($n) => number_format((float) $n, 2, ',', ' ');

    $statusMap = [
        'nouvelle'       => ['new',    'Nouvelle'],
        'acceptee'       => ['prep',   'Acceptée'],
        'en_preparation' => ['prep',   'En préparation'],
        'prete'          => ['ready',  'Prête'],
        'servie'         => ['served', 'Servie'],
        'payee'          => ['ready',  'Payée'],
    ];
    [$stCls, $stLabel] = $statusMap[$commande->statut] ?? ['served', ucfirst(str_replace('_', ' ', (string) $commande->statut))];

    $lignes = $commande->items->map(function ($item) {
        $pu = (float) ($item->prix_unitaire ?? $item->plat->prix ?? 0);
        return (object) [
            'nom'   => $item->plat->nom ?? 'Plat supprimé',
            'note'  => $item->commentaire ?? null,
            'qte'   => (int) $item->quantite,
            'pu'    => $pu,
            'total' => $pu * (int) $item->quantite,
        ];
    });

    $sousTotal = $lignes->sum('total');
    $total     = (float) $commande->total;
    $remise    = (float) ($commande->remise ?? 0);

    // Différence entre le total enregistré et la somme des plats (service, remise…)
    $ecart     = round($total - $sousTotal, 2);
    $tauxEcart = $sousTotal > 0 ? round($ecart / $sousTotal * 100) : 0;

    $numero = str_pad($commande->id, 5, '0', STR_PAD_LEFT);
@endphp

@push('styles')
<style>
    .invoice { max-width: 760px; }
    .inv-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; padding: 24px; border-bottom: 1px solid var(--border); }
    .inv-brand { display: flex; align-items: center; gap: 12px; }
    .inv-brand .brand-mark { width: 40px; height: 40px; border-radius: 10px; background: var(--brand); color: #fff; display: grid; place-items: center; font-weight: 800; }
    .inv-brand strong { display: block; font-size: 16px; }
    .inv-brand small { color: var(--text-3); }
    .inv-num { text-align: right; }
    .inv-num .label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text-3); }
    .inv-num .n { font-family: var(--mono, monospace); font-size: 20px; font-weight: 700; }
    .inv-num .d { color: var(--text-2); font-size: 13px; }

    .inv-meta { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); border-bottom: 1px solid var(--border); }
    .inv-meta > div { padding: 14px 24px; border-right: 1px solid var(--border); }
    .inv-meta > div:last-child { border-right: 0; }
    .inv-meta span { display: block; font-size: 11.5px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
    .inv-meta strong { font-size: 14px; }

    .inv-lines th:first-child, .inv-lines td:first-child { padding-left: 24px; }
    .inv-lines th:last-child, .inv-lines td:last-child { padding-right: 24px; }
    .inv-lines .note { display: block; font-size: 12.5px; color: var(--text-3); font-style: italic; margin-top: 2px; }

    .inv-totals { margin-left: auto; width: 100%; max-width: 320px; padding: 14px 24px 22px; }
    .inv-totals .row { display: flex; justify-content: space-between; padding: 5px 0; color: var(--text-2); }
    .inv-totals .row.grand { border-top: 1px solid var(--border-strong); margin-top: 8px; padding-top: 12px; color: var(--text); font-size: 18px; font-weight: 800; }
    .inv-totals .row.discount { color: var(--st-ready); }

    .inv-foot { padding: 14px 24px; border-top: 1px dashed var(--border-strong); text-align: center; color: var(--text-3); font-size: 13px; }
    .inv-note { margin: 0 24px 16px; padding: 10px 12px; border-radius: var(--radius-sm); background: #fff6dc; color: #6b4a00; font-size: 13px; }

    @media (max-width: 640px) {
        .inv-head { flex-direction: column; }
        .inv-num { text-align: left; }
        .inv-meta { grid-template-columns: 1fr 1fr; }
        .inv-meta > div:nth-child(2) { border-right: 0; }
        .inv-meta > div:nth-child(-n+2) { border-bottom: 1px solid var(--border); }
    }

    /* ---------- Impression A4 ---------- */
    @media print {
        @page { margin: 14mm; }
        .sidebar, .topbar, .page-head, .back-link, .no-print { display: none !important; }
        .main { margin: 0 !important; }
        .content { padding: 0 !important; max-width: none; }
        body { background: #fff; }
        .invoice { max-width: none; border: 0; }
        .status { border: 1px solid currentColor; }
    }

    /* ---------- Impression ticket 80 mm ---------- */
    @media print {
        body.ticket { font-size: 12px; }
        body.ticket .invoice { width: 72mm; margin: 0 auto; }
        body.ticket .inv-head { flex-direction: column; align-items: center; text-align: center; padding: 6px 0 10px; gap: 6px; }
        body.ticket .inv-brand { flex-direction: column; gap: 4px; }
        body.ticket .inv-brand .brand-mark { display: none; }
        body.ticket .inv-num { text-align: center; }
        body.ticket .inv-meta { grid-template-columns: 1fr 1fr; }
        body.ticket .inv-meta > div { padding: 4px 0; border: 0 !important; }
        body.ticket .inv-meta > div:nth-child(4) { display: none; }
        body.ticket .inv-lines th, body.ticket .inv-lines td { padding: 3px 0 !important; font-size: 11.5px; white-space: normal; }
        body.ticket .inv-lines .col-pu { display: none; }
        body.ticket .inv-totals { max-width: none; padding: 6px 0 10px; }
        body.ticket .inv-totals .row.grand { font-size: 15px; }
        body.ticket .inv-note { margin: 0 0 8px; }
        body.ticket .inv-foot { padding: 8px 0 0; }
        body.ticket .status { background: none !important; padding: 0; border: 0; }
        @page { margin: 3mm; }
    }
</style>
@endpush

@section('content')

<a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/admin/ventes') }}" class="back-link no-print"><i data-lucide="arrow-left"></i> Retour</a>

<div class="page-head">
    <div>
        <h1>Facture n° {{ $numero }}</h1>
        <p>Table {{ $commande->table->numero ?? '—' }} · {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="page-actions no-print">
        <button type="button" class="btn" onclick="printAs('ticket')"><i data-lucide="receipt"></i> Ticket 80 mm</button>
        <button type="button" class="btn btn-primary" onclick="printAs('a4')"><i data-lucide="printer"></i> Imprimer A4</button>
    </div>
</div>

<article class="card invoice">
    <header class="inv-head">
        <div class="inv-brand">
            <span class="brand-mark">KY</span>
            <div>
                <strong>Resto Kay-Y</strong>
                <small>{{ config('app.restaurant_adresse', '') }}</small>
            </div>
        </div>
        <div class="inv-num">
            <div class="label">Facture</div>
            <div class="n">#{{ $numero }}</div>
            <div class="d num">{{ $commande->created_at->format('d/m/Y · H:i') }}</div>
        </div>
    </header>

    <div class="inv-meta">
        <div><span>Table</span><strong>{{ $commande->table->numero ?? '—' }}</strong></div>
        <div><span>Client</span><strong>{{ $commande->client ?: 'Sur place' }}</strong></div>
        <div><span>Statut</span><span class="status {{ $stCls }}" style="display:inline-flex;margin:0;text-transform:none;letter-spacing:0">{{ $stLabel }}</span></div>
        <div><span>Servi par</span><strong>{{ $commande->user->name ?? '—' }}</strong></div>
    </div>

    <table class="table inv-lines">
        <thead>
            <tr>
                <th>Article</th>
                <th class="right">Qté</th>
                <th class="right col-pu">Prix unit.</th>
                <th class="right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lignes as $l)
                <tr>
                    <td>
                        <strong>{{ $l->nom }}</strong>
                        @if($l->note)<span class="note">{{ $l->note }}</span>@endif
                    </td>
                    <td class="right num">{{ $l->qte }}</td>
                    <td class="right num col-pu muted">{{ $fmt($l->pu) }}</td>
                    <td class="right num"><strong>{{ $fmt($l->total) }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Aucun article sur cette commande.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="inv-totals num">
        <div class="row"><span>Sous-total</span><span>{{ $fmt($sousTotal) }} HTG</span></div>

        @if($ecart > 0.009)
            <div class="row"><span>Service{{ $tauxEcart > 0 ? ' ('.$tauxEcart.' %)' : '' }}</span><span>{{ $fmt($ecart) }} HTG</span></div>
        @elseif($ecart < -0.009)
            <div class="row discount"><span>Remise{{ $remise > 0 ? ' ('.rtrim(rtrim(number_format($remise, 2, ',', ''), '0'), ',').' %)' : '' }}</span><span>− {{ $fmt(abs($ecart)) }} HTG</span></div>
        @endif

        <div class="row grand"><span>Total</span><span>{{ $fmt($total) }} HTG</span></div>
    </div>

    @if(!empty($commande->note))
        <div class="inv-note"><strong>Note :</strong> {{ $commande->note }}</div>
    @endif

    <footer class="inv-foot">Merci de votre visite · À bientôt chez Resto Kay-Y</footer>
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