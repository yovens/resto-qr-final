{{--
    Formulaire partagé entre « Nouveau plat » et « Modifier le plat ».
    Variables : $plat (null en création), $categories, $action, $method, $submitLabel
--}}
@php
    $plat      = $plat ?? null;
    $imageUrl  = $plat && $plat->image ? asset('images/'.$plat->image) : null;
    // Après une erreur de validation, une case décochée n'est pas renvoyée : on se base sur la présence d'autres anciennes valeurs
    $dispo     = session()->hasOldInput() ? (bool) old('disponible') : ($plat ? (bool) $plat->disponible : true);
@endphp

@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="form-layout">

        {{-- Colonne principale --}}
        <div class="card">
            <div class="form-section">
                <h3>Informations</h3>
                <p class="hint">Ce que le client voit sur le menu QR.</p>

                <div class="field">
                    <label for="nom">Nom du plat</label>
                    <input class="input @error('nom') is-invalid @enderror" id="nom" name="nom" type="text"
                           value="{{ old('nom', $plat->nom ?? '') }}" placeholder="Ex. Griot ak bannann peze" required autofocus>
                    @error('nom')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="description">Description <span class="opt">(facultatif)</span></label>
                    <textarea class="textarea @error('description') is-invalid @enderror" id="description" name="description"
                              rows="3" placeholder="Ingrédients, accompagnements, portion…">{{ old('description', $plat->description ?? '') }}</textarea>
                    @error('description')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="prix">Prix</label>
                        <div class="input-affix">
                            <input class="input num @error('prix') is-invalid @enderror" id="prix" name="prix" type="number"
                                   min="0" step="0.01" inputmode="decimal"
                                   value="{{ old('prix', $plat->prix ?? '') }}" placeholder="0" required>
                            <span>HTG</span>
                        </div>
                        @error('prix')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="category_id">Catégorie</label>
                        <select class="select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                            <option value="">Sans catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $plat->category_id ?? '') == $cat->id)>
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <label class="switch">
                    <input type="checkbox" name="disponible" value="1" @checked($dispo)>
                    <span class="track"></span>
                    <span>
                        <strong>Disponible à la commande</strong>
                        <small>Désactivez quand le plat est en rupture : il reste dans le menu mais ne peut plus être commandé.</small>
                    </span>
                </label>
            </div>

            <div class="form-footer">
                <a href="/admin/plats" class="btn">Annuler</a>
                <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> {{ $submitLabel }}</button>
            </div>
        </div>

        {{-- Colonne image --}}
        <div class="card form-section">
            <h3>Photo</h3>
            <p class="hint">JPG, PNG ou WEBP, format paysage de préférence. La photo est réduite automatiquement.</p>

            <label class="dropzone {{ $imageUrl ? 'has-image' : '' }}" id="dropzone">
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" id="imageInput">
                <img id="imagePreview" src="{{ $imageUrl ?? '' }}" alt="" @if(!$imageUrl) hidden @endif>
                <span class="dropzone-empty">
                    <i data-lucide="image-plus"></i>
                    Cliquez ou glissez une photo ici
                </span>
            </label>
            @error('image')<div class="field-error">{{ $message }}</div>@enderror

            @if($imageUrl)
                <p class="hint" style="margin:10px 0 0">Choisissez une nouvelle photo pour remplacer l'actuelle.</p>
            @endif
        </div>

    </div>
</form>

@push('scripts')
<script>
(function () {
    const zone    = document.getElementById('dropzone');
    const input   = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');

    const MAX = 1200;  // px, plus grand côté
    let erreur = null;

    function montrerErreur(msg) {
        if (!erreur) { erreur = document.createElement('div'); erreur.className = 'field-error'; zone.after(erreur); }
        erreur.textContent = msg || '';
        erreur.hidden = !msg;
    }

    // Réduit la photo dans le navigateur AVANT l'envoi :
    // envoi rapide sur le Wi-Fi et pas de blocage par la limite d'upload de PHP (2 Mo par défaut).
    async function reduire(file) {
        if (!window.createImageBitmap || !window.DataTransfer) return null;
        const bmp = await createImageBitmap(file, { imageOrientation: 'from-image' });
        const r = Math.min(1, MAX / Math.max(bmp.width, bmp.height));
        if (r === 1 && file.size < 900 * 1024) return null;            // déjà légère
        const c = document.createElement('canvas');
        c.width = Math.round(bmp.width * r); c.height = Math.round(bmp.height * r);
        const ctx = c.getContext('2d');
        ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
        ctx.drawImage(bmp, 0, 0, c.width, c.height);
        const blob = await new Promise(res => c.toBlob(res, 'image/jpeg', 0.82));
        if (!blob) return null;
        return new File([blob], (file.name.replace(/\.[^.]+$/, '') || 'photo') + '.jpg', { type: 'image/jpeg' });
    }

    input.addEventListener('change', async () => {
        const file = input.files && input.files[0];
        montrerErreur(null);
        if (!file) return;
        if (!/^image\/(jpeg|png|webp|gif)$/.test(file.type)) {
            input.value = '';
            montrerErreur('Format non accepté (' + (file.name.split('.').pop() || '?').toUpperCase() + '). Choisissez une photo JPG, PNG ou WEBP.');
            return;
        }
        try {
            const petite = await reduire(file);
            if (petite) {
                const dt = new DataTransfer(); dt.items.add(petite); input.files = dt.files;
            }
        } catch (e) { /* on envoie l'original, le serveur le réduira */ }
        preview.src = URL.createObjectURL(input.files[0]);
        preview.hidden = false;
        zone.classList.add('has-image');
    });
    ['dragenter', 'dragover'].forEach(e => zone.addEventListener(e, () => zone.classList.add('drag')));
    ['dragleave', 'drop'].forEach(e => zone.addEventListener(e, () => zone.classList.remove('drag')));
})();
</script>
@endpush