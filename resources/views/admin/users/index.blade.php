@extends('admin.layouts.layout')

@section('title', 'Utilisateurs')

@php
    $roles = [
        'admin'     => ['role-admin', 'Administrateur'],
        'caissier'  => ['ready',      'Caissier'],
        'cuisinier' => ['prep',       'Cuisinier'],
        'serveur'   => ['new',        'Serveur'],
    ];
    $isPaginated = $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $total = $isPaginated ? $users->total() : $users->count();
@endphp

@push('styles')
<style>
    .status.role-admin { color: #fff; background: var(--text); }
    .you { font-size: 11px; font-weight: 700; color: var(--brand-600); background: var(--brand-50); padding: 1px 6px; border-radius: 4px; margin-left: 6px; }
    .flash.error { background: #fdf1f1; color: #9b2020; border-color: #f3c9c9; }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Utilisateurs & rôles</h1>
        <p>{{ $total }} {{ $total > 1 ? 'comptes ont' : 'compte a' }} accès au système</p>
    </div>
    <div class="page-actions">
        <a href="/admin/users/create" class="btn btn-primary"><i data-lucide="user-plus"></i> Nouvel utilisateur</a>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Téléphone</th>
                    <th>Créé le</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    @php [$cls, $label] = $roles[$u->role] ?? ['served', ucfirst((string) $u->role)]; @endphp
                    <tr>
                        <td>
                            <a href="/admin/users/{{ $u->id }}" class="cell-main">
                                <span class="profile-avatar" style="width:36px;height:36px;font-size:14px;border-radius:50%">
                                    {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                                </span>
                                <div style="min-width:0">
                                    <strong>{{ $u->name }}@if($u->id == auth()->id())<span class="you">vous</span>@endif</strong>
                                    <small>{{ $u->email }}</small>
                                </div>
                            </a>
                        </td>
                        <td><span class="status {{ $cls }}">{{ $label }}</span></td>
                        <td class="num">
                            @if($u->telephone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $u->telephone) }}">{{ $u->telephone }}</a>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="muted num">{{ $u->created_at?->format('d/m/Y') }}</td>
                        <td class="right">
                            <div class="row-actions">
                                <a href="/admin/users/{{ $u->id }}/edit" class="icon-action" title="Modifier"><i data-lucide="pencil"></i></a>
                                @if($u->id != auth()->id())
                                    <form method="POST" action="/admin/users/{{ $u->id }}"
                                          onsubmit="return confirm('Supprimer le compte de {{ addslashes($u->name) }} ? Cette personne ne pourra plus se connecter.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            Aucun utilisateur.
                            <a href="/admin/users/create" class="link">Créer le premier compte</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($isPaginated && $users->hasPages())
        <div class="pager">
            <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} sur {{ $users->total() }}</span>
            <div class="pager-links">
                @if($users->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}">‹</a>
                @endif
                @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection