@extends('admin.layouts.layout')

@section('title', 'Nouveau plat')

@section('content')

<a href="/admin/plats" class="back-link"><i data-lucide="arrow-left"></i> Menu</a>
<div class="page-head">
    <div>
        <h1>Nouveau plat</h1>
        <p>Il apparaîtra sur le menu QR dès l'enregistrement s'il est disponible.</p>
    </div>
</div>

@include('admin.plats._form', [
    'plat'        => null,
    'action'      => route('plats.store'),
    'method'      => 'POST',
    'submitLabel' => 'Ajouter au menu',
])

@endsection