@extends('admin.layouts.app')

@section('content')
<div class="user-profile-page">

    <div class="user-profile-card">

        <div class="user-header">

            <div class="user-avatar">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>

            <h1>{{ $user->name }}</h1>

            <span class="user-role">
                {{ $user->role }}
            </span>

        </div>

        <div class="user-body">

            <div class="user-info">

                <div class="user-label">

                    <i class="fa-solid fa-envelope"></i>

                    Email

                </div>

                <div class="user-value">

                    {{ $user->email }}

                </div>

            </div>

            <div class="user-info">

                <div class="user-label">

                    <i class="fa-solid fa-phone"></i>

                    Téléphone

                </div>

                <div class="user-value">

                    {{ $user->telephone ?? 'Non renseigné' }}

                </div>

            </div>

            <div class="user-info">

                <div class="user-label">

                    <i class="fa-solid fa-calendar"></i>

                    Date de création

                </div>

                <div class="user-value">

                    {{ $user->created_at->format('d/m/Y H:i') }}

                </div>

            </div>

        </div>

        <div class="user-footer">

            <a href="/admin/users" class="btn-user btn-back">

                <i class="fa-solid fa-arrow-left"></i>

                Retour

            </a>

            <a href="/admin/users/{{ $user->id }}/edit" class="btn-user btn-edit">

                <i class="fa-solid fa-pen-to-square"></i>

                Modifier

            </a>

        </div>

    </div>

</div>
<style>
    /*======================================
        USER PROFILE
======================================*/

.user-profile-page{

    max-width:750px;

    margin:35px auto;

    animation:fadeUp .5s ease;

}

.user-profile-card{

    background:#fff;

    border-radius:24px;

    overflow:hidden;

    box-shadow:
    0 20px 45px rgba(15,23,42,.08);

}

/*======================================
        HEADER
======================================*/

.user-header{

    position:relative;

    padding:45px 35px;

    text-align:center;

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );

    color:#fff;

}

.user-header::before{

    content:"";

    position:absolute;

    inset:0;

    background:

    radial-gradient(circle at top right,
    rgba(255,255,255,.18),
    transparent 55%);

}

.user-avatar{

    position:relative;

    z-index:2;

    width:120px;

    height:120px;

    margin:auto;

    border-radius:50%;

    display:flex;

    justify-content:center;

    align-items:center;

    background:#fff;

    color:#d97706;

    font-size:48px;

    font-weight:800;

    border:6px solid rgba(255,255,255,.35);

    box-shadow:
    0 18px 40px rgba(0,0,0,.18);

}

.user-header h1{

    position:relative;

    z-index:2;

    margin-top:18px;

    margin-bottom:8px;

    font-size:30px;

    font-weight:800;

}

.user-role{

    position:relative;

    z-index:2;

    display:inline-block;

    padding:8px 20px;

    border-radius:30px;

    background:rgba(255,255,255,.18);

    border:1px solid rgba(255,255,255,.35);

    text-transform:uppercase;

    font-weight:700;

    letter-spacing:1px;

}

/*======================================
        BODY
======================================*/

.user-body{

    padding:35px;

}

.user-info{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:18px 20px;

    margin-bottom:18px;

    border-radius:16px;

    background:#f8fafc;

    transition:.3s;

}

.user-info:hover{

    background:#fff7ed;

    transform:translateX(5px);

}

.user-label{

    display:flex;

    align-items:center;

    gap:10px;

    color:#64748b;

    font-weight:700;

}

.user-label i{

    width:38px;

    height:38px;

    display:flex;

    justify-content:center;

    align-items:center;

    border-radius:10px;

    background:#fef3c7;

    color:#d97706;

}

.user-value{

    color:#1f2937;

    font-weight:700;

}

/*======================================
        FOOTER
======================================*/

.user-footer{

    padding:25px 35px;

    display:flex;

    justify-content:center;

    gap:18px;

    border-top:1px solid #e5e7eb;

}

.btn-user{

    padding:13px 26px;

    border-radius:14px;

    text-decoration:none;

    color:#fff;

    font-weight:700;

    transition:.35s;

    display:flex;

    align-items:center;

    gap:10px;

}

.btn-user:hover{

    transform:translateY(-4px);

}

.btn-back{

    background:#64748b;

}

.btn-back:hover{

    background:#475569;

}

.btn-edit{

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );

    box-shadow:
    0 10px 25px rgba(245,158,11,.28);

}

/*======================================
        ANIMATION
======================================*/

@keyframes fadeUp{

    from{

        opacity:0;

        transform:translateY(30px);

    }

    to{

        opacity:1;

        transform:translateY(0);

    }

}

/*======================================
        RESPONSIVE
======================================*/

@media(max-width:768px){

    .user-body{

        padding:25px;

    }

    .user-info{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }

    .user-footer{

        flex-direction:column;

    }

    .btn-user{

        justify-content:center;

    }

}
</style>
@endsection