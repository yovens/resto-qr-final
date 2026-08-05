@extends('admin.layouts.app')

@section('content')

<style>

.page-card{
    max-width:950px;
    margin:40px auto;
    background:#fff;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 15px 45px rgba(0,0,0,.08);
}

.page-header{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
    padding:35px;
}

.page-header h1{
    margin:0;
    font-size:30px;
}

.page-header p{
    margin-top:8px;
    opacity:.9;
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
    font-weight:bold;
    color:#374151;
    margin-bottom:8px;
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
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
    background:white;
}

input[type=file]{
    padding:12px;
    height:auto;
}

.preview-box{
    display:flex;
    justify-content:center;
    margin-top:20px;
}

.preview-box img{
    width:140px;
    height:140px;
    object-fit:cover;
    border-radius:50%;
    border:5px solid #e5e7eb;
}

.actions{
    margin-top:35px;
    display:flex;
    justify-content:flex-end;
    gap:15px;
}

.btn{
    padding:14px 28px;
    border:none;
    border-radius:12px;
    text-decoration:none;
    cursor:pointer;
    font-weight:bold;
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
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
}

.btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(37,99,235,.3);
}

@media(max-width:768px){

.form-grid{
grid-template-columns:1fr;
}

.page-card{
margin:15px;
}

.actions{
flex-direction:column;
}

}

</style>

<div class="page-card">

<div class="page-header">

<h1>✏ Modifier un employé</h1>

<p>
Mettre à jour les informations de l'employé.
</p>

</div>

<div class="form-body">

@if($errors->any())

<div class="alert-danger">

<strong>Des erreurs ont été détectées :</strong>

<ul>

@foreach($errors->all() as $error)

<li>{{ $error }}</li>

@endforeach

</ul>

</div>

@endif

<form
action="/admin/employes/{{ $employe->id }}"
method="POST"
enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="form-grid">

<div class="form-group">

<label>Nom</label>

<input
type="text"
name="nom"
value="{{ old('nom',$employe->nom) }}"
required>

</div>

<div class="form-group">

<label>Prénom</label>

<input
type="text"
name="prenom"
value="{{ old('prenom',$employe->prenom) }}"
required>

</div>

<div class="form-group">

<label>Adresse e-mail</label>

<input
type="email"
name="email"
value="{{ old('email',$employe->email) }}"
required>

</div>

<div class="form-group">

<label>Téléphone</label>

<input
type="text"
name="telephone"
value="{{ old('telephone',$employe->telephone) }}"
required>

</div>

<div class="form-group">

<label>Fonction</label>

<select name="role">

<option value="caissiere"
{{ $employe->role=='caissiere'?'selected':'' }}>
Caissière
</option>

<option value="serveur"
{{ $employe->role=='serveur'?'selected':'' }}>
Serveur
</option>

<option value="serveuse"
{{ $employe->role=='serveuse'?'selected':'' }}>
Serveuse
</option>

<option value="cuisine"
{{ $employe->role=='cuisine'?'selected':'' }}>
Cuisinier
</option>

<option value="autre"
{{ $employe->role=='autre'?'selected':'' }}>
Autre
</option>

</select>

</div>

<div class="form-group">

<label>Salaire mensuel (HTG)</label>

<input
type="number"
step="0.01"
name="salaire"
value="{{ old('salaire',$employe->salaire) }}"
required>

</div>

<div class="form-group full">

<label>Photo</label>

<input
type="file"
id="photo"
name="photo"
accept="image/*">

<div class="preview-box">

@if($employe->photo)

<img src="{{ asset($employe->photo) }}">
@else

<img
id="preview"
style="display:none;">

@endif

</div>

</div>

</div>

<div class="actions">

<a
href="/admin/employes"
class="btn btn-secondary">

Retour

</a>

<button
type="submit"
class="btn btn-primary">

💾 Enregistrer les modifications

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