<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resto Kay-Y - Espace Caisse (POS)</title>
    <!-- Tailwind CSS & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #374151;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- =========================
            TOPBAR CAISSE
    ========================== -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            
            <!-- Logo & Tit -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-white text-xl shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-xl text-gray-800 tracking-tight">RESTO KAY-Y</h1>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Terminal Point de Vente</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="/caisse/dashboard" class="flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-amber-600 transition">
                    <i class="fa-solid fa-house text-amber-500"></i> Accueil Caisse
                </a>
                <a href="/caisse/dashboard" class="flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-amber-600 transition">
                    <i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Historique
                </a>
            </nav>

            <!-- User Info & Logout -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-sm font-bold text-gray-800">{{ auth()->user()->name ?? 'Caissier' }}</span>
                    <span class="text-xs font-semibold text-emerald-600">En service</span>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold text-sm transition">
                        <i class="fa-solid fa-power-off"></i> Déconnexion
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- =========================
            MAIN CONTENT
    ========================== -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- =========================
            FOOTER CAISSE
    ========================== -->
    <footer class="bg-white border-t border-gray-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center text-xs text-gray-400 font-medium">
            &copy; {{ date('Y') }} Resto Kay-Y. Tous droits réservés. Module Caissier Sécurisé.
        </div>
    </footer>

</body>
</html>