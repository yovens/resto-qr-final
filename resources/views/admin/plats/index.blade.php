@extends('admin.layouts.layout')

@section('title', 'Menu')

@section('content')

<div class="page-head">
    <div>
        <h1>Menu</h1>
        <p>{{ $plats->count() }} plats · {{ $plats->where('disponible', true)->count() }} disponibles</p>
    </div>
    <div class="page-actions">
        <a href="/admin/categories" class="btn"><i data-lucide="folder"></i> Catégories</a>
        <a href="/admin/plats/create" class="btn btn-primary"><i data-lucide="plus"></i> Nouveau plat</a>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif

<div class="card">
    <div class="toolbar">
        <label class="search">
            <i data-lucide="search"></i>
            <input type="search" id="filterText" placeholder="Rechercher un plat…">
        </label>
        <select class="select" id="filterCat">
            <option value="">Toutes les catégories</option>
            @foreach($plats->pluck('category')->filter()->unique('id')->sortBy('nom') as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
            @endforeach
        </select>
        <select class="select" id="filterDispo">
            <option value="">Tous les statuts</option>
            <option value="1">Disponibles</option>
            <option value="0">Indisponibles</option>
        </select>
        <span class="count" id="filterCount"></span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Plat</th>
                    <th>Catégorie</th>
                    <th class="right">Prix</th>
                    <th>Statut</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody id="platRows">
                @forelse($plats as $plat)
                    <tr data-name="{{ \Illuminate\Support\Str::lower($plat->nom) }}"
                        data-cat="{{ $plat->category_id }}"
                        data-dispo="{{ $plat->disponible ? 1 : 0 }}">
                        <td>
                            <div class="cell-main">
                                @if($plat->image)
                                    <img class="thumb" src="{{ asset('images/'.$plat->image) }}" alt="" loading="lazy">
                                @else
                                    <span class="thumb" style="display:grid;place-items:center;color:var(--text-3)"><i data-lucide="image-off"></i></span>
                                @endif
                                <div style="min-width:0">
                                    <strong>{{ $plat->nom }}</strong>
                                    @if($plat->description)<small>{{ $plat->description }}</small>@endif
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $plat->category->nom ?? '—' }}</td>
                        <td class="right num"><strong>{{ number_format($plat->prix, 0, ',', ' ') }}</strong> <span class="muted">HTG</span></td>
                        <td>
                            @if($plat->disponible)
                                <span class="status ready">Disponible</span>
                            @else
                                <span class="status served">Indisponible</span>
                            @endif
                        </td>
                        <td class="right">
                            <div class="row-actions">
                                <a href="/admin/plats/{{ $plat->id }}/edit" class="icon-action" title="Modifier"><i data-lucide="pencil"></i></a>
                                <form method="POST" action="/admin/plats/{{ $plat->id }}"
                                      onsubmit="return confirm('Supprimer « {{ addslashes($plat->nom) }} » du menu ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            Aucun plat pour le moment.
                            <a href="/admin/plats/create" class="link">Ajouter le premier plat</a>
                        </td>
                    </tr>
                @endforelse
                <tr id="noMatch" hidden><td colspan="5" class="empty">Aucun plat ne correspond à la recherche.</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const text  = document.getElementById('filterText');
    const cat   = document.getElementById('filterCat');
    const dispo = document.getElementById('filterDispo');
    const count = document.getElementById('filterCount');
    const rows  = [...document.querySelectorAll('#platRows tr[data-name]')];
    const none  = document.getElementById('noMatch');

    function apply() {
        const q = text.value.trim().toLowerCase();
        let shown = 0;
        rows.forEach(r => {
            const ok = (!q || r.dataset.name.includes(q))
                && (!cat.value || r.dataset.cat === cat.value)
                && (!dispo.value || r.dataset.dispo === dispo.value);
            r.hidden = !ok;
            if (ok) shown++;
        });
        none.hidden = shown > 0 || rows.length === 0;
        count.textContent = rows.length ? shown + ' sur ' + rows.length : '';
    }

    [text, cat, dispo].forEach(i => i.addEventListener('input', apply));
    apply();
})();
</script>
@endpush