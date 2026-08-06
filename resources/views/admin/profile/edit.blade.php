@extends('admin.layouts.app')

@section('content')

<div class="notifications-page">

    <!-- =========================
            HEADER
    ========================== -->
    <div class="notifications-header">
        <div class="notifications-title">
            <div class="notifications-icon">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h1>Mon Profil &amp; Sécurité</h1>
                <p>Mettez à jour vos informations personnelles et changez votre mot de passe.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl font-semibold flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl text-sm shadow-sm">
            <div class="font-bold flex items-center gap-2 mb-1">
                <i class="fa-solid fa-triangle-exclamation">></i> Veuillez corriger les erreurs suivantes :
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- =========================
                CARD: INFOS GENERALES
        ========================== -->
        <div class="notification-card lg:col-span-2">
            <div class="card-header">
                <h2>Informations Personnelles</h2>
                <span class="badge-count">{{ ucfirst($user->role) }}</span>
            </div>

            <form action="/admin/profile" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom complet</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full pl-11 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-gray-50 text-sm font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Adresse Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full pl-11 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-gray-50 text-sm font-semibold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Téléphone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="w-full pl-11 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-gray-50 text-sm font-semibold" placeholder="Ex: +509 3700-0000">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 my-4">

                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-amber-600"></i> Modification du Mot de passe <small class="text-gray-400 font-normal">(Optionnel)</small>
                </h3>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe actuel</label>
                    <input type="password" name="current_password" placeholder="Laissez vide si vous ne changez pas de mot de passe" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-gray-50 text-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nouveau mot de passe</label>
                        <input type="password" name="password" placeholder="Minimum 6 caractères" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-gray-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="password_confirmation" placeholder="Répéter le mot de passe" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-gray-50 text-sm">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="dashboard-btn">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        <!-- =========================
                CARD: RESUME & STATS
        ========================== -->
        <div class="space-y-6">
            <div class="notification-card text-center p-6">
                <div class="w-24 h-24 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-4xl font-extrabold mx-auto mb-4 shadow-inner">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-amber-600 font-bold uppercase text-xs mt-1">{{ $user->role }}</p>
                
                <div class="mt-6 pt-6 border-t border-gray-100 text-left space-y-3 text-sm">
                    <div class="flex justify-between items-center text-gray-600">
                        <span><i class="fa-solid fa-calendar mr-2 text-amber-500"></i> Membre depuis :</span>
                        <strong class="text-gray-800">{{ $user->created_at->format('d/m/Y') }}</strong>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span><i class="fa-solid fa-shield-halved mr-2 text-amber-500"></i> Statut du compte :</span>
                        <strong class="text-emerald-600">Actif</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
/*=====================================================
    PROFILE - RESTO KAY-Y DESIGN SYSTEM
======================================================*/
:root{
    --primary:#f59e0b;
    --primary-dark:#d97706;
    --success:#10b981;
    --danger:#ef4444;
    --info:#3b82f6;
    --dark:#1f2937;
    --text:#374151;
    --muted:#6b7280;
    --bg:#f8fafc;
    --card:#ffffff;
    --border:#e5e7eb;
}

.notifications-page{
    max-width:1450px;
    margin:auto;
    padding:30px;
    animation:fadeUp .5s ease-in-out;
}

.notifications-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
    gap:20px;
}

.notifications-title{
    display:flex;
    align-items:center;
    gap:18px;
}

.notifications-icon{
    width:75px;
    height:75px;
    border-radius:22px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:32px;
    color:#fff;
    background:linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow:0 18px 35px rgba(245,158,11,.30);
}

.notifications-title h1{
    margin:0;
    font-size:30px;
    color:var(--dark);
    font-weight:800;
}

.notifications-title p{
    margin-top:5px;
    color:var(--muted);
    font-size:14px;
}

.dashboard-btn{
    padding:12px 24px;
    border-radius:14px;
    color:#fff;
    text-decoration:none;
    background:linear-gradient(135deg, #f59e0b, #d97706);
    transition:.35s;
    font-weight:700;
    display:inline-flex;
    align-items:center;
    gap:8px;
    border:none;
    cursor:pointer;
    box-shadow:0 10px 20px rgba(245,158,11,.25);
}

.dashboard-btn:hover{
    transform:translateY(-3px);
    background:linear-gradient(135deg, #d97706, #b45309);
}

.notification-card{
    background:#fff;
    border-radius:22px;
    box-shadow:0 20px 45px rgba(15,23,42,.08);
    overflow:hidden;
}

.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 28px;
    background:#fffaf2;
    border-bottom:1px solid #f3f4f6;
}

.card-header h2{
    margin:0;
    font-size:20px;
    color:#1f2937;
}

.badge-count{
    background:#fef3c7;
    color:#b45309;
    padding:6px 14px;
    border-radius:30px;
    font-weight:700;
    font-size:13px;
}

@keyframes fadeUp{
    from{ opacity:0; transform:translateY(20px); }
    to{ opacity:1; transform:translateY(0); }
}

@media(max-width:1024px){
    .grid-cols-3{ grid-template-columns:1fr; }
}
</style>
@endsection