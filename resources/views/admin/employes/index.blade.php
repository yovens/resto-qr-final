@extends('admin.layouts.layout')

@section('title', 'Employés')

@php
    $roles = [
        'caissiere' => ['ready',  'Caissière'],
        'serveur'   => ['new',    'Serveur'],
        'serveuse'  => ['new',    'Serveuse'],
        'cuisine'   => ['prep',   'Cuisinier'],
        'cuisinier' => ['prep',   'Cuisinier'],
    ];
    $wa = function ($tel) {
        $d = preg_replace('/\D+/', '', (string) $tel);
        return strlen($d) === 8 ? '509'.$d : $d;
    };
    $isPaginated = $employes instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $total = $isPaginated ? $employes->total() : $employes->count();
@endphp

@push('styles')
<style>
    .emp-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border); }
    .emp-initials { width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; display: grid; place-items: center; background: var(--brand-50); color: var(--brand-600); font-weight: 800; font-size: 14px; }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Employés</h1>
        <p>{{ $total }} {{ $total > 1 ? 'membres' : 'membre' }} du personnel</p>
    </div>
    <div class="page-actions">
        <a href="/admin/employes/create" class="btn btn-primary"><i data-lucide="user-plus"></i> Nouvel employé</a>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif

<div class="card">
    <div class="toolbar">
        <label class="search">
            <i data-lucide="search"></i>
            <input type="search" id="filterText" placeholder="Nom, fonction, téléphone…">
        </label>
        <span class="count" id="filterCount"></span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Fonction</th>
                    <th>Téléphone</th>
                    <th class="right">Salaire mensuel</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody id="empRows">
                @forelse($employes as $emp)
                    @php [$cls, $label] = $roles[$emp->role] ?? ['served', ucfirst((string) $emp->role)]; @endphp
                    <tr data-search="{{ \Illuminate\Support\Str::lower($emp->prenom.' '.$emp->nom.' '.$label.' '.$emp->telephone) }}">
                        <td>
                            <a href="/admin/employes/{{ $emp->id }}" class="cell-main">
                                @if($emp->photo)
                                    <img src="{{ asset($emp->photo) }}" alt="" class="emp-avatar" loading="lazy">
                                @else
                                    <span class="emp-initials">{{ mb_strtoupper(mb_substr($emp->prenom, 0, 1).mb_substr($emp->nom, 0, 1)) }}</span>
                                @endif
                                <div style="min-width:0">
                                    <strong>{{ $emp->prenom }} {{ $emp->nom }}</strong>
                                    @if($emp->email)<small>{{ $emp->email }}</small>@endif
                                </div>
                            </a>
                        </td>
                        <td><span class="status {{ $cls }}">{{ $label }}</span></td>
                        <td class="num">
                            @if($emp->telephone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $emp->telephone) }}">{{ $emp->telephone }}</a>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="right num"><strong>{{ number_format((float) $emp->salaire, 0, ',', ' ') }}</strong> <span class="muted">HTG</span></td>
                        <td class="right">
                            <div class="row-actions">
                                @if($emp->telephone)
                                    <a href="https://wa.me/{{ $wa($emp->telephone) }}" target="_blank" rel="noopener" class="icon-action" title="WhatsApp"><i data-lucide="message-circle"></i></a>
                                @endif
                                <a href="/admin/employes/{{ $emp->id }}/edit" class="icon-action" title="Modifier"><i data-lucide="pencil"></i></a>
                                <form method="POST" action="/admin/employes/{{ $emp->id }}"
                                      onsubmit="return confirm('Supprimer {{ addslashes($emp->prenom.' '.$emp->nom) }} ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            Aucun employé enregistré.
                            <a href="/admin/employes/create" class="link">Ajouter le premier</a>
                        </td>
                    </tr>
                @endforelse
                <tr id="noMatch" hidden><td colspan="5" class="empty">Aucun employé ne correspond sur cette page.</td></tr>
            </tbody>
        </table>
    </div>

    @if($isPaginated && $employes->hasPages())
        <div class="pager">
            <span>{{ $employes->firstItem() }}–{{ $employes->lastItem() }} sur {{ $employes->total() }}</span>
            <div class="pager-links">
                @if($employes->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $employes->previousPageUrl() }}">‹</a>
                @endif
                @foreach($employes->getUrlRange(max(1, $employes->currentPage() - 2), min($employes->lastPage(), $employes->currentPage() + 2)) as $page => $url)
                    @if($page == $employes->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($employes->hasMorePages())
                    <a href="{{ $employes->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
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
    const rows  = [...document.querySelectorAll('#empRows tr[data-search]')];
    const none  = document.getElementById('noMatch');
    text.addEventListener('input', () => {
        const q = text.value.trim().toLowerCase();
        let shown = 0;
        rows.forEach(r => { const ok = !q || r.dataset.search.includes(q); r.hidden = !ok; if (ok) shown++; });
        none.hidden = shown > 0 || rows.length === 0;
        count.textContent = q && rows.length ? shown + ' sur ' + rows.length : '';
    });
})();
</script>
@endpush