@extends('caisse.layouts.app')

@section('title', 'Encaisser · commande #' . $commande->id)

@php
    $fmt = fn ($n) => number_format((float) $n, 2, ',', ' ');
    $total = (float) $commande->total;
    $items = $commande->items ?? collect();
    $modes = [
        ['Espèces',  'banknote',     'var(--st-ready)'],
        ['Carte',    'credit-card',  'var(--st-new)'],
        ['MonCash',  'smartphone',   'var(--danger)'],
        ['NatCash',  'wallet',       'var(--st-prep)'],
        ['Virement', 'landmark',     'var(--text-2)'],
    ];
    $selMode = old('mode_paiement', 'Espèces');
@endphp

@push('styles')
<style>
    .pay-layout { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 16px; align-items: start; }
    .order-lines { list-style: none; margin: 0; padding: 0; }
    .order-lines li { display: grid; grid-template-columns: 36px 1fr auto; gap: 10px; padding: 10px 20px; border-bottom: 1px solid var(--border); }
    .order-lines li:last-child { border-bottom: 0; }
    .order-lines .q { font-family: var(--mono, monospace); font-weight: 700; color: var(--text-2); }
    .order-total { display: flex; justify-content: space-between; align-items: baseline; padding: 16px 20px; border-top: 1px solid var(--border-strong); }
    .order-total strong { font-size: 24px; font-weight: 800; }

    .modes { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
    .mode { position: relative; cursor: pointer; }
    .mode input { position: absolute; opacity: 0; }
    .mode span {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        padding: 14px 6px; border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        font-weight: 700; font-size: 13px; text-align: center;
    }
    .mode span svg.lucide { width: 22px; height: 22px; color: var(--c); }
    .mode input:checked + span { border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-50); }
    .mode input:focus-visible + span { outline: 2px solid var(--brand); outline-offset: 2px; }

    .cash-box { margin-top: 16px; padding: 16px; border-radius: var(--radius-sm); background: var(--bg); }
    .quick { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .quick button { height: 32px; padding: 0 10px; border-radius: 6px; border: 1px solid var(--border-strong); background: var(--surface); font-weight: 700; font-size: 12.5px; cursor: pointer; }
    .quick button:hover { border-color: var(--brand); }
    .change { display: flex; justify-content: space-between; align-items: baseline; margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--border-strong); }
    .change strong { font-size: 22px; font-weight: 800; color: var(--st-ready); }
    .change.short strong { color: var(--danger); }

    .pay-btn { width: 100%; height: 52px; justify-content: center; font-size: 15px; margin-top: 16px; }

    @media (max-width: 1000px) { .pay-layout { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')

<a href="{{ url('/caisse/dashboard') }}" class="back-link"><i data-lucide="arrow-left"></i> Tableau de bord</a>
<div class="page-head">
    <div>
        <h1>Table {{ $commande->table->numero ?? $commande->restaurant_table_id }}</h1>
        <p>Commande #{{ $commande->id }} · passée à {{ $commande->created_at->format('H:i') }}</p>
    </div>
</div>

@if($errors->any())
    <div class="errors">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
@endif

<div class="pay-layout">

    {{-- Détail de la commande --}}
    <article class="card">
        <div class="card-head" style="padding-bottom: 12px">
            <div>
                <h2>Détail</h2>
                <span class="sub">Vérifiez avec le client avant d'encaisser</span>
            </div>
        </div>
        @if($items->isEmpty())
            <div class="empty">Le détail des plats n'est pas disponible.</div>
        @else
            <ul class="order-lines" style="border-top: 1px solid var(--border)">
                @foreach($items as $item)
                    @php $pu = (float) ($item->prix ?? $item->prix_unitaire ?? $item->plat->prix ?? 0); @endphp
                    <li>
                        <span class="q">{{ $item->quantite }}×</span>
                        <span>{{ $item->plat->nom ?? 'Plat supprimé' }}</span>
                        <span class="num">{{ $fmt($pu * $item->quantite) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        <div class="order-total">
            <span>Total à encaisser</span>
            <strong class="num">{{ $fmt($total) }} HTG</strong>
        </div>
    </article>

    {{-- Paiement --}}
    <form method="POST" action="{{ route('caisse.paiement') }}" class="card form-section" id="payForm">
        @csrf
        <input type="hidden" name="commande_id" value="{{ $commande->id }}">
        <input type="hidden" name="montant" value="{{ $commande->total }}">

        <h3>Mode de paiement</h3>
        <p class="hint">Choisissez comment le client règle.</p>

        <div class="modes">
            @foreach($modes as [$val, $icon, $color])
                <label class="mode" style="--c: {{ $color }}">
                    <input type="radio" name="mode_paiement" value="{{ $val }}" @checked($selMode === $val) required>
                    <span><i data-lucide="{{ $icon }}"></i>{{ $val }}</span>
                </label>
            @endforeach
        </div>
        @error('mode_paiement')<div class="field-error">{{ $message }}</div>@enderror

        {{-- Calcul de la monnaie (espèces) : simple aide, non enregistrée --}}
        <div class="cash-box" id="cashBox">
            <label for="recu" style="font-weight:700;font-size:13px">Montant reçu</label>
            <div class="input-affix" style="margin-top:6px">
                <input class="input num" id="recu" type="number" min="0" step="1" inputmode="decimal" placeholder="{{ (int) ceil($total) }}">
                <span>HTG</span>
            </div>
            <div class="quick" id="quick"></div>
            <div class="change" id="change">
                <span>Monnaie à rendre</span>
                <strong class="num" id="changeVal">0,00 HTG</strong>
            </div>
        </div>

        <button type="submit" class="btn btn-primary pay-btn" id="payBtn">
            <i data-lucide="check"></i> Encaisser {{ $fmt($total) }} HTG
        </button>
    </form>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const TOTAL  = {{ json_encode($total) }};
    const form   = document.getElementById('payForm');
    const box    = document.getElementById('cashBox');
    const recu   = document.getElementById('recu');
    const change = document.getElementById('change');
    const val    = document.getElementById('changeVal');
    const quick  = document.getElementById('quick');
    const money  = (n) => n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' HTG';

    // Montants ronds au-dessus du total (centaine, 500, millier, millier suivant)
    const suggestions = new Set([Math.ceil(TOTAL)]);
    [100, 500, 1000].forEach(b => suggestions.add(Math.ceil(TOTAL / b) * b));
    suggestions.add(Math.ceil(TOTAL / 1000) * 1000 + 1000);
    [...suggestions].filter(n => n >= TOTAL).sort((a, b) => a - b).slice(0, 4).forEach(n => {
        const b = document.createElement('button');
        b.type = 'button';
        b.textContent = n === Math.ceil(TOTAL) ? 'Montant exact' : n.toLocaleString('fr-FR');
        b.addEventListener('click', () => { recu.value = n; update(); });
        quick.append(b);
    });

    function update() {
        const mode = form.querySelector('input[name="mode_paiement"]:checked')?.value;
        box.hidden = mode !== 'Espèces';
        const r = parseFloat(recu.value);
        if (!Number.isFinite(r)) { val.textContent = money(0); change.classList.remove('short'); return; }
        const diff = r - TOTAL;
        change.classList.toggle('short', diff < 0);
        change.firstElementChild.textContent = diff < 0 ? 'Il manque' : 'Monnaie à rendre';
        val.textContent = money(Math.abs(diff));
    }

    form.addEventListener('change', update);
    recu.addEventListener('input', update);
    update();

    // Empêche le double encaissement (double clic)
    form.addEventListener('submit', (e) => {
        const mode = form.querySelector('input[name="mode_paiement"]:checked')?.value;
        const r = parseFloat(recu.value);
        if (mode === 'Espèces' && Number.isFinite(r) && r < TOTAL) {
            if (!confirm('Le montant reçu est inférieur au total. Encaisser quand même ?')) { e.preventDefault(); return; }
        }
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.textContent = 'Enregistrement…';
    });
})();
</script>
@endpush