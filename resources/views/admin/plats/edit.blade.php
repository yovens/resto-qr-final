@extends('admin.layouts.layout')

@section('title', 'Modifier · '.$plat->nom)

@section('content')

<a href="/admin/plats" class="back-link"><i data-lucide="arrow-left"></i> Menu</a>
<div class="page-head">
    <div>
        <h1>{{ $plat->nom }}</h1>
        <p>Modifié {{ $plat->updated_at?->locale('fr')->diffForHumans() ?? '' }}</p>
    </div>
</div>

@include('admin.plats._form', [
    'action'      => url('/admin/plats/'.$plat->id),
    'method'      => 'PUT',
    'submitLabel' => 'Enregistrer',
])

@endsection