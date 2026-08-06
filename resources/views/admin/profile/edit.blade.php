@extends('admin.layouts.app')

@section('content')

<div class="profile-page">


    <!-- HEADER -->
    <div class="profile-header">

        <div class="profile-title">

            <div class="profile-icon">
                <i class="fa-solid fa-user-gear"></i>
            </div>

            <div>
                <h1>Mon Profil & Sécurité</h1>

                <p>
                    Mettez à jour vos informations personnelles
                    et protégez votre compte administrateur.
                </p>
            </div>

        </div>

    </div>



    <!-- ALERTS -->

    @if(session('success'))

    <div class="alert-success">

        <i class="fa-solid fa-circle-check"></i>

        {{ session('success') }}

    </div>

    @endif



    @if($errors->any())

    <div class="alert-error">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <div>

            <strong>Veuillez corriger les erreurs :</strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    </div>

    @endif





<div class="profile-grid">



    <!-- FORMULAIRE -->

    <div class="profile-card large">


        <div class="card-header">

            <h2>
                <i class="fa-solid fa-user"></i>
                Informations personnelles
            </h2>

            <span class="role-badge">
                {{ ucfirst($user->role) }}
            </span>

        </div>




        <form action="/admin/profile"
              method="POST"
              class="profile-form">

            @csrf
            @method('PUT')



            <div class="form-group">

                <label>
                    Nom complet
                </label>


                <div class="input-box">

                    <i class="fa-solid fa-user"></i>

                    <input type="text"
                           name="name"
                           value="{{ old('name',$user->name) }}"
                           required>

                </div>


            </div>




            <div class="form-row">


                <div class="form-group">

                    <label>Email</label>

                    <div class="input-box">

                        <i class="fa-solid fa-envelope"></i>

                        <input type="email"
                               name="email"
                               value="{{ old('email',$user->email) }}"
                               required>

                    </div>


                </div>



                <div class="form-group">

                    <label>Téléphone</label>

                    <div class="input-box">

                        <i class="fa-solid fa-phone"></i>

                        <input type="text"
                               name="telephone"
                               value="{{ old('telephone',$user->telephone) }}">

                    </div>


                </div>


            </div>




            <div class="separator"></div>




            <h3>
                <i class="fa-solid fa-lock"></i>

                Changer le mot de passe

            </h3>



            <div class="form-group">

                <label>
                    Mot de passe actuel
                </label>

                <input type="password"
                       name="current_password"
                       placeholder="Mot de passe actuel">


            </div>




            <div class="form-row">


                <div class="form-group">

                    <label>Nouveau mot de passe</label>

                    <input type="password"
                           name="password"
                           placeholder="Minimum 6 caractères">

                </div>



                <div class="form-group">

                    <label>Confirmation</label>

                    <input type="password"
                           name="password_confirmation"
                           placeholder="Répéter">

                </div>


            </div>




            <button class="save-btn">

                <i class="fa-solid fa-floppy-disk"></i>

                Enregistrer les modifications

            </button>



        </form>


    </div>







    <!-- PROFIL -->

    <div class="profile-card">


        <div class="avatar">

            {{ strtoupper(substr($user->name,0,1)) }}

        </div>



        <h2 class="username">

            {{ $user->name }}

        </h2>


        <span class="user-role">

            {{ ucfirst($user->role) }}

        </span>




        <div class="profile-info">


            <div>

                <span>
                    <i class="fa-solid fa-calendar"></i>
                    Création
                </span>


                <strong>
                    {{ $user->created_at->format('d/m/Y') }}
                </strong>

            </div>




            <div>

                <span>

                    <i class="fa-solid fa-shield-halved"></i>
                    Statut

                </span>


                <strong class="active">

                    Actif

                </strong>


            </div>



        </div>


    </div>



</div>


</div>
<style>
    /* ===============================
        PROFILE PAGE
================================*/

.profile-page{

    padding:30px;

}



.profile-header{

    margin-bottom:35px;

}



.profile-title{

    display:flex;
    align-items:center;
    gap:20px;

}



.profile-icon{

    width:80px;
    height:80px;

    background:linear-gradient(135deg,#f59e0b,#d97706);

    color:white;

    border-radius:22px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:35px;

    box-shadow:0 15px 35px rgba(245,158,11,.3);

}



.profile-title h1{

    font-size:30px;

    font-weight:800;

    color:#1f2937;

}



.profile-title p{

    color:#6b7280;

}





/* ALERT */

.alert-success,
.alert-error{

    padding:16px;

    border-radius:15px;

    margin-bottom:25px;

    display:flex;

    gap:12px;

    align-items:center;

    font-weight:600;

}


.alert-success{

    background:#dcfce7;

    color:#166534;

}



.alert-error{

    background:#fee2e2;

    color:#991b1b;

}




/* GRID */

.profile-grid{

    display:grid;

    grid-template-columns:2fr 1fr;

    gap:30px;

}





/* CARD */

.profile-card{

    background:white;

    border-radius:22px;

    padding:25px;

    box-shadow:0 15px 40px rgba(0,0,0,.07);

    border:1px solid #eee;

}



.profile-card.large{

    padding:0;

}





/* HEADER CARD */

.card-header{

    padding:22px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    background:#fafafa;

    border-bottom:1px solid #eee;

}



.card-header h2{

    font-size:20px;

    font-weight:800;

}



.role-badge{

    background:#fef3c7;

    color:#b45309;

    padding:8px 15px;

    border-radius:30px;

    font-size:13px;

    font-weight:700;

}





/* FORM */

.profile-form{

    padding:25px;

}



.form-row{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

}



.form-group{

    margin-bottom:20px;

}



.form-group label{

    display:block;

    margin-bottom:8px;

    font-weight:700;

    color:#374151;

}



.input-box{

    position:relative;

}



.input-box i{

    position:absolute;

    left:15px;

    top:50%;

    transform:translateY(-50%);

    color:#9ca3af;

}



input{

    width:100%;

    padding:13px 15px 13px 45px;

    border-radius:14px;

    border:1px solid #ddd;

    background:#f9fafb;

}



input:focus{

    outline:none;

    border-color:#f59e0b;

    box-shadow:0 0 0 4px rgba(245,158,11,.15);

}




.separator{

    border-top:1px solid #eee;

    margin:25px 0;

}



h3{

    margin-bottom:20px;

    font-weight:800;

}





.save-btn{

    width:100%;

    background:linear-gradient(135deg,#f59e0b,#d97706);

    color:white;

    padding:15px;

    border:none;

    border-radius:15px;

    font-weight:800;

    cursor:pointer;

    font-size:15px;

}



.save-btn:hover{

    transform:translateY(-2px);

}





/* PROFILE SIDE */

.avatar{

    width:110px;

    height:110px;

    border-radius:50%;

    background:#fef3c7;

    color:#b45309;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:45px;

    font-weight:900;

    margin:auto;

}



.username{

    text-align:center;

    margin-top:15px;

    font-size:22px;

}



.user-role{

    display:block;

    text-align:center;

    color:#d97706;

    font-weight:800;

}



.profile-info{

    margin-top:30px;

    border-top:1px solid #eee;

    padding-top:20px;

}



.profile-info div{

    display:flex;

    justify-content:space-between;

    margin-bottom:18px;

}



.profile-info span{

    color:#6b7280;

}



.active{

    color:#059669;

}





/* RESPONSIVE */

@media(max-width:900px){

.profile-grid{

    grid-template-columns:1fr;

}


.form-row{

    grid-template-columns:1fr;

}

}
</style>
@endsection
