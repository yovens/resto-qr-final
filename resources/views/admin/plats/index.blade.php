@extends('admin.layouts.app')

@section('content')

<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .btn-add { background: #27ae60; color: white; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; transition: 0.3s; }
    .btn-add:hover { background: #219150; transform: translateY(-2px); }
    
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .data-table th { padding: 15px; color: #7f8c8d; text-transform: uppercase; font-size: 0.85rem; }
    .data-table tr { background: white; transition: 0.3s; }
    .data-table td { padding: 15px; border-top: 1px solid #f1f1f1; border-bottom: 1px solid #f1f1f1; }
    .data-table tr td:first-child { border-left: 1px solid #f1f1f1; border-radius: 10px 0 0 10px; }
    .data-table tr td:last-child { border-right: 1px solid #f1f1f1; border-radius: 0 10px 10px 0; }
    .data-table tr:hover { background: #fdfdfd; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

    .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
    .badge-success { background: #e8f5e9; color: #2e7d32; }
    .badge-danger { background: #ffebee; color: #c62828; }
</style>

<div class="page-header">
    <h1>🍔 Gestion des Plats</h1>
    <a href="/admin/plats/create" class="btn-add">➕ Ajouter Plat</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Prix</th>
            <th>Catégorie</th>
            <th>Disponibilité</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($plats as $plat)
        <tr>
            <td style="text-align:center;">
                <img src="{{ $plat->image ? asset('images/'.$plat->image) : asset('images/default.png') }}" 
                     style="width:50px; height:50px; border-radius:10px; object-fit:cover; border: 2px solid #eee;">
            </td>
            <td><strong>{{ $plat->nom }}</strong></td>
            <td>{{ number_format($plat->prix, 2) }} HTG</td>
            <td>{{ $plat->category->nom ?? '—' }}</td>
            <td>
                @if($plat->disponible)
                    <span class="badge badge-success">✔ Disponible</span>
                @else
                    <span class="badge badge-danger">✖ Indisponible</span>
                @endif
            </td>
            <td>
                <a href="/admin/plats/{{ $plat->id }}/edit" style="color: #2980b9; margin-right: 10px; text-decoration: none; font-weight: bold;">✏️ Éditer</a>
                
                <form method="POST" action="/admin/plats/{{ $plat->id }}" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?');">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none; border:none; color: #c0392b; cursor:pointer; font-weight: bold;">🗑 Supprimer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection