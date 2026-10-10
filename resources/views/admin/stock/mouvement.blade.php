@extends('admin.layouts.layout')

@section('title', 'Entrée / sortie de stock')

@php
    $selProduct = old('product_id', request('product'));
    $selType    = old('type', request('type', 'entrant'));
@endphp

@section('content')

<a href="/admin/stock" class="back-link"><i data-lucide="arrow-left"></i> Stock</a>
<div class="page-head">
    <div>
        <h1>Entrée / sortie de stock</h1>
        <p>Achat fournisseur, utilisation en cuisine, perte…</p>
    </div>
</div>

@if($errors->any())
    <div class="errors">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

@if($products->isEmpty())
    <div class="card empty">
        Aucun article en stock. <a href="/admin/stock/create" class="link">Créer un article</a> d'abord.
    </div>
@else
<form method="POST" action="/admin/stock-mouvement" class="card" style="max-width: 680px" id="mvtForm">
    @csrf

    <div class="form-section">
        <div class="field">
            <label for="product_id">Article</label>
            <select class="select" id="product_id" name="product_id" required>
                @foreach($products->sortBy('nom') as $prod)
                    <option value="{{ $prod->id }}"
                            data-qty="{{ (float) $prod->quantite_actuelle }}"
                            data-unit="{{ $prod->unite }}"
                            @selected($selProduct == $prod->id)>
                        {{ $prod->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Type</label>
            <div class="segmented">
                <label>
                    <input type="radio" name="type" value="entrant" @checked($selType === 'entrant')>
                    <span class="dot" style="background: var(--st-ready)"></span>
                    <span>Entrée<small>Achat, livraison</small></span>
                </label>
                <label>
                    <input type="radio" name="type" value="sortant" @checked($selType === 'sortant')>
                    <span class="dot" style="background: var(--danger)"></span>
                    <span>Sortie<small>Utilisation, perte</small></span>
                </label>
            </div>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="quantite">Quantité</label>
                <div class="input-affix">
                    <input class="input num" id="quantite" name="quantite" type="number" step="0.01" min="0.01"
                           inputmode="decimal" value="{{ old('quantite') }}" placeholder="0" required autofocus>
                    <span id="qtyUnit"></span>
                </div>
            </div>
            <div class="field">
                <label for="motif">Motif <span class="opt">(facultatif)</span></label>
                <input class="input" id="motif" name="motif" type="text" list="motifs" value="{{ old('motif') }}" placeholder="Ex. Achat fournisseur">
                <datalist id="motifs">
                    <option value="Achat fournisseur"><option value="Utilisation cuisine"><option value="Perte / périmé"><option value="Correction d'inventaire">
                </datalist>
            </div>
        </div>

        <div class="preview-line" id="preview">
            <span>En stock : <strong class="num" id="pvNow">—</strong></span>
            <span>Après : <strong class="num" id="pvAfter">—</strong></span>
        </div>
    </div>

    <div class="form-footer">
        <a href="/admin/stock" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> Enregistrer le mouvement</button>
    </div>
</form>
@endif

@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('mvtForm');
    if (!form) return;

    const product = document.getElementById('product_id');
    const qty     = document.getElementById('quantite');
    const unitEl  = document.getElementById('qtyUnit');
    const preview = document.getElementById('preview');
    const now     = document.getElementById('pvNow');
    const after   = document.getElementById('pvAfter');
    const fmt = (n) => n.toLocaleString('fr-FR', { maximumFractionDigits: 2 });

    function update() {
        const opt   = product.selectedOptions[0];
        const stock = parseFloat(opt.dataset.qty) || 0;
        const unit  = opt.dataset.unit || '';
        const q     = parseFloat(qty.value) || 0;
        const out   = form.querySelector('input[name="type"]:checked')?.value === 'sortant';
        const res   = out ? stock - q : stock + q;

        unitEl.textContent = unit.slice(0, 8);
        now.textContent    = fmt(stock) + ' ' + unit;
        after.textContent  = fmt(res) + ' ' + unit;

        const tooMuch = out && q > stock;
        preview.classList.toggle('bad', tooMuch);
        qty.setCustomValidity(tooMuch ? 'La sortie dépasse la quantité en stock (' + fmt(stock) + ' ' + unit + ').' : '');
    }

    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
})();
</script>
@endpush