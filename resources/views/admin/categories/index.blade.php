@extends('admin.layouts.layout')

@section('title', 'Catégories')

@section('content')

<div class="page-head">
    <div>
        <h1>Catégories</h1>
        <p>Les sections du menu QR, dans l'ordre où le client les voit.</p>
    </div>
    <div class="page-actions">
        <a href="/admin/plats" class="btn"><i data-lucide="book-open"></i> Voir le menu</a>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif

<div class="card" style="max-width: 760px">
    <form method="POST" action="/admin/categories" class="toolbar" style="gap: 10px">
        @csrf
        <input class="input @error('nom') is-invalid @enderror" name="nom" value="{{ old('nom') }}"
               placeholder="Nouvelle catégorie, ex. Fruits de mer" required style="flex: 1; min-width: 200px">
        <button type="submit" class="btn btn-primary"><i data-lucide="plus"></i> Ajouter</button>
        @error('nom')<div class="field-error" style="flex-basis: 100%; margin: 0">{{ $message }}</div>@enderror
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    @if($categories->first() && isset($categories->first()->plats_count))
                        <th class="right">Plats</th>
                    @endif
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td>
                            <div class="cell-main">
                                <span class="kpi-icon" style="width:32px;height:32px"><i data-lucide="folder"></i></span>
                                <strong>{{ $cat->nom }}</strong>
                            </div>
                        </td>
                        @isset($cat->plats_count)
                            <td class="right num muted">{{ $cat->plats_count }}</td>
                        @endisset
                        <td class="right">
                            <form method="POST" action="/admin/categories/{{ $cat->id }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer la catégorie « {{ addslashes($cat->nom) }} » ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty">Aucune catégorie. Ajoutez la première ci-dessus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection