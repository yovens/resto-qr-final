@extends('admin.layouts.app')

@section('content')

<style>
    .form-container { 
        max-width: 600px; 
        margin: 20px auto; 
        background: white; 
        padding: 40px; 
        border-radius: 20px; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }
    .form-group { margin-bottom: 20px; }
    label { display: block; font-weight: 600; margin-bottom: 8px; color: #444; }
    input[type="text"], input[type="number"], textarea, select { 
        width: 100%; 
        padding: 12px; 
        border: 2px solid #eee; 
        border-radius: 10px; 
        transition: 0.3s;
        box-sizing: border-box;
    }
    input:focus, textarea:focus { border-color: #2575fc; outline: none; }
    .btn-save { 
        width: 100%; 
        background: #2575fc; 
        color: white; 
        padding: 15px; 
        border: none; 
        border-radius: 12px; 
        font-size: 1rem; 
        font-weight: bold; 
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-save:hover { background: #1a5ac9; transform: scale(1.02); }
</style>

<div class="form-container">
    <h2 style="margin-top:0; color:#333;">🍔 Ajouter un Plat</h2>
    <p style="color:#777; margin-bottom: 30px;">Remplissez les détails ci-dessous pour ajouter un nouveau produit.</p>

    <form method="POST" action="{{ route('plats.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Nom du plat</label>
            <input type="text" name="nom" placeholder="Ex: Pizza Margherita" required>
        </div>

        <div class="form-group">
            <label>Prix (HTG)</label>
            <input type="number" name="prix" placeholder="0.00" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Décrivez les ingrédients..."></textarea>
        </div>

        <div class="form-group">
            <label>Catégorie</label>
            <select name="category_id">
                <option value="">Choisir une catégorie</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Image du plat</label>
            <input type="file" name="image" style="border:none; padding-left:0;">
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" name="disponible" value="1" checked style="width: 20px; height: 20px;">
            <label style="margin-bottom:0;">Disponible immédiatement</label>
        </div>

        <button type="submit" class="btn-save">💾 Enregistrer le plat</button>
    </form>
</div>

@endsection