<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Caisse - Resto Kay-Y</title>
    <!-- FontAwesome CDN pou ikon yo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; background: #0f172a; color: #fff; height: 100vh; position: fixed; left: 0; top: 0; }
        .sidebar .logo { padding: 20px; font-size: 1.2rem; font-weight: bold; color: #f59e0b; display: flex; align-items: center; gap: 10px; }
        .sidebar .menu-title { padding: 15px 20px 5px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .sidebar a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #cbd5e1; text-decoration: none; font-size: 0.95rem; transition: all 0.2s; }
        .sidebar a:hover { background: #1e293b; color: #fff; }
        .sidebar .badge { background: #ef4444; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; margin-left: auto; }
        .main { margin-left: 260px; min-height: 100vh; background: #f8fafc; }
        .topbar { background: #fff; padding: 15px 30px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; }
        .search-box { display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 8px 15px; border-radius: 20px; width: 300px; }
        .search-box input { background: transparent; border: none; outline: none; width: 100%; font-size: 0.9rem; }
        .top-actions { display: flex; align-items: center; gap: 20px; }
        .notification { position: relative; cursor: pointer; }
        .notification span { position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 5px; font-size: 0.65rem; }
        .profile { display: flex; align-items: center; gap: 10px; }
        .avatar { width: 35px; height: 35px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    </style>
</head>
<body>

    <!-- SIDEBAR CAISSIÈRE -->
    <div class="sidebar" id="sidebar" style="overflow-y: auto; max-height: 100vh; display: flex; flex-direction: column;">
        <div style="flex: 1;">
            <div class="logo">
                <i class="fa-solid fa-cash-register"></i> RESTO KAY-Y
            </div>

            <div class="menu-title">Principal</div>
            <a href="/caisse/dashboard">
                <i class="fa-solid fa-chart-line"></i> Dashboard Caisse
            </a>

            <div class="menu-title">Opèrasyon</div>
            <a href="/cuisine">
                <i class="fa-solid fa-fire"></i> Ekran Kwizin
                <span class="badge">5</span>
            </a>

            <a href="/menu/1" target="_blank">
                <i class="fa-solid fa-utensils"></i> Gade Meni (Kliyan)
            </a>
        </div>

        <!-- Bouton Dekonekte -->
        <div style="padding: 15px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: auto;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width: 100%; background: transparent; border: none; color: #ff6b6b; display: flex; align-items: center; gap: 10px; padding: 10px; cursor: pointer; font-size: 14px; font-weight: bold; border-radius: 6px; text-align: left;" onmouseover="this.style.background='rgba(255,107,107,0.1)'" onmouseout="this.style.background='transparent'">
                    <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="topbar">
            <i class="fa-solid fa-bars menu-toggle" onclick="toggleMenu()"></i>

            <div class="search-box">
                <i class="fa fa-search text-gray-400"></i>
                <input placeholder="Rechercher yon kòmand...">
            </div>

            <div class="top-actions">
                <div class="notification">
                    <i class="fa-solid fa-bell text-gray-600"></i>
                    <span>2</span>
                </div>

                <div class="profile">
                    <div class="avatar">C</div>
                    <div>
                        <strong>{{ auth()->user()->name ?? 'Caissière' }}</strong><br>
                        <small class="text-gray-500">Resto Kay-Y</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Espace Caisse & Encaissement</h1>

            <!-- Cards Rezime -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-emerald-500">
                    <p class="text-sm font-semibold text-gray-500">Vant Jodia (Caisse)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">0.00 HTG</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-amber-500">
                    <p class="text-sm font-semibold text-gray-500">Kòmand ki Peye</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">0</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-semibold text-gray-500">Kòmand an Attente</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">0</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Aksyon Rapid</h2>
                <div class="flex gap-4">
                    <a href="/cuisine" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium shadow inline-flex items-center gap-2">
                        <i class="fa-solid fa-fire"></i> Swiv Kòmand nan Kwizin
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>