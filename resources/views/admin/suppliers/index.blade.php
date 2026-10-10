@extends('admin.layouts.layout')

@section('title', 'Fournisseurs')

@php
    // Numéro WhatsApp : chiffres uniquement, indicatif Haïti (509) ajouté pour les numéros locaux à 8 chiffres
    $wa = function ($tel) {
        $d = preg_replace('/\D+/', '', (string) $tel);
        return strlen($d) === 8 ? '509'.$d : $d;
    };
    $total = method_exists($suppliers, 'total') ? $suppliers->total() : $suppliers->count();
@endphp

@section('content')

<div class="page-head">
    <div>
        <h1>Fournisseurs</h1>
        <p>{{ $total }} {{ $total > 1 ? 'partenaires' : 'partenaire' }} du restaurant</p>
    </div>
    <div class="page-actions">
        <a href="/admin/suppliers/create" class="btn btn-primary"><i data-lucide="plus"></i> Nouveau fournisseur</a>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif

<div class="card">
    <div class="toolbar">
        <label class="search">
            <i data-lucide="search"></i>
            <input type="search" id="filterText" placeholder="Entreprise, contact, produit…">
        </label>
        <span class="count" id="filterCount"></span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Contact</th>
                    <th>Produits</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody id="supRows">
                @forelse($suppliers as $sup)
                    <tr data-search="{{ \Illuminate\Support\Str::lower($sup->nom_entreprise.' '.$sup->nom_contact.' '.$sup->produits_fournis.' '.$sup->telephone) }}">
                        <td>
                            <a href="/admin/suppliers/{{ $sup->id }}" class="cell-main">
                                <span class="profile-avatar" style="width:36px;height:36px;font-size:14px;border-radius:9px">
                                    {{ mb_strtoupper(mb_substr($sup->nom_entreprise, 0, 1)) }}
                                </span>
                                <strong>{{ $sup->nom_entreprise }}</strong>
                            </a>
                        </td>
                        <td>
                            <div style="font-weight:600">{{ $sup->nom_contact }}</div>
                            @if($sup->telephone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $sup->telephone) }}" class="muted num" style="font-size:12.5px">{{ $sup->telephone }}</a>
                            @endif
                        </td>
                        <td style="white-space:normal; max-width:280px">
                            @if($sup->produits_fournis)
                                @foreach(array_filter(array_map('trim', preg_split('/[,;\/]+/', $sup->produits_fournis))) as $p)
                                    <span class="tag">{{ $p }}</span>
                                @endforeach
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="right">
                            <div class="row-actions">
                                @if($sup->telephone)
                                    <a href="https://wa.me/{{ $wa($sup->telephone) }}" target="_blank" rel="noopener" class="icon-action" title="WhatsApp"><i data-lucide="message-circle"></i></a>
                                @endif
                                <a href="/admin/suppliers/{{ $sup->id }}/edit" class="icon-action" title="Modifier"><i data-lucide="pencil"></i></a>
                                <form method="POST" action="/admin/suppliers/{{ $sup->id }}"
                                      onsubmit="return confirm('Supprimer le fournisseur « {{ addslashes($sup->nom_entreprise) }} » ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">
                            Aucun fournisseur enregistré.
                            <a href="/admin/suppliers/create" class="link">Ajouter le premier</a>
                        </td>
                    </tr>
                @endforelse
                <tr id="noMatch" hidden><td colspan="4" class="empty">Aucun fournisseur ne correspond sur cette page.</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($suppliers instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $suppliers->hasPages())
        <div class="pager">
            <span>{{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }} sur {{ $suppliers->total() }}</span>
            <div class="pager-links">
                @if($suppliers->onFirstPage())
                    <span class="disabled" aria-hidden="true">‹</span>
                @else
                    <a href="{{ $suppliers->previousPageUrl() }}" aria-label="Page précédente">‹</a>
                @endif

                @foreach($suppliers->getUrlRange(max(1, $suppliers->currentPage() - 2), min($suppliers->lastPage(), $suppliers->currentPage() + 2)) as $page => $url)
                    @if($page == $suppliers->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($suppliers->hasMorePages())
                    <a href="{{ $suppliers->nextPageUrl() }}" aria-label="Page suivante">›</a>
                @else
                    <span class="disabled" aria-hidden="true">›</span>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
    const text  = document.getElementById('filterText');
    const count = document.getElementById('filterCount');
    const rows  = [...document.querySelectorAll('#supRows tr[data-search]')];
    const none  = document.getElementById('noMatch');
    function apply() {
        const q = text.value.trim().toLowerCase();
        let shown = 0;
        rows.forEach(r => { const ok = !q || r.dataset.search.includes(q); r.hidden = !ok; if (ok) shown++; });
        none.hidden = shown > 0 || rows.length === 0;
        count.textContent = q && rows.length ? shown + ' sur ' + rows.length : '';
    }
    text.addEventListener('input', apply);
})();
</script>
@endpush