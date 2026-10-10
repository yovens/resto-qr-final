@extends('admin.layouts.layout')

@section('title', 'Modifier · '.$product->nom)

@section('content')

<a href="/admin/stock" class="back-link"><i data-lucide="arrow-left"></i> Stock</a>
<div class="page-head">
    <div>
        <h1>{{ $product->nom }}</h1>
        <p>Nom, unité et seuil d'alerte.</p>
    </div>
</div>

@include('admin.stock._form', [
    'action'      => url('/admin/stock/'.$product->id),
    'method'      => 'PUT',
    'submitLabel' => 'Enregistrer',
])

@endsection