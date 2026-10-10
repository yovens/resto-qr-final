<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion · Resto Kay-Y</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <style>
        body { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }

        .login-side {
            background: var(--side-bg); color: var(--side-text);
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 40px 48px;
        }
        .login-side .brand { height: auto; padding: 0; border: 0; }
        .login-side .brand-mark { width: 36px; height: 36px; font-size: 14px; }
        .login-pitch h1 { color: #fff; font-size: 30px; line-height: 1.2; letter-spacing: -.02em; margin: 0 0 14px; font-weight: 800; max-width: 420px; }
        .login-pitch p { margin: 0; max-width: 400px; font-size: 15px; line-height: 1.6; }
        .login-roles { list-style: none; margin: 28px 0 0; padding: 0; display: grid; gap: 12px; max-width: 400px; }
        .login-roles li { display: flex; gap: 12px; align-items: flex-start; font-size: 14px; }
        .login-roles svg.lucide { width: 18px; height: 18px; color: var(--brand); flex-shrink: 0; margin-top: 1px; }
        .login-roles strong { color: #fff; display: block; font-weight: 700; }
        .login-side footer { font-size: 12.5px; color: #5f727c; }

        .login-main { display: grid; place-items: center; padding: 40px 24px; background: var(--bg); }
        .login-card { width: 100%; max-width: 380px; }
        .login-card h2 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -.02em; }
        .login-card > p { margin: 6px 0 24px; color: var(--text-2); }
        .login-card .field { margin-bottom: 16px; }
        .login-card .input { height: 44px; }

        .pw-wrap { position: relative; }
        .pw-wrap .input { padding-right: 46px; }
        .pw-wrap button { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); }

        .remember { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--text-2); cursor: pointer; margin: 4px 0 20px; }
        .remember input { width: 16px; height: 16px; accent-color: var(--brand); margin: 0; }

        .login-btn { width: 100%; height: 46px; justify-content: center; font-size: 14.5px; }
        .login-error {
            display: flex; gap: 10px; align-items: flex-start;
            padding: 12px 14px; margin-bottom: 18px; border-radius: var(--radius-sm);
            background: #fdf1f1; border: 1px solid #f3c9c9; color: #9b2020; font-size: 13.5px; font-weight: 600;
        }
        .login-error svg.lucide { width: 18px; height: 18px; flex-shrink: 0; }
        .login-status { padding: 12px 14px; margin-bottom: 18px; border-radius: var(--radius-sm); background: var(--st-ready-bg); color: #126b33; font-size: 13.5px; font-weight: 600; }

        .mobile-brand { display: none; }

        @media (max-width: 860px) {
            body { grid-template-columns: 1fr; }
            .login-side { display: none; }
            .mobile-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; margin-bottom: 28px; }
            .mobile-brand .brand-mark { width: 32px; height: 32px; border-radius: 8px; background: var(--brand); color: #fff; display: grid; place-items: center; font-size: 13px; }
        }
    </style>
</head>
<body>

<aside class="login-side">
    <div class="brand">
        <span class="brand-mark">KY</span>
        <span>Resto Kay-Y<small>Espace équipe</small></span>
    </div>

    <div class="login-pitch">
        <h1>Le service, de la table à la caisse.</h1>
        <p>Un seul accès pour toute l'équipe. Chaque compte ouvre l'espace qui correspond à son rôle.</p>
        <ul class="login-roles">
            <li><i data-lucide="layout-dashboard"></i><span><strong>Administration</strong>Menu, stock, ventes et personnel.</span></li>
            <li><i data-lucide="chef-hat"></i><span><strong>Cuisine</strong>Commandes en temps réel, par ordre d'arrivée.</span></li>
            <li><i data-lucide="wallet"></i><span><strong>Caisse</strong>Encaissement et reçus.</span></li>
        </ul>
    </div>

    <footer>© {{ date('Y') }} Resto Kay-Y</footer>
</aside>

<main class="login-main">
    <div class="login-card">
        <div class="mobile-brand"><span class="brand-mark">KY</span> Resto Kay-Y</div>

        <h2>Connexion</h2>
        <p>Entrez vos identifiants pour continuer.</p>

        @if(session('status'))
            <div class="login-status">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="login-error" role="alert">
                <i data-lucide="alert-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
            @csrf

            <div class="field">
                <label for="email">E-mail</label>
                <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                       value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <div class="pw-wrap">
                    <input class="input @error('password') is-invalid @enderror" id="password" name="password" type="password"
                           autocomplete="current-password" required>
                    <button type="button" class="icon-action" id="pwToggle" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
            </div>

            <label class="remember">
                <input type="checkbox" name="remember" @checked(old('remember'))>
                Rester connecté sur cet appareil
            </label>

            <button type="submit" class="btn btn-primary login-btn" id="loginBtn">Se connecter</button>
        </form>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
(function () {
    window.lucide && lucide.createIcons();

    const pw = document.getElementById('password');
    const toggle = document.getElementById('pwToggle');
    toggle.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        toggle.title = show ? 'Masquer le mot de passe' : 'Afficher le mot de passe';
        toggle.setAttribute('aria-label', toggle.title);
        toggle.innerHTML = '<i data-lucide="' + (show ? 'eye-off' : 'eye') + '"></i>';
        window.lucide && lucide.createIcons();
        pw.focus();
    });

    // Après une erreur, on se replace directement sur le mot de passe
    @if($errors->any() && old('email'))
        pw.focus();
    @endif

    const form = document.getElementById('loginForm');
    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) { e.preventDefault(); form.reportValidity(); return; }
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.textContent = 'Connexion…';
    });
})();
</script>
</body>
</html>