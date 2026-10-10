@extends('admin.layouts.layout')

@section('title', 'Nouvel article')

@section('content')

<a href="/admin/stock" class="back-link"><i data-lucide="arrow-left"></i> Stock</a>
<div class="page-head">
    <div>
        <h1>Nouvel article</h1>
        <p>Ajoutez un ingrédient ou un produit à suivre.</p>
    </div>
</div>

@include('admin.stock._form', [
    'product'     => null,
    'action'      => url('/admin/stock'),
    'method'      => 'POST',
    'submitLabel' => 'Ajouter au stock',
])

@endsection