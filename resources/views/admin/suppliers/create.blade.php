@extends('admin.layouts.layout')

@section('title', 'Nouveau fournisseur')

@section('content')

<a href="/admin/suppliers" class="back-link"><i data-lucide="arrow-left"></i> Fournisseurs</a>
<div class="page-head">
    <div>
        <h1>Nouveau fournisseur</h1>
        <p>Enregistrez un partenaire pour retrouver ses coordonnées rapidement.</p>
    </div>
</div>

@include('admin.suppliers._form', [
    'supplier'    => null,
    'action'      => url('/admin/suppliers'),
    'method'      => 'POST',
    'submitLabel' => 'Enregistrer',
])

@endsection