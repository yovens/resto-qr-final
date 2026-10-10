@extends('admin.layouts.layout')

@section('title', 'Modifier · '.$supplier->nom_entreprise)

@section('content')

<a href="/admin/suppliers/{{ $supplier->id }}" class="back-link"><i data-lucide="arrow-left"></i> {{ $supplier->nom_entreprise }}</a>
<div class="page-head">
    <div>
        <h1>Modifier le fournisseur</h1>
        <p>{{ $supplier->nom_entreprise }}</p>
    </div>
</div>

@include('admin.suppliers._form', [
    'action'      => url('/admin/suppliers/'.$supplier->id),
    'method'      => 'PUT',
    'submitLabel' => 'Enregistrer',
])

@endsection