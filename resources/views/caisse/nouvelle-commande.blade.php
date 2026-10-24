@extends('caisse.layouts.app')

@section('title', 'Nouvelle commande')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $menu = [];
    foreach ($categories as $cat) {
        foreach ($cat->plats as $p) {
            $menu[$p->id] = ['id' => $p->id, 'nom' => $p->nom, 'prix' => (float) ($p->prix_promo ?: $p->prix), 'cat' => $cat->id];
        }
    }
    $oldItems = collect(old('items', []))->mapWithKeys(fn ($i) => [(string) ($i['plat_id'] ?? '') => (int) ($i['quantite'] ?? 0)])->filter();
    $tableSel = (int) old('table_id', $tableChoisie);
@endphp

@push('styles')
<style>
    .pos { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 16px; align-items: start; }
    .step-title { display: flex; align-items: center; gap: 10px; margin: 0 0 10px; font-size: 14.5px; font-weight: 800; }
    .step-title b { width: 22px; height: 22px; border-radius: 50%; background: var(--brand); color: #fff; display: grid; place-items: center; font-size: 12px; }

    .tables { display: grid; grid-template-columns: repeat(auto-fill, minmax(64px, 1fr)); gap: 8px; }
    .t-btn { position: relative; height: 54px; border-radius: var(--radius-sm); border: 1px solid var(--border-strong); background: var(--surface); font-weight: 800; font-size: 17px; cursor: pointer; }
    .t-btn small { display: block; font-size: 10px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: .04em; }
    .t-btn.busy::after { content: ""; position: absolute; top: 6px; right: 6px; width: 7px; height: 7px; border-radius: 50%; background: var(--st-prep); }
    .t-btn.on { border-color: var(--brand); background: var(--brand-50); color: var(--brand-600); box-shadow: 0 0 0 2px var(--brand); }

    .menu-tools { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 10px; }
    .menu-tools .search { width: 240px; display: flex; }
    .cats { display: flex; gap: 6px; flex-wrap: wrap; }
    .cats button { height: 34px; padding: 0 12px; border-radius: 17px; border: 1px solid var(--border); background: var(--surface); font-weight: 700; font-size: 13px; color: var(--text-2); cursor: pointer; }
    .cats button.on { background: var(--text); border-color: var(--text); color: #fff; }

    .plats { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; }
    .p-btn { position: relative; text-align: left; padding: 12px; min-height: 76px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); cursor: pointer; display: flex; flex-direction: column; justify-content: space-between; gap: 6px; touch-action: manipulation; }
    .p-btn:hover { border-color: var(--brand); }
    .p-btn:active { transform: scale(.98); }
    .p-btn strong { font-size: 13.5px; line-height: 1.25; }
    .p-btn span { color: var(--text-2); font-size: 13px; font-weight: 700; }
    .p-btn .qty { position: absolute; top: -7px; right: -7px; min-width: 24px; height: 24px; padding: 0 6px; border-radius: 12px; background: var(--brand); color: #fff; font-size: 12px; font-weight: 800; display: grid; place-items: center; }

    .ticket { position: sticky; top: calc(var(--topbar-h) + 16px); }
    .ticket .card-head { padding-bottom: 12px; border-bottom: 1px solid var(--border); }
    .t-table { font-size: 13px; font-weight: 700; color: var(--text-3); }
    .t-table.ok { color: var(--brand-600); }
    .lines { max-height: 40vh; overflow-y: auto; }
    .line { display: grid; grid-template-columns: 1fr auto; gap: 2px 10px; padding: 10px 20px; border-bottom: 1px solid var(--border); align-items: center; }
    .line .n { font-weight: 700; font-size: 13.5px; }
    .line .pr { color: var(--text-3); font-size: 12.5px; }
    .qty-ctl { grid-row: span 2; display: inline-flex; align-items: center; border: 1px solid var(--border-strong); border-radius: 8px; height: 32px; }
    .qty-ctl button { width: 30px; height: 30px; border: 0; background: none; cursor: pointer; font-size: 17px; font-weight: 700; color: var(--text-2); }
    .qty-ctl span { min-width: 22px; text-align: center; font-weight: 800; font-size: 13.5px; }
    .empty-ticket { padding: 28px 20px; text-align: center; color: var(--text-3); font-size: 13px; }
    .ticket .textarea { min-height: 60px; }
    .sums { padding: 12px 20px; font-size: 13.5px; }
    .sums div { display: flex; justify-content: space-between; padding: 2px 0; color: var(--text-2); }
    .sums .grand { color: var(--text); font-weight: 800; font-size: 17px; padding-top: 8px; margin-top: 4px; border-top: 1px dashed var(--border-strong); }
    .send { width: 100%; height: 48px; justify-content: center; font-size: 14.5px; }

    @media (max-width: 1100px) {
        .pos { grid-template-columns: minmax(0, 1fr); }
        .ticket { position: static; }
    }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Nouvelle commande</h1>
        <p>Pour les clients sans téléphone : la commande part en cuisine comme une commande QR.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('caisse.commandes') }}" class="btn"><i data-lucide="list"></i> Commandes en cours</a>
    </div>
</div>

@if($errors->any())
    <div class="errors">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

@if($tables->isEmpty() || $categories->isEmpty())
    <div class="card empty">
        @if($tables->isEmpty()) Aucune table n'est créée. Demandez à l'administrateur d'ajouter les tables. @endif
        @if($categories->isEmpty()) Aucun plat disponible dans le menu. @endif
    </div>
@else
<form method="POST" action="{{ route('caisse.commandes.store') }}" id="posForm" class="pos">
    @csrf
    <input type="hidden" name="table_id" id="tableId" value="{{ $tableSel ?: '' }}">
    <div id="itemsInputs" hidden></div>

    <div class="stack">
        {{-- 1. Table --}}
        <section class="card card-body">
            <h2 class="step-title"><b>1</b> Table</h2>
            <div class="tables" id="tables">
                @foreach($tables as $t)
                    <button type="button" class="t-btn {{ $occupees->contains($t->id) ? 'busy' : '' }} {{ $tableSel === $t->id ? 'on' : '' }}"
                            data-id="{{ $t->id }}" data-num="{{ $t->numero }}"
                            title="{{ $occupees->contains($t->id) ? 'Commande déjà en cours sur cette table' : '' }}">
                        <small>Table</small>{{ $t->numero }}
                    </button>
                @endforeach
            </div>
        </section>

        {{-- 2. Plats --}}
        <section class="card card-body">
            <h2 class="step-title"><b>2</b> Plats</h2>
            <div class="menu-tools">
                <label class="search">
                    <i data-lucide="search"></i>
                    <input type="search" id="q" placeholder="Chercher un plat…" autocomplete="off">
                </label>
                <div class="cats" id="cats">
                    <button type="button" class="on" data-cat="">Tout</button>
                    @foreach($categories as $c)
                        <button type="button" data-cat="{{ $c->id }}">{{ $c->nom }}</button>
                    @endforeach
                </div>
            </div>
            <div class="plats" id="plats">
                @foreach($categories as $c)
                    @foreach($c->plats as $p)
                        <button type="button" class="p-btn" data-id="{{ $p->id }}" data-cat="{{ $c->id }}"
                                data-search="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($p->nom)) }}">
                            <strong>{{ $p->nom }}</strong>
                            <span class="num">{{ $fmt($p->prix_promo ?: $p->prix) }} HTG</span>
                            <em class="qty num" hidden></em>
                        </button>
                    @endforeach
                @endforeach
            </div>
            <p class="empty" id="noPlat" hidden>Aucun plat ne correspond.</p>
        </section>
    </div>

    {{-- 3. Ticket --}}
    <aside class="card ticket">
        <div class="card-head">
            <div>
                <h2>Ticket</h2>
                <span class="t-table" id="tLabel">Choisissez une table</span>
            </div>
            <button type="button" class="link" id="clearBtn" style="border:0;background:none;cursor:pointer" hidden>Vider</button>
        </div>
        <div class="lines" id="lines"><div class="empty-ticket">Touchez un plat pour l'ajouter.</div></div>
        <div class="card-body" style="padding-bottom:0">
            <label for="note" style="display:block;font-weight:700;font-size:13px;margin-bottom:6px">Note pour la cuisine <span class="opt" style="color:var(--text-3);font-weight:500">(facultatif)</span></label>
            <textarea class="textarea" name="note" id="note" maxlength="500" placeholder="Ex. sans piment, à emporter…">{{ old('note') }}</textarea>
        </div>
        <div class="sums num">
            <div><span>Sous-total</span><span id="sSub">0 HTG</span></div>
            <div><span>Service ({{ (int) round($serviceRate * 100) }} %)</span><span id="sServ">0 HTG</span></div>
            <div class="grand"><span>Total</span><span id="sTotal">0 HTG</span></div>
        </div>
        <div class="card-body" style="padding-top:0">
            <button type="submit" class="btn btn-primary send" id="sendBtn" disabled><i data-lucide="send"></i> Envoyer en cuisine</button>
        </div>
    </aside>
</form>
@endif

@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('posForm');
    if (!form) return;

    const MENU = @json($menu, JSON_UNESCAPED_UNICODE);
    const RATE = {{ json_encode((float) $serviceRate) }};
    const cart = new Map(Object.entries(@json($oldItems)).filter(([id]) => MENU[id]).map(([id, q]) => [id, q]));
    const $ = (id) => document.getElementById(id);
    const money = (n) => Math.round(n).toLocaleString('fr-FR').replace(/ | /g, ' ') + ' HTG';

    /* ---------- Table ---------- */
    let tableNum = (document.querySelector('.t-btn.on') || {}).dataset?.num || null;
    document.querySelectorAll('.t-btn').forEach(b => b.addEventListener('click', () => {
        document.querySelectorAll('.t-btn').forEach(x => x.classList.toggle('on', x === b));
        $('tableId').value = b.dataset.id;
        tableNum = b.dataset.num;
        if (b.classList.contains('busy')) caisseToast('Cette table a déjà une commande en cours : celle-ci sera ajoutée à part.');
        render();
    }));

    /* ---------- Plats ---------- */
    document.querySelectorAll('.p-btn').forEach(b => b.addEventListener('click', () => {
        const id = b.dataset.id;
        cart.set(id, Math.min((cart.get(id) || 0) + 1, 99));
        render();
    }));

    function setQty(id, q) {
        if (q <= 0) cart.delete(id); else cart.set(id, Math.min(q, 99));
        render();
    }

    /* ---------- Filtres ---------- */
    let cat = '';
    const norm = (s) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    function filter() {
        const t = norm($('q').value.trim());
        let shown = 0;
        document.querySelectorAll('.p-btn').forEach(b => {
            const ok = (!cat || b.dataset.cat === cat) && (!t || b.dataset.search.includes(t));
            b.hidden = !ok; if (ok) shown++;
        });
        $('noPlat').hidden = shown > 0;
    }
    document.querySelectorAll('#cats button').forEach(b => b.addEventListener('click', () => {
        cat = b.dataset.cat;
        document.querySelectorAll('#cats button').forEach(x => x.classList.toggle('on', x === b));
        filter();
    }));
    $('q').addEventListener('input', filter);

    /* ---------- Ticket ---------- */
    function render() {
        const lines = $('lines');
        lines.innerHTML = '';
        let sub = 0;
        cart.forEach((q, id) => {
            const p = MENU[id];
            sub += p.prix * q;
            const row = document.createElement('div'); row.className = 'line';
            const n = document.createElement('div'); n.className = 'n'; n.textContent = p.nom;
            const ctl = document.createElement('div'); ctl.className = 'qty-ctl';
            const m = document.createElement('button'); m.type = 'button'; m.textContent = '−'; m.setAttribute('aria-label', 'Retirer un'); m.onclick = () => setQty(id, q - 1);
            const v = document.createElement('span'); v.className = 'num'; v.textContent = q;
            const pl = document.createElement('button'); pl.type = 'button'; pl.textContent = '+'; pl.setAttribute('aria-label', 'Ajouter un'); pl.onclick = () => setQty(id, q + 1);
            ctl.append(m, v, pl);
            const pr = document.createElement('div'); pr.className = 'pr num'; pr.textContent = money(p.prix * q);
            row.append(n, ctl, pr);
            lines.append(row);
        });
        if (!cart.size) lines.innerHTML = '<div class="empty-ticket">Touchez un plat pour l\'ajouter.</div>';

        document.querySelectorAll('.p-btn').forEach(b => {
            const q = cart.get(b.dataset.id) || 0, badge = b.querySelector('.qty');
            badge.hidden = !q; badge.textContent = q;
        });

        const serv = Math.round(sub * RATE);
        $('sSub').textContent = money(sub);
        $('sServ').textContent = money(serv);
        $('sTotal').textContent = money(sub + serv);
        $('tLabel').textContent = tableNum ? 'Table ' + tableNum : 'Choisissez une table';
        $('tLabel').classList.toggle('ok', !!tableNum);
        $('clearBtn').hidden = !cart.size;
        $('sendBtn').disabled = !cart.size || !$('tableId').value;
    }
    $('clearBtn').addEventListener('click', () => { if (confirm('Vider le ticket ?')) { cart.clear(); render(); } });

    /* ---------- Envoi ---------- */
    form.addEventListener('submit', (e) => {
        if (!$('tableId').value) { e.preventDefault(); caisseToast('Choisissez la table.', 'error'); return; }
        if (!cart.size) { e.preventDefault(); caisseToast('Ajoutez au moins un plat.', 'error'); return; }
        const box = $('itemsInputs'); box.innerHTML = '';
        let i = 0;
        cart.forEach((q, id) => {
            box.insertAdjacentHTML('beforeend',
                '<input type="hidden" name="items[' + i + '][plat_id]" value="' + Number(id) + '">' +
                '<input type="hidden" name="items[' + i + '][quantite]" value="' + Number(q) + '">');
            i++;
        });
        const btn = $('sendBtn'); btn.disabled = true; btn.textContent = 'Envoi…';
    });

    render();
})();
</script>
@endpush
