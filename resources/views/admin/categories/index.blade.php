@extends('admin.layouts.app')

@section('content')

<style>
    .cat-container { max-width: 600px; margin: 0 auto; }
    
    /* Fòm Ajoute */
    .add-card { 
        background: white; padding: 25px; border-radius: 15px; 
        box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 30px; 
        display: flex; gap: 10px; align-items: center; 
    }
    .input-cat { flex: 1; padding: 12px; border: 2px solid #eee; border-radius: 8px; }
    .btn-add { background: #27ae60; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; }
    
    /* Lis Kategori */
    .cat-item { 
        background: white; padding: 15px 20px; margin-bottom: 10px; 
        border-radius: 10px; display: flex; justify-content: space-between; 
        align-items: center; transition: 0.3s; border: 1px solid #f9f9f9;
    }
    .cat-item:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .btn-del { background: #ff7675; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
</style>

<div class="cat-container">
    <h1 style="margin-bottom:20px;">📂 Gestion Catégories</h1>

    <div class="add-card">
        <form method="POST" action="/admin/categories" style="display:flex; width:100%; gap:10px;">
            @csrf
            <input name="nom" class="input-cat" placeholder="Nom de la nouvelle catégorie" required>
            <button type="submit" class="btn-add">➕ Ajouter</button>
        </form>
    </div>

    @foreach($categories as $cat)
    <div class="cat-item">
        <strong style="font-size: 1.1rem;">{{ $cat->nom }}</strong>
        
        <form method="POST" action="/admin/categories/{{ $cat->id }}" onsubmit="return confirm('Supprimer cette catégorie ?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn-del">🗑 Supprimer</button>
        </form>
    </div>
    @endforeach
</div>

@endsection