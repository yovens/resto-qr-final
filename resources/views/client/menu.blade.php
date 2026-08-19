@extends('client.layouts.app')

@section('title', 'Meni - Kay-Y')

@section('content')

<!-- Hero -->
<header class="hero">
    <div class="hero-top">
        <div class="restaurant">
            <div class="restaurant-logo">🇭🇹</div>
            <div>
                <h1>Restaurant Kay-Y</h1>
                <span>Kwizin Ayisyen • Griyo • Pikliz</span>
            </div>
        </div>
   
    </div>
    <div class="hero-middle">
        <div class="table-badge">
            <div>
                <small>Tab ou</small>
                <h2>Tab {{ $tableId }}</h2>
            </div>
            <div class="wifi"><i class="fa-solid fa-wifi"></i></div>
        </div>
        <div class="hero-title">
            <h2>Salut 👋</h2>
            <p>Chwazi nan pi bon pla nou yo pare pou ou.</p>
        </div>
    </div>
    <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchDish" placeholder="Chache yon plat..." onkeyup="filterDishes(this.value)">
    </div>
</header>

<!-- Kòmand ki an kou -->
@if($activeCommande)
<div class="active-order-banner">
    <div>
        <strong>🍽️ Kòmand #{{ $activeCommande->id }} an kou</strong>
        <small>Statu: {{ $activeCommande->statut === 'nouvelle' ? 'Nouvo' : ($activeCommande->statut === 'en_preparation' ? 'Ap kwit' : 'Pare') }}</small>
    </div>
    <a href="/waiting/{{ $tableId }}/{{ $activeCommande->id }}">Swiv →</a>
</div>
@endif

<!-- Kategori -->
<section style="padding:0 20px;margin-bottom:25px;">
    <div style="display:flex;gap:10px;overflow-x:auto;padding-bottom:10px;scrollbar-width:none;">
        <a href="/menu/{{ $tableId }}" class="cat-chip {{ !request('category') ? 'active' : '' }}">
            <span>🔥</span> Tout
        </a>
        @foreach($allCategories as $cat)
        <a href="/menu/{{ $tableId }}?category={{ $cat->id }}" class="cat-chip {{ request('category')==$cat->id ? 'active':'' }}">
            @if(str_contains(strtolower($cat->nom),'griyo')||str_contains(strtolower($cat->nom),'vyann'))🍖
            @elseif(str_contains(strtolower($cat->nom),'pwason'))🐟
            @elseif(str_contains(strtolower($cat->nom),'diri'))🍚
            @elseif(str_contains(strtolower($cat->nom),'bweson'))🥥
            @elseif(str_contains(strtolower($cat->nom),'desè'))🍮
            @else🍲@endif
            {{ $cat->nom }}
        </a>
        @endforeach
    </div>
</section>

<!-- Meni -->
<div style="padding:0 20px;">
@foreach($categories as $cat)
    @if($cat->plats->count())
    <div style="margin-bottom:30px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <h3 style="font-family:var(--font-bistro);font-size:22px;color:var(--kreyòl);">{{ $cat->nom }}</h3>
            <div style="flex:1;height:1px;background:linear-gradient(90deg,var(--lò),transparent);"></div>
            <span style="font-size:12px;color:var(--terre);background:var(--sable-fonce);padding:4px 12px;border-radius:20px;">{{ $cat->plats->count() }} plat</span>
        </div>
        
        <div class="dish-grid">
        @foreach($cat->plats as $plat)
        <article class="dish-card" data-name="{{ strtolower($plat->nom) }}">
            <div class="dish-img">
                <img src="{{ $plat->image ? asset('images/'.$plat->image) : asset('images/default-food.jpg') }}" alt="{{ $plat->nom }}" loading="lazy">
                @if($plat->is_populaire)<span class="dish-badge hot">🔥 Chokola</span>@endif
                @if($plat->prix_promo)<span class="dish-badge promo">PROMO</span>@endif
            </div>
            <div class="dish-body">
                <h4>{{ $plat->nom }}</h4>
                <p>{{ Str::limit($plat->description,60) }}</p>
                <div class="dish-meta">
                    <span>⏱️ {{ $plat->temps_preparation ?? 20 }} min</span>
                    <span>⭐ 4.8</span>
                </div>
                <div class="dish-footer">
                    <div class="dish-price">
                        @if($plat->prix_promo)
                            <del>{{ number_format($plat->prix,0) }}</del>
                            <strong>{{ number_format($plat->prix_promo,0) }} HTG</strong>
                        @else
                            <strong>{{ number_format($plat->prix,0) }} HTG</strong>
                        @endif
                    </div>
                    <div class="dish-actions">
                        <a href="{{ route('client.plat.show',['tableId'=>$tableId,'id'=>$plat->id]) }}" class="btn-detail"><i class="fa-solid fa-eye"></i></a>
                        <button class="btn-add" onclick="addToCart({{ $plat->id }},'{{ addslashes($plat->nom) }}',{{ $plat->prix_promo ?? $plat->prix }},'{{ $plat->image }}')">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </article>
        @endforeach
        </div>
    </div>
    @endif
@endforeach
</div>

<style>
.cat-chip{
    display:inline-flex;align-items:center;gap:8px;padding:10px 18px;
    background:var(--blan);border:1px solid var(--sable-fonce);border-radius:50px;
    text-decoration:none;color:var(--kreyòl);font-size:13px;font-weight:600;
    white-space:nowrap;transition:.3s;box-shadow:0 2px 8px rgba(0,0,0,.04);
}
.cat-chip.active{background:var(--bleu-haiti);color:#fff;border-color:var(--bleu-haiti);box-shadow:0 4px 15px rgba(30,58,95,.2);}
.cat-chip:active{transform:scale(.95);}

.dish-grid{display:grid;grid-template-columns:1fr;gap:16px;}
.dish-card{
    background:var(--blan);border-radius:24px;overflow:hidden;
    box-shadow:0 4px 20px rgba(30,58,95,.06);border:1px solid var(--sable-fonce);
    transition:.3s;display:flex;flex-direction:column;
}
.dish-card:active{transform:scale(.98);}
.dish-img{position:relative;height:180px;overflow:hidden;}
.dish-img img{width:100%;height:100%;object-fit:cover;}
.dish-badge{position:absolute;padding:6px 12px;border-radius:20px;font-size:11px;font-weight:700;}
.dish-badge.hot{top:12px;left:12px;background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));color:#fff;}
.dish-badge.promo{top:12px;right:12px;background:var(--lò);color:var(--kreyòl);}
.dish-body{padding:18px;flex:1;display:flex;flex-direction:column;}
.dish-body h4{font-family:var(--font-bistro);font-size:18px;color:var(--kreyòl);margin-bottom:6px;}
.dish-body p{color:var(--terre);font-size:13px;line-height:1.5;margin-bottom:12px;flex:1;}
.dish-meta{display:flex;gap:14px;margin-bottom:14px;font-size:12px;color:var(--terre);}
.dish-footer{display:flex;justify-content:space-between;align-items:center;}
.dish-price strong{font-family:var(--font-mono);font-size:18px;color:var(--rouge-brik);}
.dish-price del{font-size:13px;color:var(--terre);margin-right:6px;}
.dish-actions{display:flex;gap:10px;}
.btn-detail{
    width:40px;height:40px;border-radius:12px;border:1px solid var(--sable-fonce);
    background:var(--sable);color:var(--kreyòl);display:flex;align-items:center;justify-content:center;
    text-decoration:none;font-size:14px;transition:.2s;
}
.btn-add{
    width:40px;height:40px;border-radius:12px;border:none;
    background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));
    color:#fff;display:flex;align-items:center;justify-content:center;
    font-size:16px;cursor:pointer;box-shadow:0 4px 12px rgba(192,57,43,.25);transition:.2s;
}
.btn-add:active{transform:scale(.9);}

@media(min-width:480px){
    .dish-grid{grid-template-columns:repeat(2,1fr);}
}
</style>

<script>
function filterDishes(q){
    const term = q.toLowerCase();
    document.querySelectorAll('.dish-card').forEach(card=>{
        card.style.display = card.dataset.name.includes(term) ? 'flex' : 'none';
    });
}


</script>
@endsection