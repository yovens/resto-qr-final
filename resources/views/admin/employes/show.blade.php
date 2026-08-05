@extends('admin.layouts.app')

@section('content')

<style>

.employee-card{
    max-width:950px;
    margin:40px auto;
    background:#fff;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
}

.employee-header{
    background:linear-gradient(135deg,#f59e0b,#ea580c);
    padding:40px;
    text-align:center;
    color:#fff;
}

.employee-photo{
    width:150px;
    height:150px;
    border-radius:50%;
    object-fit:cover;
    border:6px solid rgba(255,255,255,.35);
}

.employee-avatar{
    width:150px;
    height:150px;
    border-radius:50%;
    background:white;
    color:#ea580c;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:60px;
    font-weight:bold;
    margin:auto;
    border:6px solid rgba(255,255,255,.35);
}

.employee-header h1{
    margin-top:20px;
    font-size:34px;
}

.role{
    display:inline-block;
    margin-top:12px;
    padding:8px 20px;
    border-radius:30px;
    background:white;
    color:#ea580c;
    font-weight:bold;
}

.employee-body{
    padding:35px;
}

.info-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
}

.info-card{
    background:#f8fafc;
    padding:22px;
    border-radius:18px;
    border-left:5px solid #f59e0b;
}

.info-card h4{
    margin:0;
    color:#6b7280;
    font-size:14px;
}

.info-card p{
    margin-top:8px;
    font-size:18px;
    font-weight:bold;
    color:#111827;
}

.salary{
    color:#16a34a;
}

.actions{
    margin-top:35px;
    display:flex;
    justify-content:center;
    gap:20px;
}

.btn{
    padding:14px 28px;
    border-radius:12px;
    text-decoration:none;
    font-weight:bold;
    transition:.3s;
}

.btn-back{
    background:#e5e7eb;
    color:#374151;
}

.btn-edit{
    background:linear-gradient(135deg,#f59e0b,#ea580c);
    color:white;
}

.btn-delete{
    background:#dc2626;
    color:white;
    border:none;
    cursor:pointer;
}

.btn:hover{
    transform:translateY(-3px);
}

@media(max-width:768px){

.info-grid{
grid-template-columns:1fr;
}

.employee-card{
margin:15px;
}

.actions{
flex-direction:column;
}

}
.employee-header{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    margin-bottom:35px;
}

.employee-photo{
    width:160px;
    height:160px;
    border-radius:50%;
    object-fit:cover;
    border:6px solid #f59e0b;
    box-shadow:0 10px 30px rgba(0,0,0,.18);
    margin-bottom:18px;
    background:#fff;
}

.employee-avatar{
    width:160px;
    height:160px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
    color:#fff;
    font-size:60px;
    font-weight:700;
    box-shadow:0 10px 30px rgba(0,0,0,.18);
    margin-bottom:18px;
}

.employee-header h1{
    margin:0;
    font-size:30px;
    font-weight:700;
    color:#1f2937;
}

.employee-header .role{
    margin-top:10px;
    background:#fff3cd;
    color:#b45309;
    padding:8px 22px;
    border-radius:30px;
    font-weight:700;
    text-transform:capitalize;
    letter-spacing:.5px;
}
</style>

<div class="employee-card">

<div class="employee-header">

    @if($employe->photo)

        <img
            src="{{ asset($employe->photo) }}"
            alt="{{ $employe->prenom }}"
            class="employee-photo">

    @else

        <div class="employee-avatar">
            {{ strtoupper(substr($employe->prenom,0,1)) }}
        </div>

    @endif

    <h1>{{ $employe->prenom }} {{ $employe->nom }}</h1>

    <span class="role">
        {{ ucfirst($employe->role) }}
    </span>

</div>

    <div class="employee-body">

        <div class="info-grid">

            <div class="info-card">
                <h4>Adresse e-mail</h4>
                <p>{{ $employe->email }}</p>
            </div>

            <div class="info-card">
                <h4>Téléphone</h4>
                <p>{{ $employe->telephone }}</p>
            </div>

            <div class="info-card">
                <h4>Salaire mensuel</h4>
                <p class="salary">
                    {{ number_format($employe->salaire,2) }} HTG
                </p>
            </div>

            <div class="info-card">
                <h4>Date d'embauche</h4>
                <p>
                    {{ $employe->created_at->format('d/m/Y') }}
                </p>
            </div>

            <div class="info-card">
                <h4>Identifiant</h4>
                <p>#{{ $employe->id }}</p>
            </div>

            <div class="info-card">
                <h4>Statut</h4>

                <p style="color:#16a34a">
                    ● Actif
                </p>

            </div>

        </div>

        <div class="actions">

            <a
            href="{{ url('/admin/employes') }}"
            class="btn btn-back">

                ← Retour

            </a>

            <a
            href="{{ url('/admin/employes/'.$employe->id.'/edit') }}"
            class="btn btn-edit">

                ✏ Modifier

            </a>

            <form
            action="{{ url('/admin/employes/'.$employe->id) }}"
            method="POST"
            onsubmit="return confirm('Supprimer cet employé ?')">

                @csrf
                @method('DELETE')

                <button
                class="btn btn-delete">

                    🗑 Supprimer

                </button>

            </form>

        </div>

    </div>

</div>

@endsection