@extends('client.layouts.app')

@section('title', 'Meni')
@section('with-cart', true)

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $tableNum = $table->numero ?? $tableId;

    // Catégories qui ont au moins un plat disponible
    $cats = collect($allCategories ?? $categories)->filter(fn ($c) => $c->plats->count() > 0)->values();

    // Données des plats pour le panier et la fiche (prix effectif = promo si présente)
    $menuData = [];
    foreach ($cats as $cat) {
        foreach ($cat->plats as $p) {
            $menuData[$p->id] = [
                'id'    => $p->id,
                'nom'   => $p->nom,
                'desc'  => (string) $p->description,
                'prix'  => (float) ($p->prix_promo ?: $p->prix),
                'avant' => $p->prix_promo ? (float) $p->prix : null,
                'img'   => $p->image ? asset('images/' . $p->image) : null,
                'temps' => $p->temps_preparation ? (int) $p->temps_preparation : null,
                'cat'   => $cat->nom,
                'top'   => (bool) ($p->is_populaire ?? false),
            ];
        }
    }

    $heure = (int) now()->format('G');
    $salut = $heure < 12 ? 'Bonjou' : 'Bonswa';

    $statutLabels = [
        'nouvelle' => 'Kwizin nan resevwa l', 'acceptee' => 'Y ap prepare l', 'en_preparation' => 'Y ap prepare l',
        'prete' => 'Li pare !', 'servie' => 'Sèvi · bon apeti',
    ];
@endphp

@push('styles')
<style>
    .top {
        background: var(--dark); color: #fff; padding: 18px 16px 22px;
    }
    .top-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; }
    .brand-mark { width: 34px; height: 34px; border-radius: 9px; background: var(--brand); display: grid; place-items: center; font-size: 13px; }
    .table-pill { padding: 6px 12px; border-radius: 16px; background: rgba(255, 255, 255, .1); font-weight: 700; font-size: 13.5px; }
    .top h1 { margin: 18px 0 2px; font-size: 24px; font-weight: 800; letter-spacing: -.02em; }
    .top p { margin: 0; color: rgba(255, 255, 255, .7); font-size: 14.5px; }

    .live-order {
        display: flex; align-items: center; gap: 12px; margin: 14px 16px 0; padding: 12px 14px;
        background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
    }
    .live-order .dot { width: 10px; height: 10px; border-radius: 50%; background: var(--warn); flex-shrink: 0; box-shadow: 0 0 0 4px var(--warn-50); }
    .live-order.ready .dot { background: var(--ready); box-shadow: 0 0 0 4px var(--ready-50); }
    .live-order strong { display: block; font-size: 14px; }
    .live-order span { color: var(--text-2); font-size: 13px; }
    .live-order .go { margin-left: auto; display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 13.5px; color: var(--brand-600); white-space: nowrap; }
    .live-order .go .ico { width: 16px; height: 16px; }

    /* Barre collante : recherche + catégories */
    .sticky { position: sticky; top: 0; z-index: 20; background: var(--bg); padding: 12px 0 0; border-bottom: 1px solid transparent; transition: border-color .2s, box-shadow .2s; }
    .sticky.stuck { border-bottom-color: var(--line); box-shadow: 0 4px 12px rgba(0, 0, 0, .04); }
    .search { display: flex; align-items: center; gap: 10px; margin: 0 16px; height: 44px; padding: 0 14px; border-radius: 12px; background: var(--surface); border: 1px solid var(--line); color: var(--text-3); }
    .search:focus-within { border-color: var(--brand); }
    .search input { flex: 1; border: 0; outline: 0; background: none; font: inherit; color: var(--text); min-width: 0; }
    .search button { border: 0; background: none; padding: 4px; color: var(--text-3); cursor: pointer; display: grid; }
    .chips { display: flex; gap: 8px; overflow-x: auto; padding: 12px 16px; scrollbar-width: none; scroll-behavior: smooth; }
    .chips::-webkit-scrollbar { display: none; }
    .chip { flex-shrink: 0; height: 36px; padding: 0 14px; border-radius: 18px; border: 1px solid var(--line); background: var(--surface); font-weight: 700; font-size: 13.5px; color: var(--text-2); cursor: pointer; white-space: nowrap; }
    .chip.on { background: var(--dark); border-color: var(--dark); color: #fff; }

    /* Liste */
    .section { padding: 8px 16px 4px; scroll-margin-top: 120px; }
    .section h2 { margin: 12px 0 4px; font-size: 18px; font-weight: 800; letter-spacing: -.01em; }
    .dish { display: flex; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--line); cursor: pointer; }
    .section .dish:last-child { border-bottom: 0; }
    .dish-txt { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .dish-txt h3 { margin: 0 0 4px; font-size: 15.5px; font-weight: 700; }
    .dish-txt p { margin: 0 0 8px; color: var(--text-2); font-size: 13.5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .dish-meta { margin-top: auto; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .price { font-weight: 800; font-size: 15px; }
    .price-old { color: var(--text-3); text-decoration: line-through; font-size: 13px; }
    .tag { font-size: 11.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; background: var(--warn-50); color: var(--warn); }
    .tag.promo { background: var(--brand-50); color: var(--brand-600); }
    .dish-pic { position: relative; width: 104px; height: 104px; flex-shrink: 0; }
    .dish-pic img, .dish-pic .ph { width: 100%; height: 100%; border-radius: 12px; object-fit: cover; background: var(--line); }
    .ph { display: grid; place-items: center; font-size: 30px; font-weight: 800; color: var(--text-3); }
    .dish-pic .add-btn, .dish-pic .stepper { position: absolute; right: -6px; bottom: -6px; border: 3px solid var(--bg); }
    .dish-pic .stepper { height: 40px; }
    .no-pic .dish-pic { width: auto; height: auto; align-self: flex-end; }
    .no-pic .dish-pic .add-btn, .no-pic .dish-pic .stepper { position: static; border: 0; }

    .no-result { text-align: center; color: var(--text-2); padding: 40px 16px; }
    .menu-foot { text-align: center; color: var(--text-3); font-size: 12.5px; padding: 28px 16px 16px; }

    /* Fiche plat */
    .detail-img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; background: var(--line); border-radius: 0; }
    .detail .sheet-body { padding-top: 16px; }
    .detail h2 { margin: 0 0 6px; font-size: 22px; font-weight: 800; letter-spacing: -.01em; }
    .detail .desc { color: var(--text-2); margin: 0 0 14px; }
    .detail .facts { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
    .fact { display: inline-flex; align-items: center; gap: 6px; height: 30px; padding: 0 10px; border-radius: 15px; background: var(--bg); font-size: 13px; font-weight: 600; color: var(--text-2); }
    .fact .ico { width: 15px; height: 15px; }
    .detail .sheet-foot { display: flex; gap: 12px; align-items: center; }
    .qty-big { display: inline-flex; align-items: center; height: 48px; border-radius: 12px; border: 1px solid var(--line); }
    .qty-big button { width: 46px; height: 46px; border: 0; background: none; cursor: pointer; display: grid; place-items: center; }
    .qty-big span { min-width: 24px; text-align: center; font-weight: 800; font-size: 16px; }
    .detail-close { position: absolute; top: 12px; right: 12px; background: rgba(255, 255, 255, .92); border: 0; }
</style>
@endpush

@section('content')

{{-- Données du menu pour le panier (avant le script du layout) --}}
<script>
    window.KY_MENU = @json($menuData, JSON_UNESCAPED_UNICODE);
    window.KY_MENU_FULL = true; // menu complet : les plats absents ne sont plus disponibles
    // Image manquante : on affiche l'initiale du plat à la place
    function kyPh(img) { const d = document.createElement('div'); d.className = 'ph'; d.textContent = img.dataset.initial || ''; img.replaceWith(d); }
</script>

<div class="wrap">
    <header class="top">
        <div class="top-row">
            <div class="brand"><span class="brand-mark">KY</span> Resto Kay-Y</div>
            <span class="table-pill">Tab {{ $tableNum }}</span>
        </div>
        <h1>{{ $salut }} !</h1>
        <p>Chwazi sa w anvi manje, n ap voye l dirèk nan kwizin nan.</p>
    </header>

    @if($activeCommande)
        <a href="/waiting/{{ $tableId }}/{{ $activeCommande->id }}" class="live-order {{ in_array($activeCommande->statut, ['prete', 'servie']) ? 'ready' : '' }}" id="liveOrder"
           data-url="{{ url('/menu/' . $tableId . '/commande/' . $activeCommande->id . '/statut') }}">
            <span class="dot"></span>
            <div>
                <strong>Kòmand #{{ $activeCommande->id }}</strong>
                <span id="liveLabel">{{ $statutLabels[$activeCommande->statut] ?? 'An kou' }}</span>
            </div>
            <span class="go">Swiv li <svg class="ico"><use href="#i-arrow"/></svg></span>
        </a>
    @endif

    @if($cats->isEmpty())
        <div class="no-result">Meni an poko disponib. Mande yon sèvè pou ede w.</div>
    @else
        <div class="sticky" id="sticky">
            <label class="search">
                <svg class="ico"><use href="#i-search"/></svg>
                <input type="search" id="q" placeholder="Chèche yon plat…" autocomplete="off" enterkeyhint="search">
                <button type="button" id="qClear" hidden aria-label="Efase"><svg class="ico"><use href="#i-x"/></svg></button>
            </label>
            <nav class="chips" id="chips">
                @foreach($cats as $cat)
                    <button type="button" class="chip {{ $loop->first ? 'on' : '' }}" data-target="cat-{{ $cat->id }}">{{ $cat->nom }}</button>
                @endforeach
            </nav>
        </div>

        <main id="menuList">
            @foreach($cats as $cat)
                <section class="section" id="cat-{{ $cat->id }}" data-cat>
                    <h2>{{ $cat->nom }}</h2>
                    @foreach($cat->plats as $p)
                        @php $d = $menuData[$p->id]; @endphp
                        <article class="dish {{ $d['img'] ? '' : 'no-pic' }}" data-id="{{ $p->id }}" data-search="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($p->nom . ' ' . $p->description)) }}">
                            <div class="dish-txt">
                                <h3>{{ $p->nom }}</h3>
                                @if($p->description)<p>{{ $p->description }}</p>@endif
                                <div class="dish-meta">
                                    <span class="price num">{{ $fmt($d['prix']) }} HTG</span>
                                    @if($d['avant'])<span class="price-old num">{{ $fmt($d['avant']) }}</span><span class="tag promo">Pwomo</span>@endif
                                    @if($d['top'])<span class="tag">Pi renmen</span>@endif
                                </div>
                            </div>
                            <div class="dish-pic">
                                @if($d['img'])
                                    <img src="{{ $d['img'] }}" alt="" loading="lazy" data-initial="{{ mb_strtoupper(mb_substr($p->nom, 0, 1)) }}" onerror="kyPh(this)">
                                @endif
                                <span class="ctrl" data-ctrl="{{ $p->id }}"></span>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endforeach
            <div class="no-result" id="noResult" hidden>Nou pa jwenn plat sa a. Eseye yon lòt mo.</div>
        </main>

        <p class="menu-foot">Pri yo an goud (HTG). Sèvis {{ (int) round(($serviceRate ?? 0.10) * 100) }} % ajoute nan total la.</p>
    @endif
</div>

{{-- Fiche détail d'un plat --}}
<section class="sheet detail" id="detailSheet" role="dialog" aria-modal="true" aria-labelledby="dName">
    <div style="position:relative">
        <img class="detail-img" id="dImg" alt="" hidden>
        <button type="button" class="icon-btn detail-close" onclick="KY.closeSheets()" aria-label="Fèmen"><svg class="ico"><use href="#i-x"/></svg></button>
    </div>
    <div class="sheet-body">
        <h2 id="dName"></h2>
        <p class="desc" id="dDesc"></p>
        <div class="facts" id="dFacts"></div>
    </div>
    <div class="sheet-foot">
        <div class="qty-big">
            <button type="button" id="dMinus" aria-label="Retire youn"><svg class="ico"><use href="#i-minus"/></svg></button>
            <span class="num" id="dQty">1</span>
            <button type="button" id="dPlus" aria-label="Ajoute youn"><svg class="ico"><use href="#i-plus"/></svg></button>
        </div>
        <button type="button" class="btn btn-primary" style="flex:1" id="dAdd">Ajoute</button>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    const menu = KY.menu;

    /* ---------- Boutons + / stepper sur chaque plat ---------- */
    function renderCtrl(id) {
        const slot = document.querySelector('[data-ctrl="' + id + '"]');
        if (!slot) return;
        slot.innerHTML = '';
        const q = KY.qty(String(id));
        if (q > 0) {
            slot.append(KY.stepper(String(id), q));
        } else {
            const b = document.createElement('button');
            b.type = 'button'; b.className = 'add-btn'; b.setAttribute('aria-label', 'Ajoute ' + menu[id].nom);
            b.append(KY.icon('plus'));
            b.onclick = (e) => { e.stopPropagation(); KY.add(String(id)); };
            slot.append(b);
        }
    }
    Object.keys(menu).forEach(renderCtrl);
    document.addEventListener('ky:cart', (e) => { renderCtrl(e.detail.id); if (current === e.detail.id) syncDetail(); });

    /* ---------- Fiche détail ---------- */
    let current = null, dq = 1;
    const $ = (id) => document.getElementById(id);

    function syncDetail() {
        const p = menu[current];
        $('dQty').textContent = dq;
        $('dAdd').textContent = (KY.qty(current) ? 'Ajoute ' : 'Ajoute nan panye · ') + KY.money(p.prix * dq);
    }

    function openDetail(id) {
        const p = menu[id]; if (!p) return;
        current = String(id); dq = 1;
        const img = $('dImg');
        if (p.img) { img.src = p.img; img.hidden = false; img.onerror = () => { img.hidden = true; }; } else { img.hidden = true; }
        $('dName').textContent = p.nom;
        $('dDesc').textContent = p.desc || '';
        const facts = $('dFacts'); facts.innerHTML = '';
        const fact = (icon, txt) => { const f = document.createElement('span'); f.className = 'fact'; f.append(KY.icon(icon), document.createTextNode(txt)); facts.append(f); };
        fact('dish', p.cat);
        if (p.temps) fact('clock', 'Anviwon ' + p.temps + ' min');
        if (KY.qty(current)) fact('bag', KY.qty(current) + ' deja nan panye a');
        syncDetail();
        KY.openSheet('detailSheet');
    }

    $('dMinus').onclick = () => { dq = Math.max(1, dq - 1); syncDetail(); };
    $('dPlus').onclick  = () => { dq = Math.min(20, dq + 1); syncDetail(); };
    $('dAdd').onclick   = () => { KY.add(current, dq); KY.closeSheets(); };

    document.querySelectorAll('.dish').forEach(d => d.addEventListener('click', () => openDetail(d.dataset.id)));

    /* ---------- Catégories : défilement + catégorie active ---------- */
    const chips = [...document.querySelectorAll('.chip')];
    const sections = [...document.querySelectorAll('[data-cat]')];
    const sticky = $('sticky');
    let clicking = false;

    chips.forEach(c => c.addEventListener('click', () => {
        const target = $(c.dataset.target);
        if (!target) return;
        clicking = true;
        setActive(c);
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => { clicking = false; }, 700);
    }));

    function setActive(chip) {
        chips.forEach(x => x.classList.toggle('on', x === chip));
        chip.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    if ('IntersectionObserver' in window && sections.length) {
        const io = new IntersectionObserver((entries) => {
            if (clicking) return;
            entries.forEach(en => {
                if (en.isIntersecting) {
                    const chip = chips.find(c => c.dataset.target === en.target.id);
                    if (chip && !chip.classList.contains('on')) setActive(chip);
                }
            });
        }, { rootMargin: '-130px 0px -65% 0px' });
        sections.forEach(s => io.observe(s));
    }
    if (sticky) {
        new IntersectionObserver(([e]) => sticky.classList.toggle('stuck', e.intersectionRatio < 1), { threshold: [1], rootMargin: '-1px 0px 0px 0px' }).observe(sticky);
    }

    /* ---------- Recherche ---------- */
    const q = $('q'), clear = $('qClear'), noRes = $('noResult');
    const norm = (s) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    function search() {
        const term = norm(q.value.trim());
        clear.hidden = !term;
        let any = false;
        sections.forEach(sec => {
            let shown = 0;
            sec.querySelectorAll('.dish').forEach(d => { const ok = !term || d.dataset.search.includes(term); d.hidden = !ok; if (ok) shown++; });
            sec.hidden = shown === 0;
            if (shown) any = true;
        });
        noRes.hidden = any;
        document.getElementById('chips').hidden = !!term;
    }
    if (q) {
        q.addEventListener('input', search);
        clear.addEventListener('click', () => { q.value = ''; search(); q.focus(); });
    }

    /* ---------- Suivi de la commande en cours ---------- */
    const live = $('liveOrder');
    if (live) {
        const labels = @json($statutLabels);
        async function poll() {
            if (document.visibilityState !== 'visible') return;
            try {
                const r = await fetch(live.dataset.url, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const s = await r.json();
                if (s.termine) { live.remove(); return; }
                $('liveLabel').textContent = labels[s.statut] || s.label || 'An kou';
                live.classList.toggle('ready', ['prete', 'servie'].includes(s.statut));
            } catch (e) {}
        }
        setInterval(poll, 8000);
    }
})();
</script>
@endpush