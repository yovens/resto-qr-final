@extends('admin.layouts.layout')

@section('title', 'Nouvel utilisateur')

@push('styles')
<style>
    .segmented.roles { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    @media (max-width: 560px) { .segmented.roles { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<a href="/admin/users" class="back-link"><i data-lucide="arrow-left"></i> Utilisateurs</a>
<div class="page-head">
    <div>
        <h1>Nouvel utilisateur</h1>
        <p>Créez un accès au système pour un membre de l'équipe.</p>
    </div>
</div>

@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

@php $role = old('role', 'serveur'); @endphp

<form method="POST" action="{{ url('/admin/users') }}" class="card" style="max-width: 760px" autocomplete="off">
    @csrf
    <div class="form-section">
        <h3>Compte</h3>
        <p class="hint">L'e-mail sert d'identifiant pour se connecter.</p>

        <div class="field">
            <label for="name">Nom complet</label>
            <input class="input @error('name') is-invalid @enderror" id="name" name="name" type="text"
                   value="{{ old('name', '') }}" required autofocus>
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field-row">
            <div class="field">
                <label for="email">E-mail</label>
                <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                       value="{{ old('email', '') }}" placeholder="prenom@restaurant.com" autocomplete="off" required>
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="telephone">Téléphone <span class="opt">(facultatif)</span></label>
                <input class="input num @error('telephone') is-invalid @enderror" id="telephone" name="telephone" type="tel"
                       value="{{ old('telephone', '') }}" placeholder="+509 3X XX XXXX">
                @error('telephone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="field">
            <label for="password">Mot de passe</label>
            <div class="input-affix">
                <input class="input @error('password') is-invalid @enderror" id="password" name="password" type="password"
                       autocomplete="new-password" placeholder="Choisissez un mot de passe" required>
                <span style="pointer-events:auto"><button type="button" class="icon-action" id="pwToggle" title="Afficher" style="width:28px;height:28px"><i data-lucide="eye"></i></button></span>
            </div>
            @error('password')<div class="field-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-section">
        <h3>Rôle</h3>
        <p class="hint">Détermine ce que cette personne peut voir et modifier.</p>

        <div class="segmented roles">
            <label>
                <input type="radio" name="role" value="admin" @checked($role === 'admin')>
                <span class="dot" style="background: var(--text)"></span>
                <span>Administrateur<small>Accès complet, y compris finances et comptes</small></span>
            </label>
            <label>
                <input type="radio" name="role" value="caissier" @checked($role === 'caissier')>
                <span class="dot" style="background: var(--st-ready)"></span>
                <span>Caissier<small>Encaisse et consulte les ventes</small></span>
            </label>
            <label>
                <input type="radio" name="role" value="cuisinier" @checked($role === 'cuisinier')>
                <span class="dot" style="background: var(--st-prep)"></span>
                <span>Cuisinier<small>Écran cuisine et suivi des commandes</small></span>
            </label>
            <label>
                <input type="radio" name="role" value="serveur" @checked($role === 'serveur')>
                <span class="dot" style="background: var(--st-new)"></span>
                <span>Serveur<small>Suit et sert les commandes des tables</small></span>
            </label>
        </div>
        @error('role')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-footer">
        <a href="/admin/users" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> Créer le compte</button>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    const pw = document.getElementById('password');
    const btn = document.getElementById('pwToggle');
    btn.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        btn.title = show ? 'Masquer' : 'Afficher';
        btn.innerHTML = '<i data-lucide="' + (show ? 'eye-off' : 'eye') + '"></i>';
        if (window.lucide) lucide.createIcons();
    });
})();
</script>
@endpush