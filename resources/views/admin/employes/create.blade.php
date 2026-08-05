@extends('admin.layouts.app')

@section('content')

<style>

.page-card{
    max-width:950px;
    margin:40px auto;
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 15px 45px rgba(0,0,0,.08);
}

.page-header{
    background:linear-gradient(135deg,#f59e0b,#ea580c);
    color:white;
    padding:30px;
}

.page-header h1{
    margin:0;
    font-size:30px;
}

.page-header p{
    opacity:.9;
    margin-top:8px;
}

.form-body{
    padding:35px;
}

.alert-danger{
    background:#fee2e2;
    color:#b91c1c;
    padding:18px;
    border-radius:12px;
    margin-bottom:25px;
}

.alert-danger ul{
    margin:0;
    padding-left:18px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group.full{
    grid-column:1/-1;
}

label{
    font-weight:700;
    margin-bottom:8px;
    color:#374151;
}

input,
select{
    height:50px;
    border:1px solid #d1d5db;
    border-radius:12px;
    padding:0 15px;
    font-size:15px;
    transition:.3s;
    background:#fafafa;
}

input:focus,
select:focus{
    outline:none;
    border-color:#f59e0b;
    box-shadow:0 0 0 4px rgba(245,158,11,.15);
    background:white;
}

input[type=file]{
    padding:12px;
    height:auto;
}

.preview-box{
    margin-top:15px;
    display:flex;
    justify-content:center;
}

.preview-box img{
    width:120px;
    height:120px;
    object-fit:cover;
    border-radius:50%;
    border:4px solid #f3f4f6;
    display:none;
}

.action-bar{
    margin-top:35px;
    display:flex;
    justify-content:flex-end;
    gap:15px;
}

.btn{
    padding:14px 24px;
    border:none;
    border-radius:12px;
    font-weight:bold;
    cursor:pointer;
    text-decoration:none;
    transition:.3s;
}

.btn-secondary{
    background:#e5e7eb;
    color:#374151;
}

.btn-secondary:hover{
    background:#d1d5db;
}

.btn-primary{
    background:linear-gradient(135deg,#f59e0b,#ea580c);
    color:white;
}

.btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(234,88,12,.3);
}

@media(max-width:768px){

.form-grid{
grid-template-columns:1fr;
}

.page-card{
margin:15px;
}

}

</style>

<div class="page-card">

    <div class="page-header">

        <h1>👨‍💼 Ajouter un employé</h1>

        <p>
            Complétez les informations ci-dessous pour enregistrer un nouvel employé.
        </p>

    </div>

    <div class="form-body">

        @if ($errors->any())

            <div class="alert-danger">

                <strong>Des erreurs ont été détectées :</strong>

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form action="/admin/employes"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>Nom</label>

                    <input
                        type="text"
                        name="nom"
                        value="{{ old('nom') }}"
                        required>

                </div>

                <div class="form-group">

                    <label>Prénom</label>

                    <input
                        type="text"
                        name="prenom"
                        value="{{ old('prenom') }}"
                        required>

                </div>

                <div class="form-group">

                    <label>Adresse e-mail</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required>

                </div>

                <div class="form-group">

                    <label>Téléphone</label>

                    <input
                        type="text"
                        name="telephone"
                        value="{{ old('telephone') }}"
                        required>

                </div>

                <div class="form-group">

                    <label>Fonction</label>

                    <select name="role" required>

                        <option value="">Sélectionner...</option>

                        <option value="caissiere">Caissière</option>

                        <option value="serveur">Serveur</option>

                        <option value="serveuse">Serveuse</option>

                        <option value="cuisine">Cuisinier</option>

                        <option value="autre">Autre</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Salaire mensuel (HTG)</label>

                    <input
                        type="number"
                        step="0.01"
                        name="salaire"
                        value="{{ old('salaire') }}"
                        required>

                </div>

                <div class="form-group full">

                    <label>Photo de l'employé</label>

                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        accept="image/*">

                    <div class="preview-box">

                        <img id="preview">

                    </div>

                </div>

            </div>

            <div class="action-bar">

                <a href="/admin/employes"
                   class="btn btn-secondary">

                    Retour

                </a>

                <button
                    class="btn btn-primary"
                    type="submit">

                    💾 Enregistrer l'employé

                </button>

            </div>

        </form>

    </div>

</div>

<script>

document
.getElementById("photo")
.addEventListener("change",function(e){

const file=e.target.files[0];

if(!file)return;

const reader=new FileReader();

reader.onload=function(ev){

const img=document.getElementById("preview");

img.src=ev.target.result;

img.style.display="block";

}

reader.readAsDataURL(file);

});

</script>

@endsection