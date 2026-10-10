@extends('admin.layouts.layout')

@section('title', 'Stock')

@php
    $fmtQ = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', ' '), '0'), ',');
    $basCount = $products->filter(fn ($p) => $p->quantite_actuelle <= $p->seuil_alerte)->count();
@endphp

@section('content')

<div class="page-head">
    <div>
        <h1>Stock</h1>
        <p>{{ $products->count() }} articles suivis · {{ $basCount }} en stock bas</p>
    </div>
    <div class="page-actions">
        <a href="/admin/stock-mouvement" class="btn"><i data-lucide="arrow-left-right"></i> Entrée / sortie</a>
        <a href="/admin/stock/create" class="btn btn-primary"><i data-lucide="plus"></i> Nouvel article</a>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif

@if(isset($alertes) && $alertes->count() > 0)
    <div class="alert-box">
        <div class="alert-box-head">
            <i data-lucide="alert-triangle"></i>
            {{ $alertes->count() }} {{ $alertes->count() > 1 ? 'articles à réapprovisionner' : 'article à réapprovisionner' }}
        </div>
        <div class="alert-chips">
            @foreach($alertes as $alt)
                <span class="alert-chip">
                    {{ $alt->nom }} · <b class="num">{{ $fmtQ($alt->quantite_actuelle) }} {{ $alt->unite }}</b>
                    <a href="/admin/stock-mouvement?product={{ $alt->id }}&type=entrant">+ Entrée</a>
                </span>
            @endforeach
        </div>
    </div>
@endif

<div class="card">
    <div class="toolbar">
        <label class="search">
            <i data-lucide="search"></i>
            <input type="search" id="filterText" placeholder="Rechercher un article…">
        </label>
        <select class="select" id="filterState">
            <option value="">Tous les niveaux</option>
            <option value="low">Stock bas</option>
            <option value="ok">Stock normal</option>
        </select>
        <span class="count" id="filterCount"></span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Niveau</th>
                    <th class="right">Seuil d'alerte</th>
                    <th>Statut</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody id="stockRows">
                @forelse($products as $prod)
                    @php
                        $low   = $prod->quantite_actuelle <= $prod->seuil_alerte;
                        $warn  = !$low && $prod->quantite_actuelle <= $prod->seuil_alerte * 1.5;
                        // La barre est pleine à 3× le seuil
                        $ref   = max($prod->seuil_alerte * 3, 1);
                        $pct   = max(0, min(100, $prod->quantite_actuelle / $ref * 100));
                    @endphp
                    <tr data-name="{{ \Illuminate\Support\Str::lower($prod->nom) }}" data-state="{{ $low ? 'low' : 'ok' }}">
                        <td>
                            <strong>{{ $prod->nom }}</strong>
                            <div class="muted" style="font-size:12.5px">en {{ $prod->unite }}</div>
                        </td>
                        <td>
                            <div class="level {{ $low ? 'low' : ($warn ? 'warn' : '') }}">
                                <span class="level-qty num">{{ $fmtQ($prod->quantite_actuelle) }} <span class="muted" style="font-weight:600">{{ $prod->unite }}</span></span>
                                <span class="level-bar"><span style="width: {{ $pct }}%"></span></span>
                            </div>
                        </td>
                        <td class="right num muted">{{ $fmtQ($prod->seuil_alerte) }} {{ $prod->unite }}</td>
                        <td>
                            @if($low)
                                <span class="status low">Stock bas</span>
                            @elseif($warn)
                                <span class="status warn">Bientôt bas</span>
                            @else
                                <span class="status ready">Normal</span>
                            @endif
                        </td>
                        <td class="right">
                            <div class="row-actions">
                                <a href="/admin/stock-mouvement?product={{ $prod->id }}" class="icon-action" title="Entrée / sortie"><i data-lucide="arrow-left-right"></i></a>
                                <a href="/admin/stock/{{ $prod->id }}/edit" class="icon-action" title="Modifier"><i data-lucide="pencil"></i></a>
                                <form method="POST" action="/admin/stock/{{ $prod->id }}"
                                      onsubmit="return confirm('Supprimer « {{ addslashes($prod->nom) }} » du stock ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            Aucun article en stock.
                            <a href="/admin/stock/create" class="link">Ajouter le premier article</a>
                        </td>
                    </tr>
                @endforelse
                <tr id="noMatch" hidden><td colspan="5" class="empty">Aucun article ne correspond.</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const text  = document.getElementById('filterText');
    const state = document.getElementById('filterState');
    const count = document.getElementById('filterCount');
    const rows  = [...document.querySelectorAll('#stockRows tr[data-name]')];
    const none  = document.getElementById('noMatch');

    function apply() {
        const q = text.value.trim().toLowerCase();
        let shown = 0;
        rows.forEach(r => {
            const ok = (!q || r.dataset.name.includes(q)) && (!state.value || r.dataset.state === state.value);
            r.hidden = !ok;
            if (ok) shown++;
        });
        none.hidden = shown > 0 || rows.length === 0;
        count.textContent = rows.length ? shown + ' sur ' + rows.length : '';
    }
    [text, state].forEach(i => i.addEventListener('input', apply));
    apply();
})();
</script>
@endpush