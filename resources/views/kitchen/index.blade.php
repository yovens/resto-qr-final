<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cuisine · Resto Kay-Y</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #0b141a;
            --col: #121e26;
            --col-border: #1d2b34;
            --bar: #0e1920;
            --card: #ffffff;
            --line: #e7ebee;
            --text: #0f1d24;
            --text-2: #52626b;
            --text-3: #8696a0;
            --on-dark: #e6edf1;
            --on-dark-2: #8fa2ad;

            --brand: #0f9b8e;
            --new: #2f6fe4;
            --prep: #e08a1e;
            --ready: #16a34a;
            --late: #dc3b3b;

            --font: "Manrope", system-ui, sans-serif;
            --mono: "JetBrains Mono", ui-monospace, monospace;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; height: 100%; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--on-dark);
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }
        button { font: inherit; }
        svg.lucide { width: 18px; height: 18px; stroke-width: 2; }

        /* ---------- Barre du haut ---------- */
        .bar {
            flex-shrink: 0;
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px;
            background: var(--bar);
            border-bottom: 1px solid var(--col-border);
        }
        .bar-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 17px; margin-right: 8px; }
        .bar-brand .mark {
            width: 32px; height: 32px; border-radius: 8px; background: var(--brand);
            display: grid; place-items: center; font-size: 13px; color: #fff;
        }
        .bar-brand small { display: block; font-size: 11.5px; font-weight: 600; color: var(--on-dark-2); }

        .conn { display: inline-flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 700; color: var(--on-dark-2); }
        .conn::before { content: ""; width: 8px; height: 8px; border-radius: 50%; background: var(--late); }
        .conn.on { color: #7fd8a0; }
        .conn.on::before { background: var(--ready); box-shadow: 0 0 0 3px rgba(22, 163, 74, .25); }

        .bar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

        .tbtn {
            display: inline-flex; align-items: center; gap: 8px;
            height: 42px; padding: 0 14px;
            border-radius: 9px; border: 1px solid var(--col-border);
            background: var(--col); color: var(--on-dark);
            font-weight: 700; font-size: 14px; cursor: pointer;
        }
        .tbtn:hover { background: #18262f; }
        .tbtn.off { color: var(--on-dark-2); }
        .tbtn.off svg { color: var(--late); }
        .tbtn.attention { border-color: var(--prep); animation: blink 1.6s ease-in-out infinite; }
        @keyframes blink { 50% { border-color: var(--col-border); } }

        .clock { font-family: var(--mono); font-size: 20px; font-weight: 700; padding: 0 6px 0 10px; }

        /* ---------- Tableau ---------- */
        .board {
            flex: 1; min-height: 0;
            display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px; padding: 14px;
        }
        .column {
            display: flex; flex-direction: column; min-height: 0;
            background: var(--col); border: 1px solid var(--col-border); border-radius: 12px;
        }
        .col-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 16px; border-bottom: 1px solid var(--col-border);
        }
        .col-head h2 {
            margin: 0; display: flex; align-items: center; gap: 10px;
            font-size: 17px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
        }
        .col-head h2::before { content: ""; width: 10px; height: 10px; border-radius: 3px; background: var(--c); }
        .col-count {
            min-width: 30px; height: 30px; padding: 0 8px; border-radius: 15px;
            display: grid; place-items: center;
            font-family: var(--mono); font-weight: 700; font-size: 15px;
            background: var(--c); color: #fff;
        }
        .col-body { flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 12px; }
        .col-body::-webkit-scrollbar { width: 8px; }
        .col-body::-webkit-scrollbar-thumb { background: var(--col-border); border-radius: 4px; }
        .col-empty { margin: auto; color: var(--on-dark-2); font-weight: 600; font-size: 14px; padding: 30px 0; }

        .column[data-col="nouvelle"]       { --c: var(--new); }
        .column[data-col="en_preparation"] { --c: var(--prep); }
        .column[data-col="prete"]          { --c: var(--ready); }

        /* ---------- Ticket ---------- */
        .ticket {
            background: var(--card); color: var(--text);
            border-radius: 10px; border-left: 5px solid var(--c);
            padding: 14px 16px 14px 14px;
            animation: enter .35s ease-out;
        }
        .ticket.just-added { box-shadow: 0 0 0 3px var(--c); }
        @keyframes enter { from { opacity: 0; transform: translateY(-8px); } }

        .t-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .t-table { font-size: 22px; font-weight: 800; line-height: 1.1; }
        .t-id { font-family: var(--mono); font-size: 13px; color: var(--text-3); margin-top: 3px; }
        .t-timer {
            font-family: var(--mono); font-weight: 700; font-size: 15px;
            padding: 4px 9px; border-radius: 6px;
            background: #eef2f4; color: var(--text-2); white-space: nowrap;
        }
        .ticket.warn .t-timer { background: #fdf0dd; color: #a35f06; }
        .ticket.late .t-timer { background: var(--late); color: #fff; }
        .ticket.late { border-left-color: var(--late); }

        .t-note {
            margin-top: 10px; padding: 8px 10px; border-radius: 6px;
            background: #fff6dc; color: #6b4a00; font-size: 14px; font-weight: 600;
        }
        .t-note b { display: block; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; color: #a35f06; }

        .t-items { list-style: none; margin: 12px 0 0; padding: 10px 0 0; border-top: 1px solid var(--line); }
        .t-items li { display: flex; gap: 10px; padding: 4px 0; font-size: 17px; font-weight: 600; }
        .t-items .q { font-family: var(--mono); font-weight: 700; min-width: 34px; color: var(--text); }

        .t-actions { display: flex; gap: 8px; margin-top: 14px; }
        .act {
            flex: 1; height: 50px; border: 0; border-radius: 8px; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            font-weight: 800; font-size: 15px; color: #fff; background: var(--a);
            touch-action: manipulation;
        }
        .act:active { transform: scale(.98); }
        .act[disabled] { opacity: .6; cursor: wait; }
        .act.start { --a: var(--prep); }
        .act.ready { --a: var(--ready); }
        .act.serve { --a: var(--brand); }
        .act.ghost { flex: 0 0 50px; background: #eef2f4; color: var(--text-2); }

        /* ---------- Toast ---------- */
        .toasts { position: fixed; bottom: 18px; left: 50%; transform: translateX(-50%); display: grid; gap: 8px; z-index: 100; }
        .toast {
            background: #fff; color: var(--text); font-weight: 700; font-size: 15px;
            padding: 12px 18px; border-radius: 9px; box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
            border-left: 4px solid var(--c, var(--brand));
            animation: enter .25s ease-out;
        }
        .toast.err { --c: var(--late); }

        /* ---------- Petits écrans (tablette en portrait, téléphone) ---------- */
        @media (max-width: 900px) {
            body { overflow: auto; height: auto; }
            .board { grid-template-columns: 1fr; }
            .col-body { overflow: visible; }
            .bar { flex-wrap: wrap; }
            .tbtn span { display: none; }
            .clock { display: none; }
        }
    </style>
</head>
<body>

@php
    $initial = $commandes->map(fn ($c) => [
        'id'      => $c->id,
        'statut'  => $c->statut,
        'table'   => $c->table->numero ?? null,
        'created' => $c->created_at->timestamp,
        'note'    => $c->note,
        'items'   => $c->items->map(fn ($i) => [
            'q'   => $i->quantite,
            'nom' => $i->plat->nom ?? 'Plat supprimé',
        ])->values(),
    ])->values();
@endphp

<header class="bar">
    <div class="bar-brand">
        <span class="mark">KY</span>
        <span>Cuisine<small>Resto Kay-Y</small></span>
    </div>
    <span class="conn" id="conn">Connexion…</span>

    <div class="bar-right">
        <button class="tbtn" id="btnSound" type="button"><i data-lucide="volume-2"></i><span>Son</span></button>
        <button class="tbtn" id="btnVoice" type="button"><i data-lucide="megaphone"></i><span>Annonce vocale</span></button>
        <button class="tbtn" id="btnFull" type="button" title="Plein écran"><i data-lucide="maximize"></i></button>
        <span class="clock" id="clock">--:--</span>
    </div>
</header>

<main class="board">
    <section class="column" data-col="nouvelle">
        <div class="col-head"><h2>Nouvelles</h2><span class="col-count">0</span></div>
        <div class="col-body"></div>
    </section>
    <section class="column" data-col="en_preparation">
        <div class="col-head"><h2>En préparation</h2><span class="col-count">0</span></div>
        <div class="col-body"></div>
    </section>
    <section class="column" data-col="prete">
        <div class="col-head"><h2>Prêtes</h2><span class="col-count">0</span></div>
        <div class="col-body"></div>
    </section>
</main>

<div class="toasts" id="toasts"></div>
<audio id="notifSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
(function () {
    'use strict';

    const UPDATE_URL = @json(url('/cuisine/update'));
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const WARN_AFTER = 10 * 60;   // secondes
    const LATE_AFTER = 15 * 60;

    const STATUS = {
        nouvelle:       { label: 'Nouvelle',       next: 'en_preparation', btn: 'Commencer',       cls: 'start', icon: 'play' },
        en_preparation: { label: 'En préparation', next: 'prete',          btn: 'Prête',           cls: 'ready', icon: 'check' },
        prete:          { label: 'Prête',          next: 'servie',         btn: 'Servie',          cls: 'serve', icon: 'hand-platter' },
    };

    const icons = () => window.lucide && lucide.createIcons();

    /* ======================================================
       Préférences : son & annonce vocale (gardées sur l'appareil)
       ====================================================== */
    const store = {
        get: (k, d) => { try { const v = localStorage.getItem(k); return v === null ? d : v === '1'; } catch (e) { return d; } },
        set: (k, v) => { try { localStorage.setItem(k, v ? '1' : '0'); } catch (e) {} },
    };
    let soundOn = store.get('kitchen.sound', true);
    let voiceOn = store.get('kitchen.voice', true);
    let audioUnlocked = false;

    const sound    = document.getElementById('notifSound');
    const btnSound = document.getElementById('btnSound');
    const btnVoice = document.getElementById('btnVoice');

    function renderToggles() {
        btnSound.classList.toggle('off', !soundOn);
        btnSound.innerHTML = `<i data-lucide="${soundOn ? 'volume-2' : 'volume-x'}"></i><span>${soundOn ? 'Son' : 'Son coupé'}</span>`;
        // Le navigateur bloque le son tant que personne n'a touché l'écran
        btnSound.classList.toggle('attention', soundOn && !audioUnlocked);
        btnSound.title = soundOn && !audioUnlocked ? "Touchez l'écran une fois pour autoriser le son" : '';

        btnVoice.classList.toggle('off', !voiceOn);
        btnVoice.innerHTML = `<i data-lucide="${voiceOn ? 'megaphone' : 'megaphone-off'}"></i><span>${voiceOn ? 'Annonce vocale' : 'Voix coupée'}</span>`;
        icons();
    }

    function unlockAudio() {
        if (audioUnlocked || !sound) return;
        sound.muted = true;
        sound.play().then(() => {
            sound.pause(); sound.currentTime = 0; sound.muted = false;
            audioUnlocked = true; renderToggles();
        }).catch(() => { sound.muted = false; });
    }
    document.addEventListener('pointerdown', unlockAudio);

    btnSound.addEventListener('click', () => {
        soundOn = !soundOn; store.set('kitchen.sound', soundOn);
        if (!soundOn && sound) { sound.pause(); }
        renderToggles();
    });
    btnVoice.addEventListener('click', () => {
        voiceOn = !voiceOn; store.set('kitchen.voice', voiceOn);
        if (!voiceOn && 'speechSynthesis' in window) speechSynthesis.cancel();
        renderToggles();
    });
    document.getElementById('btnFull').addEventListener('click', () => {
        if (document.fullscreenElement) document.exitFullscreen();
        else document.documentElement.requestFullscreen?.();
    });

    function ding() {
        if (!soundOn || !sound) return Promise.resolve();
        sound.currentTime = 0;
        return new Promise((resolve) => {
            sound.onended = resolve;
            sound.play().catch(resolve);
            setTimeout(resolve, 4000);
        });
    }

    function announce(order) {
        if (!voiceOn || !('speechSynthesis' in window)) return;
        const plats = order.items.map(i => i.q + ' ' + i.nom).join(', ');
        let text = 'Nouvelle commande, table ' + (order.table ?? 'inconnue') + '.';
        if (plats) text += ' ' + plats + '.';
        if (order.note) text += ' Note : ' + order.note + '.';
        speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text);
        u.lang = 'fr-FR'; u.rate = 0.95;
        speechSynthesis.speak(u);
    }

    /* ======================================================
       Rendu des tickets
       ====================================================== */
    const orders = new Map();

    function el(tag, cls, text) {
        const n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text !== undefined && text !== null) n.textContent = text;
        return n;
    }

    function buildCard(o) {
        const st = STATUS[o.statut];
        const card = el('article', 'ticket');
        card.id = 'order-' + o.id;

        const head = el('div', 't-head');
        const who = el('div');
        who.append(el('div', 't-table', o.table ? 'Table ' + o.table : 'À emporter'));
        who.append(el('div', 't-id', '#' + String(o.id).padStart(4, '0')));
        const timer = el('span', 't-timer', '00:00');
        timer.dataset.timer = '';
        head.append(who, timer);
        card.append(head);

        if (o.note) {
            const note = el('div', 't-note');
            note.append(el('b', null, 'Note client'), document.createTextNode(o.note));
            card.append(note);
        }

        const list = el('ul', 't-items');
        o.items.forEach(i => {
            const li = el('li');
            li.append(el('span', 'q', i.q + '×'), el('span', null, i.nom));
            list.append(li);
        });
        card.append(list);

        const actions = el('div', 't-actions');
        const main = el('button', 'act ' + st.cls);
        main.type = 'button';
        main.innerHTML = `<i data-lucide="${st.icon}"></i>`;
        main.append(document.createTextNode(st.btn));
        main.addEventListener('click', () => changeStatus(o.id, st.next, main));
        actions.append(main);

        if (o.statut !== 'nouvelle') {
            const back = el('button', 'act ghost');
            back.type = 'button';
            back.title = 'Revenir à l\'étape précédente';
            back.innerHTML = '<i data-lucide="undo-2"></i>';
            const prev = o.statut === 'prete' ? 'en_preparation' : 'nouvelle';
            back.addEventListener('click', () => changeStatus(o.id, prev, back));
            actions.append(back);
        }
        card.append(actions);
        return card;
    }

    function place(o, { highlight = false } = {}) {
        document.getElementById('order-' + o.id)?.remove();

        if (!STATUS[o.statut]) { orders.delete(o.id); refreshCounts(); return; }   // servie / archivée
        orders.set(o.id, o);

        const body = document.querySelector(`.column[data-col="${o.statut}"] .col-body`);
        const card = buildCard(o);
        if (highlight) { card.classList.add('just-added'); setTimeout(() => card.classList.remove('just-added'), 6000); }

        // Ordre d'arrivée : la plus ancienne en haut
        const after = [...body.querySelectorAll('.ticket')].find(c => (orders.get(+c.id.slice(6))?.created ?? 0) > o.created);
        body.insertBefore(card, after || null);

        icons(); tickTimers(); refreshCounts();
    }

    function refreshCounts() {
        document.querySelectorAll('.column').forEach(col => {
            const n = col.querySelectorAll('.ticket').length;
            col.querySelector('.col-count').textContent = n;
            const body = col.querySelector('.col-body');
            const empty = body.querySelector('.col-empty');
            if (n === 0 && !empty) body.append(el('div', 'col-empty', 'Aucune commande'));
            if (n > 0 && empty) empty.remove();
        });
    }

    /* ======================================================
       Chronos
       ====================================================== */
    const pad = n => String(n).padStart(2, '0');
    function tickTimers() {
        const now = Math.floor(Date.now() / 1000);
        orders.forEach(o => {
            const card = document.getElementById('order-' + o.id);
            if (!card) return;
            const s = Math.max(0, now - o.created);
            card.querySelector('[data-timer]').textContent = pad(Math.floor(s / 60)) + ':' + pad(s % 60);
            const active = o.statut !== 'prete';
            card.classList.toggle('late', active && s >= LATE_AFTER);
            card.classList.toggle('warn', active && s >= WARN_AFTER && s < LATE_AFTER);
        });
    }
    setInterval(tickTimers, 1000);

    const clock = document.getElementById('clock');
    const tickClock = () => clock.textContent = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    tickClock(); setInterval(tickClock, 10000);

    /* ======================================================
       Changement de statut (sans recharger la page)
       ====================================================== */
    async function changeStatus(id, statut, button) {
        const o = orders.get(id);
        if (!o) return;
        button.disabled = true;
        try {
            const res = await fetch(UPDATE_URL + '/' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ statut }),
            });
            if (!res.ok) throw new Error(res.status);
            place({ ...o, statut });
        } catch (e) {
            button.disabled = false;
            toast('Échec de la mise à jour, réessayez.', true);
        }
    }

    function toast(msg, isErr) {
        const t = el('div', 'toast' + (isErr ? ' err' : ''), msg);
        document.getElementById('toasts').append(t);
        setTimeout(() => t.remove(), 3500);
    }

    /* ======================================================
       Temps réel
       ====================================================== */
    function normalize(e) {
        const c = e.commande || e;
        const created = c.created_at ? Math.floor(Date.parse(c.created_at) / 1000) : Math.floor(Date.now() / 1000);
        return {
            id: c.id,
            statut: c.statut || 'nouvelle',
            table: c.table?.numero ?? e.table?.numero ?? null,
            created: Number.isFinite(created) ? created : Math.floor(Date.now() / 1000),
            note: c.note || null,
            items: (c.items || e.items || []).map(i => ({ q: i.quantite ?? 1, nom: i.plat?.nom ?? i.nom ?? 'Plat' })),
        };
    }

    const pusher = new Pusher(@json(config('broadcasting.connections.reverb.key')), {
        wsHost: @json(config('broadcasting.connections.reverb.options.host')),
        wsPort: @json((int) config('broadcasting.connections.reverb.options.port')),
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        cluster: 'mt1',
    });

    const conn = document.getElementById('conn');
    pusher.connection.bind('state_change', ({ current }) => {
        const on = current === 'connected';
        conn.classList.toggle('on', on);
        conn.textContent = on ? 'En direct' : 'Hors ligne';
    });

    const channel = pusher.subscribe('kitchen');

    channel.bind('new-order', async (e) => {
        const o = normalize(e);
        if (!o.id || orders.has(o.id)) return;
        place({ ...o, statut: 'nouvelle' }, { highlight: true });
        await ding();
        announce(o);
    });

    // Les noms d'événements diffèrent entre les écrans existants : on écoute les deux variantes.
    const onAccepted = (e) => { const o = normalize(e); const cur = orders.get(o.id); if (cur && cur.statut !== 'en_preparation') { place({ ...cur, statut: 'en_preparation' }); } };
    const onReady    = (e) => { const o = normalize(e); const cur = orders.get(o.id); if (cur && cur.statut !== 'prete') { place({ ...cur, statut: 'prete' }); ding(); } };
    channel.bind('order-accepted', onAccepted);
    channel.bind('accepted', onAccepted);
    channel.bind('order-ready', onReady);
    channel.bind('ready', onReady);

    /* ======================================================
       Démarrage
       ====================================================== */
    @json($initial).forEach(o => place(o));
    refreshCounts();
    renderToggles();
})();
</script>
</body>
</html>