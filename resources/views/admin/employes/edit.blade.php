@extends('admin.layouts.layout')

@section('title', 'Modifier · '.$employe->prenom.' '.$employe->nom)

@section('content')

<a href="/admin/employes/{{ $employe->id }}" class="back-link"><i data-lucide="arrow-left"></i> {{ $employe->prenom }} {{ $employe->nom }}</a>
<div class="page-head">
    <div>
        <h1>Modifier l'employé</h1>
        <p>{{ $employe->prenom }} {{ $employe->nom }}</p>
    </div>
</div>

@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

<form method="POST" action="{{ url('/admin/employes/'.$employe->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-layout">
        <div class="card">
            <div class="form-section">
                <h3>Identité</h3>
                <p class="hint">Nom tel qu'il apparaît sur la fiche de paie.</p>

                <div class="field-row">
                    <div class="field">
                        <label for="prenom">Prénom</label>
                        <input class="input @error('prenom') is-invalid @enderror" id="prenom" name="prenom" type="text"
                               value="{{ old('prenom', $employe->prenom ?? '') }}" required autofocus>
                        @error('prenom')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="nom">Nom</label>
                        <input class="input @error('nom') is-invalid @enderror" id="nom" name="nom" type="text"
                               value="{{ old('nom', $employe->nom ?? '') }}" required>
                        @error('nom')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="telephone">Téléphone</label>
                        <input class="input num @error('telephone') is-invalid @enderror" id="telephone" name="telephone" type="tel"
                               value="{{ old('telephone', $employe->telephone ?? '') }}" placeholder="+509 3X XX XXXX" required>
                        @error('telephone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="email">E-mail</label>
                        <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                               value="{{ old('email', $employe->email ?? '') }}" required>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Poste</h3>
                <p class="hint">Fonction et rémunération mensuelle.</p>

                <div class="field-row">
                    <div class="field">
                        <label for="role">Fonction</label>
                        @php $role = old('role', $employe->role ?? ''); @endphp
                        <select class="select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled @selected($role === '')>Choisir…</option>
                            <option value="caissiere" @selected($role === 'caissiere')>Caissière</option>
                            <option value="serveur"   @selected($role === 'serveur')>Serveur</option>
                            <option value="serveuse"  @selected($role === 'serveuse')>Serveuse</option>
                            <option value="cuisine"   @selected(in_array($role, ['cuisine', 'cuisinier']))>Cuisinier</option>
                            <option value="autre"     @selected($role === 'autre')>Autre</option>
                        </select>
                        @error('role')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="salaire">Salaire mensuel</label>
                        <div class="input-affix">
                            <input class="input num @error('salaire') is-invalid @enderror" id="salaire" name="salaire" type="number"
                                   step="0.01" min="0" inputmode="decimal" value="{{ old('salaire', $employe->salaire ?? '') }}" placeholder="0" required>
                            <span>HTG</span>
                        </div>
                        @error('salaire')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ url('/admin/employes/'.$employe->id) }}" class="btn">Annuler</a>
                <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> Enregistrer</button>
            </div>
        </div>

        <div class="card form-section">
            <h3>Photo</h3>
            <p class="hint">Facultatif. Une photo de face, format carré.</p>

            <label class="dropzone {{ $employe->photo ? 'has-image' : '' }}" id="dropzone" style="aspect-ratio: 1 / 1; max-width: 240px; margin: 0 auto; border-radius: 50%">
                <input type="file" name="photo" accept="image/*" id="photoInput">
                <img id="photoPreview" src="{{ $employe->photo ? asset($employe->photo) : '' }}" alt="" @unless($employe->photo) hidden @endunless>
                <span class="dropzone-empty">
                    <i data-lucide="camera"></i>
                    Ajouter une photo
                </span>
            </label>
            @error('photo')<div class="field-error" style="text-align:center">{{ $message }}</div>@enderror
            @if($employe->photo)<p class="hint" style="margin:10px 0 0;text-align:center">Choisissez une nouvelle photo pour remplacer l'actuelle.</p>@endif
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    const zone = document.getElementById('dropzone');
    const input = document.getElementById('photoInput');
    const preview = document.getElementById('photoPreview');
    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;
        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
        zone.classList.add('has-image');
    });
})();
</script>
@endpush