@php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$ip = "192.168.1.186";
@endphp

@extends('admin.layouts.app')

@section('content')

<style>
.table-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:25px;
}

.card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    text-align:center;
    transition:0.3s;
    border:1px solid #f0f0f0;
}

.card:hover{
    transform:translateY(-5px);
}

.qr-box{
    background:#f8f9fe;
    padding:20px;
    border-radius:15px;
    display:inline-block;
    margin:15px 0;
    border:1px dashed #ddd;
}

.btn-action{
    background:#27ae60;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

.input-num{
    padding:12px;
    width:200px;
    border:2px solid #eee;
    border-radius:10px;
}
</style>

<h1 style="margin-bottom:30px;">
🪑 Gestion des Tables & QR
</h1>

<div style="
background:white;
padding:25px;
border-radius:20px;
margin-bottom:35px;
box-shadow:0 5px 15px rgba(0,0,0,0.05);
">

<form method="POST" action="/admin/tables"
style="display:flex;gap:15px;align-items:center;">

    @csrf

    <input
        type="number"
        name="numero"
        class="input-num"
        placeholder="Numéro table"
        required>

    <button class="btn-action">
        ➕ Ajouter Table
    </button>

</form>
</div>

<div class="table-grid">

@foreach($tables as $table)

@php
$link = "http://".$ip.":8000/menu/".$table->id;
@endphp

<div class="card">

    <h2 style="margin-top:0;color:#2c3e50;">
        🪑 Table {{ $table->numero }}
    </h2>

    <div class="qr-box">
        {!! QrCode::size(180)
            ->margin(2)
            ->generate($link) !!}
    </div>

    <p style="color:#7f8c8d;font-size:0.9rem;">
        Scannez pour accéder au menu
    </p>

    <a href="{{ $link }}"
       target="_blank"
       style="
       display:block;
       margin-top:10px;
       color:#3498db;
       text-decoration:none;
       font-weight:bold;">
       🔗 Voir menu
    </a>

</div>

@endforeach

</div>

@endsection