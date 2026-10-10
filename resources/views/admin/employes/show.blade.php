@extends('admin.layouts.layout')

@section('title', $employe->prenom.' '.$employe->nom)

@php
    $roles = ['caissiere' => 'Caissière', 'serveur' => 'Serveur', 'serveuse' => 'Serveuse', 'cuisine' => 'Cuisinier', 'cuisinier' => 'Cuisinier'];
    $roleLabel = $roles[$employe->role] ?? ucfirst((string) $employe->role);
    $digits = preg_replace('/\D+/', '', (string) $employe->telephone);
    $wa     = strlen($digits) === 8 ? '509'.$digits : $digits;
    $tel    = preg_replace('/[^\d+]/', '', (string) $employe->telephone);
@endphp

@push('styles')
<style>
    .emp-photo { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border); flex-shrink: 0; }
    .emp-head .profile-avatar { width: 64px; height: 64px; border-radius: 50%; font-size: 22px; }
</style>
@endpush

@section('content')

<a href="/admin/employes" class="back-link"><i data-lucide="arrow-left"></i> Employés</a>

<div class="card" style="max-width: 760px">
    <div class="profile-head emp-head">
        @if($employe->photo)
            <img src="{{ asset($employe->photo) }}" alt="" class="emp-photo">
        @else
            <span class="profile-avatar">{{ mb_strtoupper(mb_substr($employe->prenom, 0, 1).mb_substr($employe->nom, 0, 1)) }}</span>
        @endif
        <div style="min-width:0">
            <h2>{{ $employe->prenom }} {{ $employe->nom }}</h2>
            <p>{{ $roleLabel }}</p>
        </div>
        <a href="/admin/employes/{{ $employe->id }}/edit" class="btn" style="margin-left:auto"><i data-lucide="pencil"></i> Modifier</a>
    </div>

    <dl class="dl">
        <div>
            <dt><i data-lucide="briefcase"></i> Fonction</dt>
            <dd>{{ $roleLabel }}</dd>
        </div>
        <div>
            <dt><i data-lucide="phone"></i> Téléphone</dt>
            <dd class="num">
                @if($employe->telephone)<a href="tel:{{ $tel }}">{{ $employe->telephone }}</a>@else<span class="empty-val">—</span>@endif
            </dd>
        </div>
        <div>
            <dt><i data-lucide="mail"></i> E-mail</dt>
            <dd>
                @if($employe->email)<a href="mailto:{{ $employe->email }}" class="link" style="font-size:inherit">{{ $employe->email }}</a>@else<span class="empty-val">—</span>@endif
            </dd>
        </div>
        <div>
            <dt><i data-lucide="banknote"></i> Salaire mensuel</dt>
            <dd class="num">{{ number_format((float) $employe->salaire, 0, ',', ' ') }} HTG</dd>
        </div>
        <div>
            <dt><i data-lucide="calendar"></i> Ajouté le</dt>
            <dd>{{ $employe->created_at?->locale('fr')->isoFormat('LL') }}</dd>
        </div>
    </dl>

    <div class="quick-actions">
        @if($employe->telephone)
            <a href="tel:{{ $tel }}" class="btn"><i data-lucide="phone"></i> Appeler</a>
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn"><i data-lucide="message-circle"></i> WhatsApp</a>
        @endif
        <form method="POST" action="{{ url('/admin/employes/'.$employe->id) }}" style="margin-left:auto"
              onsubmit="return confirm('Supprimer {{ addslashes($employe->prenom.' '.$employe->nom) }} ? Cette action est définitive.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn" style="color: var(--danger)"><i data-lucide="trash-2"></i> Supprimer</button>
        </form>
    </div>
</div>

@endsection