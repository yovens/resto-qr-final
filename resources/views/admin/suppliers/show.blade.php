@extends('admin.layouts.layout')

@section('title', $supplier->nom_entreprise)

@php
    $digits = preg_replace('/\D+/', '', (string) $supplier->telephone);
    $wa     = strlen($digits) === 8 ? '509'.$digits : $digits;
    $tel    = preg_replace('/[^\d+]/', '', (string) $supplier->telephone);
    $produits = array_filter(array_map('trim', preg_split('/[,;\/]+/', (string) $supplier->produits_fournis)));
@endphp

@section('content')

<a href="/admin/suppliers" class="back-link"><i data-lucide="arrow-left"></i> Fournisseurs</a>

<div class="card" style="max-width: 760px">
    <div class="profile-head">
        <span class="profile-avatar">{{ mb_strtoupper(mb_substr($supplier->nom_entreprise, 0, 1)) }}</span>
        <div style="min-width:0">
            <h2>{{ $supplier->nom_entreprise }}</h2>
            <p>Fournisseur · ajouté {{ $supplier->created_at?->locale('fr')->isoFormat('LL') }}</p>
        </div>
        <a href="/admin/suppliers/{{ $supplier->id }}/edit" class="btn" style="margin-left:auto"><i data-lucide="pencil"></i> Modifier</a>
    </div>

    <dl class="dl">
        <div>
            <dt><i data-lucide="user"></i> Contact</dt>
            <dd>{{ $supplier->nom_contact }}</dd>
        </div>
        <div>
            <dt><i data-lucide="phone"></i> Téléphone</dt>
            <dd class="num">
                @if($supplier->telephone)<a href="tel:{{ $tel }}">{{ $supplier->telephone }}</a>@else<span class="empty-val">—</span>@endif
            </dd>
        </div>
        <div>
            <dt><i data-lucide="mail"></i> E-mail</dt>
            <dd>
                @if($supplier->email)<a href="mailto:{{ $supplier->email }}" class="link" style="font-size:inherit">{{ $supplier->email }}</a>@else<span class="empty-val">—</span>@endif
            </dd>
        </div>
        <div>
            <dt><i data-lucide="package"></i> Produits</dt>
            <dd>
                @forelse($produits as $p)<span class="tag">{{ $p }}</span>@empty<span class="empty-val">Non précisé</span>@endforelse
            </dd>
        </div>
        <div>
            <dt><i data-lucide="map-pin"></i> Adresse</dt>
            <dd class="{{ $supplier->adresse ? '' : 'empty-val' }}" style="white-space:pre-line">{{ $supplier->adresse ?: 'Non renseignée' }}</dd>
        </div>
    </dl>

    @if($supplier->telephone || $supplier->email)
        <div class="quick-actions">
            @if($supplier->telephone)
                <a href="tel:{{ $tel }}" class="btn"><i data-lucide="phone"></i> Appeler</a>
                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn"><i data-lucide="message-circle"></i> WhatsApp</a>
            @endif
            @if($supplier->email)
                <a href="mailto:{{ $supplier->email }}" class="btn"><i data-lucide="mail"></i> E-mail</a>
            @endif
        </div>
    @endif
</div>

@endsection