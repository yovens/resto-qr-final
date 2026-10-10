@extends('admin.layouts.layout')

@section('title', 'Modifier · '.$user->name)

@push('styles')
<style>
    .segmented.roles { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    @media (max-width: 560px) { .segmented.roles { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<a href="/admin/users/{{ $user->id }}" class="back-link"><i data-lucide="arrow-left"></i> {{ $user->name }}</a>
<div class="page-head">
    <div>
        <h1>Modifier l'utilisateur</h1>
        <p>{{ $user->name }}</p>
    </div>
</div>

@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

@php $role = old('role', $user->role); @endphp

<form method="POST" action="{{ url('/admin/users/'.$user->id) }}" class="card" style="max-width: 760px" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="form-section">
        <h3>Compte</h3>
        <p class="hint">L'e-mail sert d'identifiant pour se connecter.</p>

        <div class="field">
            <label for="name">Nom complet</label>
            <input class="input @error('name') is-invalid @enderror" id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field-row">
            <div class="field">
                <label for="email">E-mail</label>
                <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}" placeholder="prenom@restaurant.com" autocomplete="off" required>
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="telephone">Téléphone <span class="opt">(facultatif)</span></label>
                <input class="input num @error('telephone') is-invalid @enderror" id="telephone" name="telephone" type="tel"
                       value="{{ old('telephone', $user->telephone) }}" placeholder="+509 3X XX XXXX">
                @error('telephone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="field">
            <label for="password">Nouveau mot de passe <span class="opt">(laisser vide pour ne pas changer)</span></label>
            <div class="input-affix">
                <input class="input @error('password') is-invalid @enderror" id="password" name="password" type="password"
                       autocomplete="new-password" placeholder="••••••••" >
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
        @if($user->id == auth()->id())
            <p class="hint" style="margin:12px 0 0;color:#a35f06">C'est votre compte : si vous quittez le rôle Administrateur, vous perdrez l'accès à cette page.</p>
        @endif
    </div>

    <div class="form-footer">
        <a href="{{ url('/admin/users/'.$user->id) }}" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> Enregistrer</button>
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