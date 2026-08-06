@extends('admin.layouts.app')

@section('content')

<div class="user-form-page">

    <div class="user-form-card">

        <!-- HEADER -->

        <div class="user-form-header">

            <div class="user-form-icon">

                <i class="fa-solid fa-user-plus"></i>

            </div>

            <div>

                <h1>
                    Ajouter un Utilisateur
                </h1>

                <p>
                    Créer un nouveau compte utilisateur pour le système.
                </p>

            </div>

        </div>



        <!-- ERREURS -->

        @if($errors->any())

        <div class="form-error">

            <strong>Erreurs détectées :</strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

        @endif



        <!-- FORMULAIRE -->

        <form action="/admin/users"
              method="POST"
              class="user-form">

            @csrf

            <div class="form-group">

                <label>Nom complet</label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nom complet"
                    required>

            </div>



            <div class="form-row">

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="email@restaurant.com"
                        required>

                </div>

                <div class="form-group">

                    <label>Téléphone</label>

                    <input
                        type="text"
                        name="telephone"
                        value="{{ old('telephone') }}"
                        placeholder="+509 ...">

                </div>

            </div>



            <div class="form-row">

                <div class="form-group">

                    <label>Rôle</label>

                    <select name="role">

                        <option value="admin">Administrateur</option>

                        <option value="caissier">Caissier</option>

                        <option value="cuisinier">Cuisinier</option>

                        <option value="serveur" selected>Serveur</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Mot de passe</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="********"
                        required>

                </div>

            </div>



            <div class="user-form-footer">

                <a href="/admin/users"
                   class="btn-cancel">

                    <i class="fa-solid fa-arrow-left"></i>

                    Annuler

                </a>

                <button
                    type="submit"
                    class="btn-save">

                    <i class="fa-solid fa-check"></i>

                    Enregistrer

                </button>

            </div>

        </form>

    </div>

</div>
<style>
    /*=========================================
        USER FORM
=========================================*/

.user-form-page{

    max-width:900px;

    margin:35px auto;

    animation:fadeUp .45s;

}

.user-form-card{

    background:#fff;

    border-radius:24px;

    overflow:hidden;

    box-shadow:
    0 20px 45px rgba(15,23,42,.08);

}

/*==========================*/

.user-form-header{

    display:flex;

    align-items:center;

    gap:20px;

    padding:35px;

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );

    color:#fff;

}

.user-form-icon{

    width:80px;

    height:80px;

    border-radius:20px;

    display:flex;

    justify-content:center;

    align-items:center;

    background:rgba(255,255,255,.18);

    font-size:32px;

}

.user-form-header h1{

    margin:0;

    font-size:30px;

    font-weight:800;

}

.user-form-header p{

    margin-top:8px;

    opacity:.9;

}

/*==========================*/

.user-form{

    padding:35px;

}

.form-row{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:20px;

}

.form-group{

    margin-bottom:20px;

}

.form-group label{

    display:block;

    margin-bottom:10px;

    font-weight:700;

    color:#374151;

}

.form-group input,

.form-group select{

    width:100%;

    padding:14px 16px;

    border:1px solid #d1d5db;

    border-radius:14px;

    font-size:15px;

    transition:.30s;

    outline:none;

    background:#fff;

}

.form-group input:focus,

.form-group select:focus{

    border-color:#f59e0b;

    box-shadow:
    0 0 0 4px rgba(245,158,11,.15);

}

/*==========================*/

.form-error{

    margin:30px;

    padding:18px;

    background:#fee2e2;

    color:#991b1b;

    border-left:5px solid #dc2626;

    border-radius:14px;

}

.form-error ul{

    margin-top:10px;

    padding-left:20px;

}

/*==========================*/

.user-form-footer{

    margin-top:20px;

    padding-top:25px;

    border-top:1px solid #e5e7eb;

    display:flex;

    justify-content:flex-end;

    gap:15px;

}

.btn-cancel,

.btn-save{

    padding:14px 24px;

    border-radius:14px;

    text-decoration:none;

    font-weight:700;

    transition:.35s;

    display:flex;

    align-items:center;

    gap:10px;

    cursor:pointer;

}

.btn-cancel{

    background:#64748b;

    color:#fff;

}

.btn-cancel:hover{

    background:#475569;

}

.btn-save{

    border:none;

    color:#fff;

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );

    box-shadow:
    0 12px 24px rgba(245,158,11,.25);

}

.btn-save:hover{

    transform:translateY(-3px);

}

/*==========================*/

@keyframes fadeUp{

    from{

        opacity:0;

        transform:translateY(25px);

    }

    to{

        opacity:1;

        transform:translateY(0);

    }

}

/*==========================*/

@media(max-width:768px){

    .user-form-header{

        flex-direction:column;

        text-align:center;

    }

    .form-row{

        grid-template-columns:1fr;

    }

    .user-form-footer{

        flex-direction:column;

    }

    .btn-cancel,

    .btn-save{

        justify-content:center;

    }

}
</style>
@endsection