@extends('client.layouts.app')

@section('title', $plat->nom)

@section('content')

<div style="position:relative;">
    <div style="height:320px;position:relative;overflow:hidden;">
        <img src="{{ $plat->image ? asset('images/'.$plat->image) : asset('images/default-food.jpg') }}" 
             style="width:100%;height:100%;object-fit:cover;" alt="{{ $plat->nom }}">
        <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(30,58,95,.8) 100%);"></div>
        <a href="/menu/{{ $tableId }}" style="position:absolute;top:20px;left:20px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;font-size:18px;border:1px solid rgba(255,255,255,.2);">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        @if($plat->is_populaire)
        <span style="position:absolute;top:20px;right:20px;background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));color:#fff;padding:8px 16px;border-radius:20px;font-size:12px;font-weight:700;">🔥 Chokola</span>
        @endif
    </div>
    
    <div style="background:var(--blan);border-radius:30px 30px 0 0;margin-top:-30px;position:relative;padding:30px 25px 100px;">
        <div style="display:inline-block;background:var(--sable);color:var(--terre);padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;margin-bottom:15px;">
            {{ $plat->category->nom }}
        </div>
        <h1 style="font-family:var(--font-bistro);font-size:28px;color:var(--kreyòl);margin-bottom:12px;">{{ $plat->nom }}</h1>
        <p style="color:var(--terre);font-size:15px;line-height:1.7;margin-bottom:25px;">{{ $plat->description }}</p>
        
        <div style="display:flex;gap:20px;margin-bottom:30px;">
            <div style="flex:1;background:var(--sable);padding:16px;border-radius:16px;text-align:center;">
                <div style="font-size:20px;margin-bottom:4px;">⏱️</div>
                <div style="font-size:12px;color:var(--terre);">Tan</div>
                <div style="font-family:var(--font-mono);font-weight:700;color:var(--kreyòl);">{{ $plat->temps_preparation ?? 20 }} min</div>
            </div>
            <div style="flex:1;background:var(--sable);padding:16px;border-radius:16px;text-align:center;">
                <div style="font-size:20px;margin-bottom:4px;">🔥</div>
                <div style="font-size:12px;color:var(--terre);">Popilè</div>
                <div style="font-family:var(--font-mono);font-weight:700;color:var(--kreyòl);">Anpil moun</div>
            </div>
        </div>
        
        <div style="display:flex;align-items:center;justify-content:space-between;position:fixed;bottom:80px;left:0;right:0;padding:15px 20px;background:var(--blan);border-top:1px solid var(--sable-fonce);z-index:800;">
            <div>
                @if($plat->prix_promo)
                    <div style="font-size:14px;color:var(--terre);text-decoration:line-through;">{{ number_format($plat->prix,0) }} HTG</div>
                    <div style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--rouge-brik);">{{ number_format($plat->prix_promo,0) }} HTG</div>
                @else
                    <div style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--rouge-brik);">{{ number_format($plat->prix,0) }} HTG</div>
                @endif
            </div>
            <button onclick="addToCart({{ $plat->id }},'{{ addslashes($plat->nom) }}',{{ $plat->prix_promo ?? $plat->prix }},'{{ $plat->image }}')" 
                    style="padding:14px 32px;background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));color:#fff;border:none;border-radius:16px;font-size:16px;font-weight:700;cursor:pointer;box-shadow:0 6px 20px rgba(192,57,43,.3);display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-plus"></i> Ajoute nan panier
            </button>
        </div>
    </div>
</div>

@if($relatedPlats->count())
<section style="padding:0 20px 40px;">
    <h3 style="font-family:var(--font-bistro);font-size:20px;margin-bottom:18px;color:var(--kreyòl);">W ap renmen tou</h3>
    <div style="display:flex;gap:14px;overflow-x:auto;padding-bottom:10px;">
        @foreach($relatedPlats as $item)
        <a href="{{ route('client.plat.show',['tableId'=>$tableId,'id'=>$item->id]) }}" style="min-width:140px;background:var(--blan);border-radius:20px;overflow:hidden;text-decoration:none;box-shadow:0 4px 15px rgba(0,0,0,.06);border:1px solid var(--sable-fonce);">
            <img src="{{ asset('images/'.$item->image) }}" style="width:100%;height:120px;object-fit:cover;" alt="">
            <div style="padding:12px;">
                <h4 style="font-size:13px;color:var(--kreyòl);margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->nom }}</h4>
                <strong style="font-family:var(--font-mono);font-size:14px;color:var(--rouge-brik);">{{ number_format($item->prix,0) }} HTG</strong>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

@endsection