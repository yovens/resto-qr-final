@extends('admin.layouts.layout')

@section('title', 'Mon profil')

@php
    $roles = ['admin' => 'Administrateur', 'caissier' => 'Caissier', 'cuisinier' => 'Cuisinier', 'serveur' => 'Serveur'];
    $roleLabel = $roles[$user->role] ?? ucfirst((string) $user->role);
    $pwErrors = $errors->has('current_password') || $errors->has('password');
@endphp

@push('styles')
<style>
    .me-card { padding: 24px 20px; text-align: center; }
    .me-avatar {
        width: 72px; height: 72px; border-radius: 50%; margin: 0 auto 12px;
        display: grid; place-items: center; background: var(--brand-50); color: var(--brand-600);
        font-weight: 800; font-size: 26px;
    }
    .me-card h2 { margin: 0; font-size: 17px; font-weight: 800; }
    .me-card .status { margin-top: 8px; }
    .me-card .dl { text-align: left; margin-top: 18px; border-top: 1px solid var(--border); }
    .me-card .dl > div { grid-template-columns: 1fr auto; padding: 11px 0; }
    .pw-wrap { position: relative; }
    .pw-wrap .input { padding-right: 44px; }
    .pw-wrap button { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 32px; height: 32px; }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Mon profil</h1>
        <p>Vos informations personnelles et votre mot de passe.</p>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="errors">Certains champs sont à corriger avant d'enregistrer.</div>
@endif

<form method="POST" action="{{ url('/admin/profile') }}" id="profileForm">
    @csrf
    @method('PUT')

    <div class="form-layout">
        <div class="card">
            <div class="form-section">
                <h3>Informations personnelles</h3>
                <p class="hint">Votre e-mail sert à vous connecter.</p>

                <div class="field">
                    <label for="name">Nom complet</label>
                    <input class="input @error('name') is-invalid @enderror" id="name" name="name" type="text"
                           value="{{ old('name', $user->name) }}" autocomplete="name" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="email">E-mail</label>
                        <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                               value="{{ old('email', $user->email) }}" autocomplete="email" required>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="telephone">Téléphone <span class="opt">(facultatif)</span></label>
                        <input class="input num @error('telephone') is-invalid @enderror" id="telephone" name="telephone" type="tel"
                               value="{{ old('telephone', $user->telephone) }}" placeholder="+509 3X XX XXXX" autocomplete="tel">
                        @error('telephone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Mot de passe</h3>
                <p class="hint">Laissez ces champs vides pour garder votre mot de passe actuel.</p>

                <div class="field">
                    <label for="current_password">Mot de passe actuel</label>
                    <div class="pw-wrap">
                        <input class="input @error('current_password') is-invalid @enderror" id="current_password" name="current_password"
                               type="password" autocomplete="current-password">
                        <button type="button" class="icon-action" data-toggle="current_password" title="Afficher"><i data-lucide="eye"></i></button>
                    </div>
                    @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="pw-wrap">
                            <input class="input @error('password') is-invalid @enderror" id="password" name="password"
                                   type="password" autocomplete="new-password" placeholder="6 caractères minimum">
                            <button type="button" class="icon-action" data-toggle="password" title="Afficher"><i data-lucide="eye"></i></button>
                        </div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirmer</label>
                        <div class="pw-wrap">
                            <input class="input" id="password_confirmation" name="password_confirmation"
                                   type="password" autocomplete="new-password" placeholder="Répétez le mot de passe">
                            <button type="button" class="icon-action" data-toggle="password_confirmation" title="Afficher"><i data-lucide="eye"></i></button>
                        </div>
                        <div class="field-error" id="pwMismatch" hidden>Les deux mots de passe ne correspondent pas.</div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> Enregistrer</button>
            </div>
        </div>

        <aside class="card me-card">
            <div class="me-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
            <h2>{{ $user->name }}</h2>
            <span class="status {{ $user->role === 'admin' ? 'served' : 'new' }}">{{ $roleLabel }}</span>

            <dl class="dl">
                <div>
                    <dt>E-mail</dt>
                    <dd style="font-size:13px">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt>Compte créé</dt>
                    <dd class="num" style="font-size:13px">{{ $user->created_at?->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </aside>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    // Afficher / masquer les mots de passe
    document.querySelectorAll('[data-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.toggle);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.title = show ? 'Masquer' : 'Afficher';
            btn.innerHTML = '<i data-lucide="' + (show ? 'eye-off' : 'eye') + '"></i>';
            if (window.lucide) lucide.createIcons();
        });
    });

    // Vérifications avant envoi
    const current = document.getElementById('current_password');
    const pw      = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const msg     = document.getElementById('pwMismatch');

    function check() {
        const changing = pw.value.length > 0;
        current.required = changing;
        confirm.required = changing;

        const mismatch = changing && confirm.value.length > 0 && pw.value !== confirm.value;
        msg.hidden = !mismatch;
        confirm.classList.toggle('is-invalid', mismatch);
        confirm.setCustomValidity(mismatch ? 'Les deux mots de passe ne correspondent pas.' : '');
    }
    [pw, confirm].forEach(i => i.addEventListener('input', check));

    @if($pwErrors)
        current.focus();
    @endif
})();
</script>
@endpush