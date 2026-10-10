{{--
    Formulaire partagé « Nouveau fournisseur » / « Modifier ».
    Variables : $supplier (null en création), $action, $method, $submitLabel
--}}
@php $supplier = $supplier ?? null; @endphp

@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

<form method="POST" action="{{ $action }}" class="card" style="max-width: 760px">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="form-section">
        <h3>Entreprise</h3>
        <p class="hint">Qui vous livre et ce qu'il fournit.</p>

        <div class="field">
            <label for="nom_entreprise">Nom de l'entreprise</label>
            <input class="input @error('nom_entreprise') is-invalid @enderror" id="nom_entreprise" name="nom_entreprise" type="text"
                   value="{{ old('nom_entreprise', $supplier->nom_entreprise ?? '') }}" placeholder="Ex. Boulangerie Saint-Marc" required autofocus>
            @error('nom_entreprise')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="produits_fournis">Produits fournis <span class="opt">(séparés par des virgules)</span></label>
            <input class="input @error('produits_fournis') is-invalid @enderror" id="produits_fournis" name="produits_fournis" type="text"
                   value="{{ old('produits_fournis', $supplier->produits_fournis ?? '') }}" placeholder="Ex. Viande, Poulet, Œufs">
            @error('produits_fournis')<div class="field-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-section">
        <h3>Contact</h3>
        <p class="hint">La personne à appeler pour passer commande.</p>

        <div class="field-row">
            <div class="field">
                <label for="nom_contact">Nom du contact</label>
                <input class="input @error('nom_contact') is-invalid @enderror" id="nom_contact" name="nom_contact" type="text"
                       value="{{ old('nom_contact', $supplier->nom_contact ?? '') }}" autocomplete="off" required>
                @error('nom_contact')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="telephone">Téléphone</label>
                <input class="input num @error('telephone') is-invalid @enderror" id="telephone" name="telephone" type="tel"
                       value="{{ old('telephone', $supplier->telephone ?? '') }}" placeholder="+509 3X XX XXXX" required>
                @error('telephone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="field">
            <label for="email">E-mail</label>
            <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                   value="{{ old('email', $supplier->email ?? '') }}" placeholder="contact@entreprise.com" required>
            @error('email')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="adresse">Adresse <span class="opt">(facultatif)</span></label>
            <textarea class="textarea @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="2"
                      placeholder="Rue, quartier, ville">{{ old('adresse', $supplier->adresse ?? '') }}</textarea>
            @error('adresse')<div class="field-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-footer">
        <a href="{{ $supplier ? '/admin/suppliers/'.$supplier->id : '/admin/suppliers' }}" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> {{ $submitLabel }}</button>
    </div>
</form>