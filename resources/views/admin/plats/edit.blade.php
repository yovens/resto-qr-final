@extends('admin.layouts.app')

@section('content')

<style>
    .edit-container { max-width: 650px; margin: 20px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.07); }
    .form-group { margin-bottom: 20px; }
    label { display: block; font-weight: 600; margin-bottom: 8px; color: #555; }
    input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; box-sizing: border-box; transition: 0.3s; }
    input:focus, textarea:focus { border-color: #007bff; outline: none; }
    
    .current-img-box { margin: 15px 0; padding: 15px; border: 2px dashed #eee; border-radius: 12px; display: flex; align-items: center; gap: 15px; }
    .btn-update { width: 100%; background: #007bff; color: white; padding: 15px; border: none; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-update:hover { background: #0056b3; transform: translateY(-2px); }
</style>

<div class="edit-container">
    <h2 style="margin-top:0; color:#333;">✏️ Modifier le plat : {{ $plat->nom }}</h2>
    
    <form method="POST" action="/admin/plats/{{ $plat->id }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="form-group">
            <label>Nom du plat</label>
            <input name="nom" value="{{ old('nom', $plat->nom) }}" required>
        </div>

        <div class="form-group">
            <label>Prix (HTG)</label>
            <input name="prix" value="{{ old('prix', $plat->prix) }}" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3">{{ old('description', $plat->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Catégorie</label>
            <select name="category_id">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $plat->category_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Changer l'image</label>
            @if($plat->image)
                <div class="current-img-box">
                    <img src="{{ asset('images/'.$plat->image) }}" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                    <span style="font-size: 0.9rem; color: #888;">Image actuelle conservée si aucune nouvelle image n'est choisie.</span>
                </div>
            @endif
            <input type="file" name="image" style="border:none; padding-left:0;">
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" name="disponible" value="1" {{ $plat->disponible ? 'checked' : '' }} style="width: 20px; height: 20px;">
            <label style="margin-bottom:0;">Disponible à la vente</label>
        </div>

        <button type="submit" class="btn-update">💾 Enregistrer les modifications</button>
    </form>
</div>

@endsection