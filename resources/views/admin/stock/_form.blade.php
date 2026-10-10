{{--
    Formulaire partagé « Nouvel article » / « Modifier l'article ».
    Variables : $product (null en création), $action, $method, $submitLabel
--}}
@php $product = $product ?? null; @endphp

@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

<form method="POST" action="{{ $action }}" class="card" style="max-width: 680px">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="form-section">
        <h3>Article</h3>
        <p class="hint">Ingrédient ou produit dont vous suivez la quantité.</p>

        <div class="field-row">
            <div class="field">
                <label for="nom">Nom</label>
                <input class="input @error('nom') is-invalid @enderror" id="nom" name="nom" type="text"
                       value="{{ old('nom', $product->nom ?? '') }}" placeholder="Ex. Riz, Poulet, Huile" required autofocus>
                @error('nom')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="unite">Unité</label>
                <input class="input @error('unite') is-invalid @enderror" id="unite" name="unite" type="text" list="unites"
                       value="{{ old('unite', $product->unite ?? '') }}" placeholder="kg, litre, unité…" required>
                <datalist id="unites">
                    <option value="kg"><option value="g"><option value="litre"><option value="unité"><option value="sac"><option value="marmite"><option value="caisse">
                </datalist>
                @error('unite')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Quantités</h3>
        <p class="hint">
            @if($product)
                La quantité en stock se modifie avec une <a href="/admin/stock-mouvement?product={{ $product->id }}" class="link">entrée ou sortie</a>, pour garder l'historique.
            @else
                Une alerte apparaît quand le stock descend au seuil ou en dessous.
            @endif
        </p>

        <div class="field-row">
            @unless($product)
                <div class="field">
                    <label for="quantite_actuelle">Quantité initiale</label>
                    <div class="input-affix">
                        <input class="input num @error('quantite_actuelle') is-invalid @enderror" id="quantite_actuelle" name="quantite_actuelle"
                               type="number" step="0.01" min="0" inputmode="decimal" value="{{ old('quantite_actuelle', 0) }}" required>
                        <span data-unit></span>
                    </div>
                    @error('quantite_actuelle')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            @else
                <div class="field">
                    <label>En stock actuellement</label>
                    <div class="input-affix">
                        <input class="input num" type="text" value="{{ rtrim(rtrim(number_format($product->quantite_actuelle, 2, ',', ' '), '0'), ',') }}" disabled>
                        <span data-unit></span>
                    </div>
                </div>
            @endunless

            <div class="field">
                <label for="seuil_alerte">Seuil d'alerte</label>
                <div class="input-affix">
                    <input class="input num @error('seuil_alerte') is-invalid @enderror" id="seuil_alerte" name="seuil_alerte"
                           type="number" step="0.01" min="0" inputmode="decimal"
                           value="{{ old('seuil_alerte', $product->seuil_alerte ?? 5) }}" required>
                    <span data-unit></span>
                </div>
                @error('seuil_alerte')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="form-footer">
        <a href="/admin/stock" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> {{ $submitLabel }}</button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    // Affiche l'unité saisie à côté des quantités
    const unit = document.getElementById('unite');
    const sync = () => document.querySelectorAll('[data-unit]').forEach(s => s.textContent = unit.value.trim().slice(0, 8));
    unit.addEventListener('input', sync);
    sync();
})();
</script>
@endpush