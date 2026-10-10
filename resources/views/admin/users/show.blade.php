@extends('admin.layouts.layout')

@section('title', $user->name)

@php
    $roles = [
        'admin'     => ['Administrateur', 'Accès complet : menu, stock, finances, employés et comptes.'],
        'caissier'  => ['Caissier',       'Encaisse les commandes et consulte les ventes.'],
        'cuisinier' => ['Cuisinier',      "Accède à l'écran cuisine et met à jour les commandes."],
        'serveur'   => ['Serveur',        'Suit les commandes des tables et les sert.'],
    ];
    [$roleLabel, $roleDesc] = $roles[$user->role] ?? [ucfirst((string) $user->role), ''];
    $isMe = $user->id == auth()->id();
@endphp

@section('content')

<a href="/admin/users" class="back-link"><i data-lucide="arrow-left"></i> Utilisateurs</a>

<div class="card" style="max-width: 760px">
    <div class="profile-head">
        <span class="profile-avatar" style="border-radius:50%">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        <div style="min-width:0">
            <h2>{{ $user->name }}</h2>
            <p>{{ $roleLabel }}@if($isMe) · c'est votre compte @endif</p>
        </div>
        <a href="/admin/users/{{ $user->id }}/edit" class="btn" style="margin-left:auto"><i data-lucide="pencil"></i> Modifier</a>
    </div>

    <dl class="dl">
        <div>
            <dt><i data-lucide="shield-check"></i> Rôle</dt>
            <dd>
                {{ $roleLabel }}
                @if($roleDesc)<div class="muted" style="font-weight:500;font-size:13px;margin-top:2px">{{ $roleDesc }}</div>@endif
            </dd>
        </div>
        <div>
            <dt><i data-lucide="mail"></i> E-mail</dt>
            <dd><a href="mailto:{{ $user->email }}" class="link" style="font-size:inherit">{{ $user->email }}</a></dd>
        </div>
        <div>
            <dt><i data-lucide="phone"></i> Téléphone</dt>
            <dd class="num {{ $user->telephone ? '' : 'empty-val' }}">
                @if($user->telephone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $user->telephone) }}">{{ $user->telephone }}</a>@else Non renseigné @endif
            </dd>
        </div>
        <div>
            <dt><i data-lucide="calendar"></i> Compte créé</dt>
            <dd>{{ $user->created_at?->locale('fr')->isoFormat('LL [à] HH:mm') }}</dd>
        </div>
    </dl>

    @unless($isMe)
        <div class="quick-actions">
            <form method="POST" action="/admin/users/{{ $user->id }}" style="margin-left:auto"
                  onsubmit="return confirm('Supprimer le compte de {{ addslashes($user->name) }} ? Cette personne ne pourra plus se connecter.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn" style="color: var(--danger)"><i data-lucide="trash-2"></i> Supprimer le compte</button>
            </form>
        </div>
    @endunless
</div>

@endsection