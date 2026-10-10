<!DOCTYPE html>
<html lang="ht">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#14231f">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meni') · Resto Kay-Y</title>

    {{--
        IMPORTANT : cette interface fonctionne SANS internet (réseau local).
        Aucune police, icône ou script externe : tout est dans ce fichier.
    --}}
    <style>
        :root {
            --bg: #f6f3ee;
            --surface: #ffffff;
            --line: #ebe5dc;
            --text: #1d1915;
            --text-2: #5e564d;
            --text-3: #968c80;
            --brand: #0f9b8e;
            --brand-600: #0b7f74;
            --brand-50: #e5f4f2;
            --dark: #14231f;
            --ready: #15803d;
            --ready-50: #e7f6ec;
            --warn: #b45309;
            --warn-50: #fdf2e3;
            --danger: #c2410c;
            --radius: 14px;
            --font: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --safe-b: env(safe-area-inset-bottom, 0px);
        }
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body { margin: 0; }
        body {
            font-family: var(--font); font-size: 15px; line-height: 1.45;
            background: var(--bg); color: var(--text);
            -webkit-font-smoothing: antialiased;
        }
        body.has-cartbar { padding-bottom: calc(92px + var(--safe-b)); }
        body.no-scroll { overflow: hidden; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; color: inherit; }
        img { display: block; max-width: 100%; }
        [hidden] { display: none !important; }
        .num { font-variant-numeric: tabular-nums; }
        .ico { width: 20px; height: 20px; flex-shrink: 0; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .wrap { max-width: 640px; margin: 0 auto; }

        /* Boutons */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            height: 48px; padding: 0 18px; border-radius: 12px; border: 0; cursor: pointer;
            font-weight: 700; font-size: 15px; background: var(--surface); border: 1px solid var(--line);
            touch-action: manipulation;
        }
        .btn:active { transform: scale(.98); }
        .btn-primary { background: var(--brand); border-color: var(--brand); color: #fff; }
        .btn-dark { background: var(--dark); border-color: var(--dark); color: #fff; }
        .btn-block { width: 100%; }
        .btn[disabled] { opacity: .6; pointer-events: none; }
        .icon-btn {
            width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--line);
            background: var(--surface); display: grid; place-items: center; cursor: pointer;
        }

        /* Stepper − 1 + */
        .stepper { display: inline-flex; align-items: center; background: var(--brand); color: #fff; border-radius: 22px; height: 36px; }
        .stepper button { width: 36px; height: 36px; border: 0; background: none; color: #fff; display: grid; place-items: center; cursor: pointer; touch-action: manipulation; }
        .stepper button .ico { width: 16px; height: 16px; stroke-width: 2.5; }
        .stepper span { min-width: 18px; text-align: center; font-weight: 800; font-size: 14px; }
        .add-btn {
            width: 36px; height: 36px; border-radius: 50%; border: 0; cursor: pointer;
            background: var(--brand); color: #fff; display: grid; place-items: center;
            box-shadow: 0 2px 6px rgba(15, 155, 142, .3); touch-action: manipulation;
        }
        .add-btn .ico { width: 18px; height: 18px; stroke-width: 2.5; }

        /* Barre panier */
        .cartbar {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 40;
            padding: 12px 16px calc(12px + var(--safe-b));
            background: linear-gradient(to top, var(--bg) 70%, rgba(246, 243, 238, 0));
            transform: translateY(120%); transition: transform .25s ease;
        }
        .cartbar.show { transform: none; }
        .cartbar button {
            width: 100%; max-width: 608px; margin: 0 auto; height: 56px; border: 0; border-radius: 14px; cursor: pointer;
            background: var(--dark); color: #fff; display: flex; align-items: center; gap: 12px; padding: 0 16px;
            font-weight: 700; font-size: 15px; box-shadow: 0 8px 24px rgba(20, 35, 31, .25);
        }
        .cartbar .count { min-width: 28px; height: 28px; border-radius: 14px; background: var(--brand); display: grid; place-items: center; font-size: 13px; padding: 0 8px; }
        .cartbar .total { margin-left: auto; }
        .cartbar.bump button { animation: bump .3s ease; }
        @keyframes bump { 50% { transform: scale(1.03); } }

        /* Feuille (bottom sheet) */
        .backdrop { position: fixed; inset: 0; background: rgba(20, 23, 21, .45); z-index: 50; opacity: 0; pointer-events: none; transition: opacity .2s; }
        .backdrop.show { opacity: 1; pointer-events: auto; }
        .sheet {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 51;
            max-width: 640px; margin: 0 auto; max-height: 92vh; display: flex; flex-direction: column;
            background: var(--surface); border-radius: 20px 20px 0 0;
            transform: translateY(100%); transition: transform .28s cubic-bezier(.2, .8, .2, 1);
        }
        .sheet.show { transform: none; }
        .sheet-grip { width: 40px; height: 4px; border-radius: 2px; background: var(--line); margin: 8px auto 0; flex-shrink: 0; }
        .sheet-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 20px; flex-shrink: 0; }
        .sheet-head h2 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -.01em; }
        .sheet-head p { margin: 2px 0 0; color: var(--text-3); font-size: 13px; }
        .sheet-body { overflow-y: auto; padding: 0 20px 12px; -webkit-overflow-scrolling: touch; }
        .sheet-foot { padding: 12px 20px calc(16px + var(--safe-b)); border-top: 1px solid var(--line); flex-shrink: 0; }

        /* Panier */
        .cart-line { display: grid; grid-template-columns: 1fr auto; gap: 4px 12px; align-items: center; padding: 14px 0; border-bottom: 1px solid var(--line); }
        .cart-line .name { font-weight: 700; }
        .cart-line .price { color: var(--text-2); font-size: 13.5px; }
        .cart-line .stepper { grid-row: span 2; background: var(--bg); color: var(--text); }
        .cart-line .stepper button { color: var(--text); }
        .cart-empty { text-align: center; padding: 36px 0 28px; color: var(--text-2); }
        .cart-empty strong { display: block; color: var(--text); font-size: 16px; margin-bottom: 4px; }
        .note-field { margin-top: 16px; }
        .note-field label { display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px; }
        .note-field textarea {
            width: 100%; min-height: 70px; resize: vertical; padding: 12px; border-radius: 12px;
            border: 1px solid var(--line); background: var(--bg); font: inherit; color: var(--text);
        }
        .note-field textarea:focus { outline: 0; border-color: var(--brand); background: #fff; }
        .totals { margin: 16px 0 4px; font-size: 14px; }
        .totals div { display: flex; justify-content: space-between; padding: 3px 0; color: var(--text-2); }
        .totals .grand { color: var(--text); font-weight: 800; font-size: 18px; padding-top: 10px; margin-top: 6px; border-top: 1px dashed var(--line); }

        /* Toasts */
        .toasts { position: fixed; top: 12px; left: 12px; right: 12px; z-index: 70; display: grid; gap: 8px; justify-items: center; pointer-events: none; }
        .toast {
            max-width: 420px; padding: 11px 16px; border-radius: 12px; background: var(--dark); color: #fff;
            font-weight: 600; font-size: 14px; box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
            animation: toastIn .2s ease-out;
        }
        .toast.error { background: var(--danger); }
        @keyframes toastIn { from { opacity: 0; transform: translateY(-8px); } }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Icônes (SVG intégrés : aucun téléchargement nécessaire) --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="i-minus" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></symbol>
    <symbol id="i-bag" viewBox="0 0 24 24"><path d="M6 7h12l1 13H5L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></symbol>
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12 5 5 9-10"/></symbol>
    <symbol id="i-chef" viewBox="0 0 24 24"><path d="M7 14a4 4 0 1 1 1.5-7.7A4 4 0 0 1 16 5a4 4 0 0 1 1 7.9V14"/><path d="M7 14v6h10v-6M7 17h10"/></symbol>
    <symbol id="i-dish" viewBox="0 0 24 24"><path d="M3 17h18M5 17a7 7 0 0 1 14 0M12 7V5M10 5h4"/></symbol>
    <symbol id="i-receipt" viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h6"/></symbol>
    <symbol id="i-send" viewBox="0 0 24 24"><path d="M4 12 20 4l-6 16-3-7-7-1Z"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
</svg>

@yield('content')

<div class="toasts" id="toasts" aria-live="polite"></div>

@hasSection('with-cart')
    {{-- Barre + feuille panier (pages meni et detay) --}}
    <div class="cartbar" id="cartbar">
        <button type="button" onclick="KY.openCart()">
            <span class="count num" id="cbCount">0</span>
            <span>Wè panye a</span>
            <span class="total num" id="cbTotal">0 HTG</span>
        </button>
    </div>

    <div class="backdrop" id="backdrop" onclick="KY.closeSheets()"></div>

    <section class="sheet" id="cartSheet" role="dialog" aria-modal="true" aria-labelledby="cartTitle">
        <div class="sheet-grip"></div>
        <div class="sheet-head">
            <div>
                <h2 id="cartTitle">Panye ou</h2>
                <p>Tab {{ $table->numero ?? $tableId }}</p>
            </div>
            <button type="button" class="icon-btn" onclick="KY.closeSheets()" aria-label="Fèmen"><svg class="ico"><use href="#i-x"/></svg></button>
        </div>
        <div class="sheet-body">
            <div id="cartLines"></div>
            <div class="note-field" id="noteField">
                <label for="orderNote">Yon mo pou kwizin nan <span style="color:var(--text-3);font-weight:500">(si w vle)</span></label>
                <textarea id="orderNote" maxlength="500" placeholder="Pa egzanp : mwens pike, san zonyon…"></textarea>
            </div>
            <div class="totals" id="cartTotals">
                <div><span>Sou-total</span><span class="num" id="tSub">0 HTG</span></div>
                <div><span>Sèvis ({{ (int) round(($serviceRate ?? 0.10) * 100) }} %)</span><span class="num" id="tService">0 HTG</span></div>
                <div class="grand"><span>Total</span><span class="num" id="tGrand">0 HTG</span></div>
            </div>
        </div>
        <div class="sheet-foot">
            <button type="button" class="btn btn-primary btn-block" id="sendBtn" onclick="KY.sendOrder()">
                <svg class="ico"><use href="#i-send"/></svg> <span>Voye kòmand lan</span>
            </button>
        </div>
    </section>
@endif

<script>
/* ==========================================================
   Noyau commun de l'interface client (hors ligne)
   ========================================================== */
window.KY = (function () {
    const TABLE_ID     = @json((string) ($tableId ?? ''));
    const SERVICE_RATE = @json((float) ($serviceRate ?? 0.10));
    const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
    const CART_KEY     = 'kayy_cart_v3_' + TABLE_ID;

    const menu = window.KY_MENU || {};      // { id: {nom, prix, image, ...} } fourni par la page
    const $ = (id) => document.getElementById(id);

    const money = (n) => Math.round(n).toLocaleString('fr-FR').replace(/ | /g, ' ') + ' HTG';

    /* ---------- Stockage du panier : { platId: {q, nom, prix} } ----------
       Le nom et le prix sont gardés pour l'affichage sur les pages qui ne
       contiennent pas tout le menu. Le serveur recalcule TOUJOURS les prix. */
    const FULL_MENU = window.KY_MENU_FULL === true;
    function load() {
        let raw = {};
        try { raw = JSON.parse(localStorage.getItem(CART_KEY) || '{}'); } catch (e) {}
        const clean = {};
        Object.entries(raw).forEach(([id, it]) => {
            const q = Math.min(Number(it && it.q) || 0, 50);
            if (q <= 0) return;
            if (menu[id]) clean[id] = { q, nom: menu[id].nom, prix: menu[id].prix };       // données fraîches
            else if (!FULL_MENU && it.nom) clean[id] = { q, nom: it.nom, prix: Number(it.prix) || 0 };
            // sur le menu complet, un plat absent n'est plus disponible : on le retire
        });
        return clean;
    }
    let cart = load();
    function save() { try { localStorage.setItem(CART_KEY, JSON.stringify(cart)); } catch (e) {} }

    function qty(id) { return cart[id] ? cart[id].q : 0; }
    function setQty(id, q) {
        id = String(id);
        const ref = menu[id] || cart[id];
        if (!ref) return;
        q = Math.max(0, Math.min(50, q));
        if (q === 0) delete cart[id];
        else cart[id] = { q, nom: ref.nom, prix: ref.prix };
        save(); render();
        document.dispatchEvent(new CustomEvent('ky:cart', { detail: { id, qty: q } }));
    }
    function add(id, n = 1) {
        id = String(id);
        const before = qty(id);
        setQty(id, before + n);
        const ref = menu[id] || cart[id];
        if (before === 0 && ref) toast(ref.nom + ' ajoute nan panye a');
        const bar = $('cartbar');
        if (bar) { bar.classList.remove('bump'); void bar.offsetWidth; bar.classList.add('bump'); }
    }

    function totals() {
        let sub = 0, count = 0;
        Object.values(cart).forEach(it => { sub += it.prix * it.q; count += it.q; });
        const service = Math.round(sub * SERVICE_RATE);
        return { sub, service, grand: sub + service, count };
    }

    /* ---------- Rendu ---------- */
    function el(tag, cls, text) { const n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; }
    function icon(name) { const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg'); s.setAttribute('class', 'ico'); const u = document.createElementNS('http://www.w3.org/2000/svg', 'use'); u.setAttribute('href', '#i-' + name); s.append(u); return s; }

    function stepper(id, q) {
        const box = el('div', 'stepper');
        const minus = el('button'); minus.type = 'button'; minus.setAttribute('aria-label', 'Retire youn'); minus.append(icon('minus'));
        const plus  = el('button'); plus.type  = 'button'; plus.setAttribute('aria-label', 'Ajoute youn');  plus.append(icon('plus'));
        minus.onclick = (e) => { e.stopPropagation(); setQty(id, qty(id) - 1); };
        plus.onclick  = (e) => { e.stopPropagation(); add(id); };
        box.append(minus, el('span', 'num', String(q)), plus);
        return box;
    }

    function render() {
        const t = totals();

        // Barre du bas
        const bar = $('cartbar');
        if (bar) {
            bar.classList.toggle('show', t.count > 0);
            document.body.classList.toggle('has-cartbar', t.count > 0);
            $('cbCount').textContent = t.count;
            $('cbTotal').textContent = money(t.grand);
        }

        // Feuille panier
        const lines = $('cartLines');
        if (lines) {
            lines.innerHTML = '';
            const ids = Object.keys(cart);
            if (!ids.length) {
                const empty = el('div', 'cart-empty');
                empty.append(el('strong', null, 'Panye a vid'), el('span', null, 'Chwazi kèk plat nan meni an.'));
                lines.append(empty);
            }
            ids.forEach(id => {
                const it = cart[id];
                const line = el('div', 'cart-line');
                line.append(el('div', 'name', it.nom), stepper(id, it.q), el('div', 'price num', money(it.prix * it.q)));
                lines.append(line);
            });
            $('noteField').hidden = !ids.length;
            $('cartTotals').hidden = !ids.length;
            $('sendBtn').hidden = !ids.length;
            $('tSub').textContent = money(t.sub);
            $('tService').textContent = money(t.service);
            $('tGrand').textContent = money(t.grand);
        }
    }

    /* ---------- Feuilles ---------- */
    function openSheet(id) {
        $('backdrop')?.classList.add('show');
        $(id)?.classList.add('show');
        document.body.classList.add('no-scroll');
    }
    function closeSheets() {
        document.querySelectorAll('.sheet.show').forEach(s => s.classList.remove('show'));
        $('backdrop')?.classList.remove('show');
        document.body.classList.remove('no-scroll');
    }
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSheets(); });

    /* ---------- Toast ---------- */
    function toast(msg, type) {
        const box = $('toasts'); if (!box) return;
        const t = el('div', 'toast' + (type === 'error' ? ' error' : ''), msg);
        box.append(t);
        setTimeout(() => t.remove(), 2600);
    }

    /* ---------- Envoi de la commande ---------- */
    let sending = false;
    async function sendOrder() {
        if (sending) return;
        const items = Object.entries(cart).map(([plat_id, it]) => ({ plat_id: Number(plat_id), quantite: it.q }));
        if (!items.length) return;

        sending = true;
        const btn = $('sendBtn');
        btn.disabled = true;
        btn.querySelector('span').textContent = 'N ap voye l…';

        try {
            const res = await fetch('/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ table_id: TABLE_ID, note: $('orderNote').value.trim(), items })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Kòmand lan pa pase. Eseye ankò.');

            cart = {}; save();
            try { localStorage.setItem('kayy_cmd_' + TABLE_ID, data.commande_id); } catch (e) {}
            window.location.href = '/waiting/' + TABLE_ID + '/' + data.commande_id + '?nouvo=1';
        } catch (err) {
            toast(err.message === 'Failed to fetch' ? "Pa gen koneksyon ak restoran an. Verifye Wi-Fi a." : err.message, 'error');
            sending = false;
            btn.disabled = false;
            btn.querySelector('span').textContent = 'Voye kòmand lan';
        }
    }

    document.addEventListener('DOMContentLoaded', render);

    return {
        TABLE_ID, menu, money, qty, add, setQty, totals, stepper, icon, toast,
        openCart: () => { render(); openSheet('cartSheet'); },
        openSheet, closeSheets, sendOrder, render,
    };
})();
</script>
@stack('scripts')
</body>
</html>