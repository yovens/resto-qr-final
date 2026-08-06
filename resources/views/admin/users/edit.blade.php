@extends('admin.layouts.app')

@section('content')

<div class="user-edit-page">

    <div class="user-edit-card">

        <!-- HEADER -->

        <div class="user-edit-header">

            <div class="user-edit-icon">
                <i class="fa-solid fa-user-pen"></i>
            </div>

            <div>
                <h1>Modifier l'Utilisateur</h1>
                <p>Modifiez les informations et les droits d'accès de cet utilisateur.</p>
            </div>

        </div>

        <!-- ERREURS -->

        @if($errors->any())

        <div class="user-error">

            <strong>
                <i class="fa-solid fa-circle-exclamation"></i>
                Des erreurs ont été détectées :
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

        @endif

        <!-- FORMULAIRE -->

        <form action="/admin/users/{{ $user->id }}"
              method="POST"
              class="user-form">

            @csrf
            @method('PUT')

            <!-- NOM -->

            <div class="form-group">

                <label>
                    Nom complet
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       placeholder="Nom complet"
                       required>

            </div>

            <!-- EMAIL + TELEPHONE -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Adresse Email
                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           placeholder="email@restaurant.com"
                           required>

                </div>

                <div class="form-group">

                    <label>
                        Téléphone
                    </label>

                    <input type="text"
                           name="telephone"
                           value="{{ old('telephone', $user->telephone) }}"
                           placeholder="+509 ..."
                    >

                </div>

            </div>

            <!-- ROLE + PASSWORD -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Rôle
                    </label>

                    <select name="role" required>

                        <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>
                            👑 Administrateur
                        </option>

                        <option value="caissier" {{ $user->role=='caissier' ? 'selected' : '' }}>
                            💰 Caissier
                        </option>

                        <option value="cuisinier" {{ $user->role=='cuisinier' ? 'selected' : '' }}>
                            👨‍🍳 Cuisinier
                        </option>

                        <option value="serveur" {{ $user->role=='serveur' ? 'selected' : '' }}>
                            🍽️ Serveur
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Nouveau mot de passe
                        <small>(Optionnel)</small>
                    </label>

                    <input type="password"
                           name="password"
                           placeholder="Laisser vide pour conserver l'ancien mot de passe">

                </div>

            </div>

            <!-- BOUTONS -->

            <div class="user-footer">

                <a href="/admin/users"
                   class="btn-cancel">

                    <i class="fa-solid fa-arrow-left"></i>

                    Annuler

                </a>

                <button type="submit"
                        class="btn-update">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Mettre à jour

                </button>

            </div>

        </form>

    </div>

</div>


<style>
    /*=========================================
        EDIT USER PAGE
=========================================*/

body{
    background:#f4f7fb;
}

/* PAGE */

.user-edit-page{
    padding:40px 20px;
}

/* CARD */

.user-edit-card{
    max-width:820px;
    margin:auto;
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    box-shadow:
        0 15px 40px rgba(0,0,0,.08);
}

/* HEADER */

.user-edit-header{
    display:flex;
    align-items:center;
    gap:20px;
    padding:28px 35px;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    color:#fff;
}

.user-edit-icon{
    width:70px;
    height:70px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.18);
    font-size:28px;
    backdrop-filter:blur(10px);
}

.user-edit-header h1{
    margin:0;
    font-size:30px;
    font-weight:700;
}

.user-edit-header p{
    margin-top:5px;
    opacity:.9;
}

/* FORM */

.user-form{
    padding:35px;
}

/* GRID */

.form-row{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:20px;
}

/* GROUP */

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:block;
    font-size:14px;
    font-weight:700;
    color:#374151;
    margin-bottom:8px;
}

/* INPUT */

.form-group input,
.form-group select{
    width:100%;
    padding:14px 16px;
    border:1px solid #d1d5db;
    border-radius:14px;
    background:#fff;
    font-size:15px;
    transition:.30s;
}

.form-group input:focus,
.form-group select:focus{
    outline:none;
    border-color:#f59e0b;
    box-shadow:0 0 0 4px rgba(245,158,11,.15);
}

/* PASSWORD */

.form-group small{
    color:#9ca3af;
    font-weight:500;
}

/* ERROR */

.user-error{
    margin:30px 35px 0;
    background:#fef2f2;
    border-left:5px solid #ef4444;
    color:#b91c1c;
    padding:18px;
    border-radius:12px;
}

.user-error strong{
    display:block;
    margin-bottom:8px;
}

.user-error ul{
    margin:0;
    padding-left:18px;
}

/* FOOTER */

.user-footer{
    display:flex;
    justify-content:flex-end;
    gap:15px;
    padding:25px 35px;
    border-top:1px solid #eee;
    background:#fafafa;
}

/* BUTTONS */

.btn-cancel{
    text-decoration:none;
    padding:13px 22px;
    border-radius:12px;
    background:#e5e7eb;
    color:#374151;
    font-weight:600;
    transition:.3s;
}

.btn-cancel:hover{
    background:#d1d5db;
}

.btn-update{
    border:none;
    cursor:pointer;
    padding:13px 28px;
    border-radius:12px;
    color:#fff;
    font-weight:700;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    box-shadow:0 8px 20px rgba(245,158,11,.30);
    transition:.30s;
}

.btn-update:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 28px rgba(245,158,11,.40);
}

/* SELECT */

select{
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='%23666'%3E%3Cpath d='M5.5 7l4.5 5 4.5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 15px center;
    padding-right:45px;
}

/* RESPONSIVE */

@media(max-width:768px){

    .user-edit-header{
        flex-direction:column;
        text-align:center;
    }

    .user-form{
        padding:25px;
    }

    .user-footer{
        flex-direction:column-reverse;
    }

    .btn-cancel,
    .btn-update{
        width:100%;
        text-align:center;
    }

}
</style>
@endsection