@extends('admin.layouts.app')

@section('content')

<div class="users-page">

    <!-- HEADER -->

    <div class="users-header">

        <div>

            <h1>
                <i class="fa-solid fa-users"></i>
                Gestion des Utilisateurs & Rôles
            </h1>

            <p>
                Gérez les comptes, les rôles et les accès au système d'administration.
            </p>

        </div>

        <a href="/admin/users/create"
           class="btn-add-user">

            <i class="fa-solid fa-user-plus"></i>

            Nouvel Utilisateur

        </a>

    </div>




    <!-- ALERTES -->

    @if(session('success'))

    <div class="alert-success">

        <i class="fa-solid fa-circle-check"></i>

        {{ session('success') }}

    </div>

    @endif


    @if(session('error'))

    <div class="alert-error">

        <i class="fa-solid fa-circle-xmark"></i>

        {{ session('error') }}

    </div>

    @endif





    <!-- TABLEAU -->

    <div class="users-card">

        <table class="users-table">

            <thead>

                <tr>

                    <th>Utilisateur</th>

                    <th>Email</th>

                    <th>Téléphone</th>

                    <th>Rôle</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

            @forelse($users as $u)

                <tr>

                    <td>

                        <div class="user-info">

                            <div class="user-avatar">

                                {{ strtoupper(substr($u->name,0,1)) }}

                            </div>

                            <div>

                                <strong>{{ $u->name }}</strong>

                            </div>

                        </div>

                    </td>

                    <td>

                        {{ $u->email }}

                    </td>

                    <td>

                        {{ $u->telephone ?? 'Non renseigné' }}

                    </td>

                    <td>

                        @if($u->role=="admin")

                            <span class="badge admin">

                                👑 Administrateur

                            </span>

                        @elseif($u->role=="caissier")

                            <span class="badge cashier">

                                💰 Caissier

                            </span>

                        @elseif($u->role=="cuisinier")

                            <span class="badge cook">

                                👨‍🍳 Cuisinier

                            </span>

                        @else

                            <span class="badge waiter">

                                🍽️ Serveur

                            </span>

                        @endif

                    </td>

                    <td>

                        <div class="action-buttons">

                            <a href="/admin/users/{{ $u->id }}"
                               class="btn-view"
                               title="Voir">

                                <i class="fa-solid fa-eye"></i>

                            </a>

                            <a href="/admin/users/{{ $u->id }}/edit"
                               class="btn-edit"
                               title="Modifier">

                                <i class="fa-solid fa-pen"></i>

                            </a>

                            @if($u->id != auth()->id())

                            <form action="/admin/users/{{ $u->id }}"
                                  method="POST"
                                  style="display:inline-block"
                                  onsubmit="return confirm('Supprimer cet utilisateur ?')">

                                @csrf

                                @method('DELETE')

                                <button class="btn-delete">

                                    <i class="fa-solid fa-trash"></i>

                                </button>

                            </form>

                            @endif

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5">

                        <div class="empty-state">

                            <i class="fa-solid fa-users-slash"></i>

                            <h3>

                                Aucun utilisateur trouvé

                            </h3>

                            <p>

                                Commencez par créer votre premier utilisateur.

                            </p>

                        </div>

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>




    <!-- PAGINATION -->

    <div class="pagination-wrapper">

        {{ $users->links() }}

    </div>

</div>
<style>
    /*==================================================
            USERS LIST PAGE
==================================================*/

.users-page{
    max-width:1400px;
    margin:auto;
    padding:35px;
}

/* HEADER */

.users-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
    margin-bottom:30px;
}

.users-header h1{
    margin:0;
    font-size:32px;
    font-weight:700;
    color:#1f2937;
    display:flex;
    align-items:center;
    gap:12px;
}

.users-header h1 i{
    color:#f59e0b;
}

.users-header p{
    margin-top:8px;
    color:#6b7280;
    font-size:15px;
}

/* ADD BUTTON */

.btn-add-user{
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    padding:14px 24px;
    border-radius:14px;
    color:#fff;
    font-weight:600;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    box-shadow:0 10px 25px rgba(245,158,11,.30);
    transition:.35s;
}

.btn-add-user:hover{
    transform:translateY(-3px);
    box-shadow:0 16px 35px rgba(245,158,11,.40);
}

/* ALERT */

.alert-success,
.alert-error{
    display:flex;
    align-items:center;
    gap:10px;
    padding:16px 20px;
    border-radius:14px;
    margin-bottom:20px;
    font-weight:600;
}

.alert-success{
    background:#ecfdf5;
    color:#059669;
    border-left:5px solid #10b981;
}

.alert-error{
    background:#fef2f2;
    color:#dc2626;
    border-left:5px solid #ef4444;
}

/* CARD */

.users-card{
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
}

/* TABLE */

.users-table{
    width:100%;
    border-collapse:collapse;
}

.users-table thead{
    background:#f8fafc;
}

.users-table th{
    padding:18px;
    text-align:left;
    font-size:13px;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:#64748b;
}

.users-table td{
    padding:18px;
    border-top:1px solid #edf2f7;
    color:#374151;
    vertical-align:middle;
}

.users-table tbody tr{
    transition:.3s;
}

.users-table tbody tr:hover{
    background:#fffaf0;
}

/* USER */

.user-info{
    display:flex;
    align-items:center;
    gap:15px;
}

.user-avatar{
    width:52px;
    height:52px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    color:#fff;
    font-size:20px;
    font-weight:700;
    box-shadow:0 8px 18px rgba(245,158,11,.30);
}

/* BADGES */

.badge{
    display:inline-block;
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.admin{
    background:#fee2e2;
    color:#b91c1c;
}

.cashier{
    background:#dcfce7;
    color:#15803d;
}

.cook{
    background:#fef3c7;
    color:#b45309;
}

.waiter{
    background:#dbeafe;
    color:#2563eb;
}

/* ACTIONS */

.action-buttons{
    display:flex;
    justify-content:center;
    gap:10px;
}

.action-buttons a,
.action-buttons button{
    width:40px;
    height:40px;
    border:none;
    border-radius:12px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    transition:.3s;
    font-size:15px;
}

.btn-view{
    background:#eff6ff;
    color:#2563eb;
}

.btn-view:hover{
    background:#2563eb;
    color:#fff;
}

.btn-edit{
    background:#fff7ed;
    color:#d97706;
}

.btn-edit:hover{
    background:#d97706;
    color:#fff;
}

.btn-delete{
    background:#fef2f2;
    color:#dc2626;
}

.btn-delete:hover{
    background:#dc2626;
    color:#fff;
}

/* EMPTY */

.empty-state{
    text-align:center;
    padding:70px 20px;
}

.empty-state i{
    font-size:65px;
    color:#cbd5e1;
    margin-bottom:20px;
}

.empty-state h3{
    color:#374151;
    margin-bottom:10px;
}

.empty-state p{
    color:#6b7280;
}

/* PAGINATION */

.pagination-wrapper{
    margin-top:30px;
    display:flex;
    justify-content:center;
}

/* RESPONSIVE */

@media(max-width:992px){

.users-table{
    min-width:900px;
}

.users-card{
    overflow:auto;
}

}

@media(max-width:768px){

.users-page{
    padding:20px;
}

.users-header{
    flex-direction:column;
    align-items:flex-start;
}

.users-header h1{
    font-size:26px;
}

.btn-add-user{
    width:100%;
    justify-content:center;
}

.action-buttons{
    flex-wrap:wrap;
}

}
</style>
@endsection