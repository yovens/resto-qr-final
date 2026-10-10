@extends('client.layouts.app')

@section('title', $plat->nom)
@section('with-cart', true)

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $item = fn ($p) => [
        'id'    => $p->id,
        'nom'   => $p->nom,
        'desc'  => (string) $p->description,
        'prix'  => (float) ($p->prix_promo ?: $p->prix),
        'avant' => $p->prix_promo ? (float) $p->prix : null,
        'img'   => $p->image ? asset('images/' . $p->image) : null,
        'temps' => $p->temps_preparation ? (int) $p->temps_preparation : null,
        'cat'   => $p->category->nom ?? '',
        'top'   => (bool) ($p->is_populaire ?? false),
    ];
    $menuData = [$plat->id => $item($plat)];
    foreach ($relatedPlats as $r) { $menuData[$r->id] = $item($r); }
    $d = $menuData[$plat->id];
@endphp

@push('styles')
<style>
    .hero-img { position: relative; background: var(--line); }
    .hero-img img, .hero-img .ph { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; }
    .ph { display: grid; place-items: center; font-size: 56px; font-weight: 800; color: var(--text-3); }
    .back { position: absolute; top: 14px; left: 14px; background: rgba(255, 255, 255, .92); border: 0; }
    .body { padding: 20px 16px 8px; }
    .cat { display: inline-block; font-size: 12.5px; font-weight: 700; color: var(--text-2); background: var(--surface); border: 1px solid var(--line); padding: 3px 10px; border-radius: 12px; margin-bottom: 10px; }
    .body h1 { margin: 0 0 8px; font-size: 26px; font-weight: 800; letter-spacing: -.02em; }
    .body .desc { color: var(--text-2); margin: 0 0 16px; font-size: 15.5px; }
    .facts { display: flex; gap: 8px; flex-wrap: wrap; }
    .fact { display: inline-flex; align-items: center; gap: 6px; height: 30px; padding: 0 10px; border-radius: 15px; background: var(--surface); border: 1px solid var(--line); font-size: 13px; font-weight: 600; color: var(--text-2); }
    .fact .ico { width: 15px; height: 15px; }
    .price-row { display: flex; align-items: baseline; gap: 10px; margin: 18px 0 0; }
    .price-row strong { font-size: 24px; font-weight: 800; }
    .price-row del { color: var(--text-3); }

    .buy { display: flex; gap: 12px; padding: 16px; }
    .qty-big { display: inline-flex; align-items: center; height: 50px; border-radius: 12px; border: 1px solid var(--line); background: var(--surface); }
    .qty-big button { width: 46px; height: 48px; border: 0; background: none; cursor: pointer; display: grid; place-items: center; }
    .qty-big span { min-width: 24px; text-align: center; font-weight: 800; font-size: 16px; }
    .buy .btn { flex: 1; height: 50px; }

    .related { padding: 12px 0 24px; }
    .related h2 { margin: 0 16px 12px; font-size: 17px; font-weight: 800; }
    .rel-list { display: flex; gap: 12px; overflow-x: auto; padding: 0 16px 6px; scrollbar-width: none; }
    .rel-list::-webkit-scrollbar { display: none; }
    .rel { flex: 0 0 150px; background: var(--surface); border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }
    .rel img, .rel .ph { width: 100%; height: 100px; object-fit: cover; font-size: 28px; }
    .rel div { padding: 10px; }
    .rel strong { display: block; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .rel span { font-size: 13px; color: var(--text-2); }
</style>
@endpush

@section('content')

<script>
    window.KY_MENU = @json($menuData, JSON_UNESCAPED_UNICODE);
    function kyPh(img) { const d = document.createElement('div'); d.className = 'ph'; d.textContent = img.dataset.initial || ''; img.replaceWith(d); }
</script>

<div class="wrap">
    <div class="hero-img">
        @if($d['img'])
            <img src="{{ $d['img'] }}" alt="{{ $plat->nom }}" data-initial="{{ mb_strtoupper(mb_substr($plat->nom, 0, 1)) }}" onerror="kyPh(this)">
        @else
            <div class="ph">{{ mb_strtoupper(mb_substr($plat->nom, 0, 1)) }}</div>
        @endif
        <a href="/menu/{{ $tableId }}" class="icon-btn back" aria-label="Retounen nan meni an"><svg class="ico"><use href="#i-back"/></svg></a>
    </div>

    <div class="body">
        @if($d['cat'])<span class="cat">{{ $d['cat'] }}</span>@endif
        <h1>{{ $plat->nom }}</h1>
        @if($plat->description)<p class="desc">{{ $plat->description }}</p>@endif
        <div class="facts">
            @if($d['temps'])<span class="fact"><svg class="ico"><use href="#i-clock"/></svg> Anviwon {{ $d['temps'] }} min</span>@endif
            @if($d['top'])<span class="fact">Youn nan sa moun pi renmen</span>@endif
        </div>
        <div class="price-row">
            <strong class="num">{{ $fmt($d['prix']) }} HTG</strong>
            @if($d['avant'])<del class="num">{{ $fmt($d['avant']) }} HTG</del>@endif
        </div>
    </div>

    <div class="buy">
        <div class="qty-big">
            <button type="button" id="dMinus" aria-label="Retire youn"><svg class="ico"><use href="#i-minus"/></svg></button>
            <span class="num" id="dQty">1</span>
            <button type="button" id="dPlus" aria-label="Ajoute youn"><svg class="ico"><use href="#i-plus"/></svg></button>
        </div>
        <button type="button" class="btn btn-primary" id="dAdd">Ajoute nan panye</button>
    </div>

    @if($relatedPlats->count())
        <section class="related">
            <h2>Ou ka renmen tou</h2>
            <div class="rel-list">
                @foreach($relatedPlats as $r)
                    <a class="rel" href="{{ route('client.plat.show', ['tableId' => $tableId, 'id' => $r->id]) }}">
                        @if($r->image)
                            <img src="{{ asset('images/' . $r->image) }}" alt="" loading="lazy" data-initial="{{ mb_strtoupper(mb_substr($r->nom, 0, 1)) }}" onerror="kyPh(this)">
                        @else
                            <span class="ph" style="display:grid">{{ mb_strtoupper(mb_substr($r->nom, 0, 1)) }}</span>
                        @endif
                        <div>
                            <strong>{{ $r->nom }}</strong>
                            <span class="num">{{ $fmt($r->prix_promo ?: $r->prix) }} HTG</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
    const id = @json((string) $plat->id);
    const p = KY.menu[id];
    let q = 1;
    const $ = (x) => document.getElementById(x);
    const sync = () => { $('dQty').textContent = q; $('dAdd').textContent = 'Ajoute · ' + KY.money(p.prix * q); };
    $('dMinus').onclick = () => { q = Math.max(1, q - 1); sync(); };
    $('dPlus').onclick  = () => { q = Math.min(20, q + 1); sync(); };
    $('dAdd').onclick   = () => { KY.add(id, q); q = 1; sync(); };
    sync();
})();
</script>
@endpush