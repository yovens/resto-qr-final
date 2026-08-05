@extends('admin.layouts.app')

@section('content')

<div class="employee-page">

    <div class="page-header">

        <div>
            <h1>👥 Gestion des employés</h1>
            <p>Liste complète du personnel du restaurant</p>
        </div>

        <a href="/admin/employes/create" class="btn-add">
            <i class="fa-solid fa-user-plus"></i>
            Nouvel employé
        </a>

    </div>

    @if(session('success'))

    <div class="success-box">

        <i class="fa-solid fa-circle-check"></i>

        {{ session('success') }}

    </div>

    @endif


    <div class="employee-table">

        <table>

            <thead>

            <tr>

                <th>Photo</th>

                <th>Nom complet</th>

                <th>Fonction</th>

                <th>Téléphone</th>

                <th>Salaire</th>

                <th>Actions</th>

            </tr>

            </thead>

            <tbody>

            @forelse($employes as $emp)

            <tr>

            <td>

    @if($emp->photo)

        <img
            src="{{ asset($emp->photo) }}"
            alt="{{ $emp->prenom }}"
            class="employee-avatar">

    @else

        <div class="avatar initials">

            {{ strtoupper(substr($emp->prenom,0,1)) }}

        </div>

    @endif

</td>

                <td>

                    <strong>

                        {{ $emp->prenom }}

                        {{ $emp->nom }}

                    </strong>

                </td>

                <td>

                    @if($emp->role=="caissiere")

                        <span class="badge green">

                            Caissière

                        </span>

                    @elseif($emp->role=="serveur")

                        <span class="badge blue">

                            Serveur

                        </span>

                    @elseif($emp->role=="serveuse")

                        <span class="badge blue">

                            Serveuse

                        </span>

                    @elseif($emp->role=="cuisinier")

                        <span class="badge orange">

                            Cuisinier

                        </span>

                    @else

                        <span class="badge gray">

                            {{ ucfirst($emp->role) }}

                        </span>

                    @endif

                </td>

                <td>

                    {{ $emp->telephone }}

                </td>

                <td>

                    <strong>

                        {{ number_format($emp->salaire,2) }}

                        HTG

                    </strong>

                </td>

                <td>

                    <div class="actions">

                        <a href="/admin/employes/{{ $emp->id }}" class="view">

                            <i class="fa-solid fa-eye"></i>

                        </a>

                        <a href="/admin/employes/{{ $emp->id }}/edit" class="edit">

                            <i class="fa-solid fa-pen"></i>

                        </a>

                        <form action="/admin/employes/{{ $emp->id }}" method="POST" onsubmit="return confirm('Supprimer cet employé ?')">

                            @csrf

                            @method('DELETE')

                            <button class="delete">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="6" class="empty">

                    Aucun employé enregistré.

                </td>

            </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="pagination-box">

        {{ $employes->links() }}

    </div>

</div>


<style>
    /*=============================
=      PHOTO EMPLOYÉ
=============================*/

.employee-avatar{

    width:60px;

    height:60px;

    border-radius:50%;

    object-fit:cover;

    border:3px solid #f59e0b;

    box-shadow:0 5px 15px rgba(0,0,0,.15);

    transition:.3s;

    cursor:pointer;

}

.employee-avatar:hover{

    transform:scale(1.12);

    box-shadow:0 8px 20px rgba(245,158,11,.4);

}

/* Avatar si pa gen foto */

.avatar.initials{

    width:60px;

    height:60px;

    border-radius:50%;

    background:linear-gradient(135deg,#f59e0b,#d97706);

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;

    font-weight:bold;

    box-shadow:0 5px 15px rgba(0,0,0,.15);

}
    .employee-page{

padding:30px;

}

.page-header{

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:30px;

}

.page-header h1{

font-size:32px;

margin-bottom:8px;

color:#1f2937;

}

.page-header p{

color:#6b7280;

}

.btn-add{

background:linear-gradient(135deg,#ff8a00,#ff5e00);

color:white;

padding:14px 25px;

border-radius:12px;

text-decoration:none;

font-weight:bold;

box-shadow:0 8px 20px rgba(255,94,0,.3);

transition:.3s;

}

.btn-add:hover{

transform:translateY(-3px);

}

.success-box{

background:#dcfce7;

padding:18px;

border-left:6px solid #16a34a;

margin-bottom:25px;

border-radius:12px;

font-weight:bold;

color:#166534;

}

.actions{

display:flex;

justify-content:center;

align-items:center;

gap:15px;

}

.actions a,

.actions button{

border:none;

background:none;

font-size:20px;

cursor:pointer;

transition:.3s;

}

.view{

color:#2563eb;

}

.edit{

color:#f59e0b;

}

.delete{

color:#dc2626;

}

.actions a:hover,

.actions button:hover{

transform:scale(1.2);

}

.empty{

text-align:center;

padding:50px;

font-size:18px;

color:#888;

}

.pagination-box{

margin-top:30px;

display:flex;

justify-content:center;

}





</style>

@endsection