@extends('client.layouts.app')

@section('title', 'Swivi kòmand')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $tableNum = $table->numero ?? $tableId;

    // Texte et étape de chaque statut (doit rester identique à ClientController::statut)
    $etats = [
        'nouvelle'       => [1, 'Kwizin nan resevwa kòmand ou', 'Yo pral kòmanse prepare l talè.'],
        'acceptee'       => [2, 'Y ap prepare manje ou', 'Chèf la sou li. Ou ka jwe yon ti jwèt pandan w ap tann.'],
        'en_preparation' => [2, 'Y ap prepare manje ou', 'Chèf la sou li. Ou ka jwe yon ti jwèt pandan w ap tann.'],
        'prete'          => [3, 'Manje ou pare !', 'Y ap pote l ba ou kounye a.'],
        'servie'         => [4, 'Bon apeti !', 'Si w bezwen lòt bagay, ou ka kòmande ankò.'],
        'payee'          => [4, 'Mèsi pou vizit ou !', 'Kòmand lan peye. N ap tann ou ankò.'],
    ];
    $etat = $commande ? ($etats[$commande->statut] ?? [1, 'Kòmand ou an kou', '']) : null;

    // Estimation : le plat le plus long de la commande, sinon 20 min
    $estimation = $commande
        ? (int) ($commande->items->map(fn ($i) => (int) ($i->plat->temps_preparation ?? 0))->max() ?: 20)
        : 20;
@endphp

@push('styles')
<style>
    .head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; }
    .head a { display: inline-flex; align-items: center; gap: 4px; font-weight: 700; color: var(--text-2); }
    .head .table-pill { font-weight: 700; font-size: 13.5px; padding: 6px 12px; border-radius: 16px; background: var(--surface); border: 1px solid var(--line); }

    .welcome { margin: 0 16px 12px; padding: 12px 14px; border-radius: 12px; background: var(--brand-50); color: var(--brand-600); font-weight: 700; font-size: 14px; display: flex; gap: 8px; align-items: center; }

    .status { margin: 0 16px; padding: 22px 18px 18px; border-radius: 18px; background: var(--surface); border: 1px solid var(--line); text-align: center; transition: background .3s, border-color .3s; }
    .status.ready { background: var(--ready-50); border-color: #bfe3cb; }
    .status-icon { width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 50%; display: grid; place-items: center; background: var(--warn-50); color: var(--warn); }
    .status-icon .ico { width: 30px; height: 30px; }
    .status.ready .status-icon { background: var(--ready); color: #fff; animation: pop .5s ease; }
    @keyframes pop { 50% { transform: scale(1.15); } }
    .status h1 { margin: 0 0 4px; font-size: 22px; font-weight: 800; letter-spacing: -.01em; }
    .status p { margin: 0; color: var(--text-2); }

    .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin: 20px 0 6px; }
    .steps .bar { height: 6px; border-radius: 3px; background: var(--line); overflow: hidden; }
    .steps .bar span { display: block; height: 100%; width: 0; background: var(--brand); transition: width .5s ease; }
    .steps .bar.done span { width: 100%; }
    .steps .bar.now span { width: 50%; animation: pulse 1.6s ease-in-out infinite; }
    .status.ready .steps .bar span { background: var(--ready); }
    @keyframes pulse { 50% { opacity: .45; } }
    .step-labels { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; font-size: 11.5px; font-weight: 700; color: var(--text-3); }
    .step-labels .on { color: var(--text); }

    .times { display: flex; justify-content: center; gap: 18px; margin-top: 14px; font-size: 13px; color: var(--text-2); }
    .times span { display: inline-flex; align-items: center; gap: 5px; }
    .times .ico { width: 15px; height: 15px; }

    .orders { display: flex; gap: 8px; overflow-x: auto; padding: 14px 16px 2px; scrollbar-width: none; }
    .orders::-webkit-scrollbar { display: none; }
    .order-chip { flex-shrink: 0; padding: 8px 12px; border-radius: 12px; background: var(--surface); border: 1px solid var(--line); font-size: 13px; }
    .order-chip strong { display: block; }
    .order-chip span { color: var(--text-3); }
    .order-chip.current { border-color: var(--brand); box-shadow: 0 0 0 2px var(--brand-50); }

    .card { margin: 14px 16px 0; padding: 16px; border-radius: 16px; background: var(--surface); border: 1px solid var(--line); }
    .card h2 { margin: 0 0 8px; font-size: 16px; font-weight: 800; }
    .line { display: flex; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--line); font-size: 14.5px; }
    .line:last-of-type { border-bottom: 0; }
    .line .q { font-weight: 800; color: var(--text-2); min-width: 28px; }
    .line .p { margin-left: auto; color: var(--text-2); }
    .sum { display: flex; justify-content: space-between; padding-top: 10px; margin-top: 4px; border-top: 1px dashed var(--line); font-weight: 800; font-size: 16px; }
    .note { margin-top: 10px; padding: 10px 12px; border-radius: 10px; background: var(--bg); color: var(--text-2); font-size: 13.5px; }
    .actions { margin: 14px 16px 0; }

    /* Jeu */
    .game { margin: 14px 16px 24px; padding: 16px; border-radius: 16px; background: var(--surface); border: 1px solid var(--line); }
    .game-head { display: flex; justify-content: space-between; align-items: baseline; gap: 10px; margin-bottom: 10px; }
    .game-head h2 { margin: 0; font-size: 16px; font-weight: 800; white-space: nowrap; }
    .game-head span { text-align: right; }
    .game-head span { font-size: 12.5px; color: var(--text-3); }
    .game-box { position: relative; border-radius: 12px; overflow: hidden; background: #cfe9f3; }
    #gameCanvas { display: block; width: 100%; height: auto; touch-action: manipulation; cursor: pointer; }
    .game-over { position: absolute; inset: 0; display: grid; place-content: center; text-align: center; gap: 4px; background: rgba(20, 35, 31, .78); color: #fff; }
    .game-over strong { font-size: 20px; }
    .game-over button { margin-top: 10px; }
    .game-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 13.5px; color: var(--text-2); }
    .game-bar b { color: var(--text); }
</style>
@endpush

@section('content')
<div class="wrap">
    <div class="head">
        <a href="/menu/{{ $tableId }}"><svg class="ico"><use href="#i-back"/></svg> Meni</a>
        <span class="table-pill">Tab {{ $tableNum }}</span>
    </div>

    @if(!$commande)
        <div class="status">
            <div class="status-icon"><svg class="ico"><use href="#i-receipt"/></svg></div>
            <h1>Pa gen kòmand an kou</h1>
            <p>Chwazi kèk plat nan meni an pou w kòmande.</p>
            <a href="/menu/{{ $tableId }}" class="btn btn-primary btn-block" style="margin-top:16px">Wè meni an</a>
        </div>
    @else
        @if(request('nouvo'))
            <div class="welcome"><svg class="ico"><use href="#i-check"/></svg> Kòmand lan pase. Mèsi !</div>
        @endif

        <section class="status {{ $etat[0] >= 3 ? 'ready' : '' }}" id="statusCard"
                 data-url="{{ url('/menu/' . $tableId . '/commande/' . $commande->id . '/statut') }}"
                 data-statut="{{ $commande->statut }}">
            <div class="status-icon" id="statusIcon"><svg class="ico"><use href="#{{ $etat[0] >= 3 ? 'i-check' : 'i-chef' }}"/></svg></div>
            <h1 id="statusTitle">{{ $etat[1] }}</h1>
            <p id="statusText">{{ $etat[2] }}</p>

            <div class="steps" id="steps">
                @for($i = 1; $i <= 4; $i++)
                    <div class="bar {{ $i < $etat[0] || $etat[0] === 4 ? 'done' : ($i === $etat[0] ? 'now' : '') }}"><span></span></div>
                @endfor
            </div>
            <div class="step-labels" id="stepLabels">
                @foreach(['Resevwa', 'Nan kwizin', 'Pare', 'Sèvi'] as $i => $lbl)
                    <span class="{{ $i + 1 <= $etat[0] ? 'on' : '' }}">{{ $lbl }}</span>
                @endforeach
            </div>

            <div class="times" id="times" @if($etat[0] >= 3) hidden @endif>
                <span><svg class="ico"><use href="#i-clock"/></svg> <span id="elapsed">—</span></span>
                <span>Anviwon {{ $estimation }} min</span>
            </div>
        </section>

        @if($hasMultipleOrders)
            <nav class="orders" aria-label="Kòmand ou yo">
                @foreach($commandesActives as $cmd)
                    <a href="/waiting/{{ $tableId }}/{{ $cmd->id }}" class="order-chip {{ $cmd->id == $commande->id ? 'current' : '' }}">
                        <strong>Kòmand #{{ $cmd->id }}</strong>
                        <span>{{ $etats[$cmd->statut][1] ?? 'An kou' }}</span>
                    </a>
                @endforeach
            </nav>
        @endif

        <section class="card">
            <h2>Kòmand #{{ $commande->id }}</h2>
            @foreach($commande->items as $item)
                @php $pu = (float) ($item->prix ?? $item->plat->prix ?? 0); @endphp
                <div class="line">
                    <span class="q num">{{ $item->quantite }}×</span>
                    <span>{{ $item->plat->nom ?? 'Plat' }}</span>
                    <span class="p num">{{ $fmt($pu * $item->quantite) }}</span>
                </div>
            @endforeach
            <div class="sum"><span>Total</span><span class="num">{{ $fmt($commande->total) }} HTG</span></div>
            @if($commande->note)
                <div class="note">Nòt ou : {{ $commande->note }}</div>
            @endif
        </section>

        <div class="actions">
            <a href="/menu/{{ $tableId }}" class="btn btn-block"><svg class="ico"><use href="#i-plus"/></svg> Kòmande lòt bagay</a>
        </div>

        {{-- Petit jeu pendant l'attente --}}
        <section class="game" id="game">
            <div class="game-head">
                <h2>Tap-Tap Kay-Y</h2>
                <span>Touche pou sote · ranmase manje</span>
            </div>
            <div class="game-box">
                <canvas id="gameCanvas" width="360" height="200" aria-label="Jwèt Tap-Tap"></canvas>
                <div class="game-over" id="gameOver">
                    <strong id="goTitle">Pare pou w kondui ?</strong>
                    <span id="goText">Evite twou ak wòch yo.</span>
                    <button type="button" class="btn btn-primary" id="goBtn">Kòmanse</button>
                </div>
            </div>
            <div class="game-bar">
                <span>Pwen : <b class="num" id="gScore">0</b></span>
                <span>Pi bon : <b class="num" id="gBest">0</b></span>
            </div>
        </section>
    @endif
</div>

<audio id="readySound" preload="auto"><source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg"></audio>
@endsection

@push('scripts')
@if($commande)
<script>
/* ==========================================================
   Suivi du statut (requête locale toutes les 4 s, sans internet)
   ========================================================== */
(function () {
    const card   = document.getElementById('statusCard');
    const ETATS  = @json($etats, JSON_UNESCAPED_UNICODE);
    const CREATED = {{ $commande->created_at->timestamp }};
    let statut   = card.dataset.statut;
    let stopped  = false;

    try { localStorage.setItem('kayy_cmd_' + KY.TABLE_ID, @json($commande->id)); } catch (e) {}

    function elapsed() {
        const min = Math.max(0, Math.floor((Date.now() / 1000 - CREATED) / 60));
        document.getElementById('elapsed').textContent = min < 1 ? 'Voye kounye a' : 'Voye depi ' + min + ' min';
    }
    elapsed(); setInterval(elapsed, 30000);

    function apply(s) {
        const e = ETATS[s] || [1, 'Kòmand ou an kou', ''];
        const step = e[0];
        card.classList.toggle('ready', step >= 3);
        document.getElementById('statusTitle').textContent = e[1];
        document.getElementById('statusText').textContent = e[2];
        document.querySelector('#statusIcon use').setAttribute('href', step >= 3 ? '#i-check' : '#i-chef');
        document.querySelectorAll('#steps .bar').forEach((b, i) => {
            b.className = 'bar ' + (i + 1 < step || step === 4 ? 'done' : (i + 1 === step ? 'now' : ''));
        });
        document.querySelectorAll('#stepLabels span').forEach((l, i) => l.classList.toggle('on', i + 1 <= step));
        document.getElementById('times').hidden = step >= 3;
    }

    function celebrate() {
        try { navigator.vibrate && navigator.vibrate([200, 100, 200]); } catch (e) {}
        const snd = document.getElementById('readySound');
        if (snd) { snd.currentTime = 0; snd.play().catch(() => {}); }
        KY.toast('Manje ou pare !');
    }

    async function poll() {
        if (stopped || document.visibilityState !== 'visible') return;
        try {
            const r = await fetch(card.dataset.url, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
            if (!r.ok) return;
            const data = await r.json();
            if (data.statut && data.statut !== statut) {
                const wasReady = (ETATS[statut] || [1])[0] >= 3;
                statut = data.statut;
                apply(statut);
                if (!wasReady && data.statut === 'prete') celebrate();
            }
            if (data.termine) stopped = true;
        } catch (e) { /* réseau local momentanément indisponible : on réessaie */ }
    }
    setInterval(poll, 4000);
    document.addEventListener('visibilitychange', poll);
})();

/* ==========================================================
   Jeu Tap-Tap
   ========================================================== */
(function () {
    const canvas = document.getElementById('gameCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height, GROUND = 160;
    const overlay = document.getElementById('gameOver');
    const scoreEl = document.getElementById('gScore');
    const bestEl  = document.getElementById('gBest');
    const BEST_KEY = 'kayy_taptap_best';
    let best = 0; try { best = Number(localStorage.getItem(BEST_KEY)) || 0; } catch (e) {}
    bestEl.textContent = best;

    let loop = null, playing = false, score, frame, y, vy, speed, obstacles, foods, particles, clouds, shield;

    function reset() {
        score = 0; frame = 0; y = GROUND - 22; vy = 0; speed = 3; shield = 0;
        obstacles = []; foods = []; particles = []; clouds = [];
        scoreEl.textContent = '0';
    }

    function rrect(x, yy, w, h, r) {
        ctx.beginPath();
        if (ctx.roundRect) ctx.roundRect(x, yy, w, h, r); else ctx.rect(x, yy, w, h);
        ctx.fill();
    }

    function drawBus(x, top) {
        ctx.fillStyle = '#c0392b'; rrect(x, top, 46, 22, 4); rrect(x + 2, top - 8, 42, 9, 3);
        ctx.fillStyle = '#f4c542'; ctx.fillRect(x + 5, top + 3, 10, 7); ctx.fillRect(x + 19, top + 3, 10, 7); ctx.fillRect(x + 33, top + 3, 9, 7);
        ctx.fillStyle = '#0f9b8e'; ctx.fillRect(x + 2, top + 14, 42, 3);
        ctx.fillStyle = '#1d1915';
        ctx.beginPath(); ctx.arc(x + 11, top + 22, 6, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(x + 35, top + 22, 6, 0, Math.PI * 2); ctx.fill();
        if (shield > 0) {
            ctx.strokeStyle = 'rgba(244,197,66,' + (0.4 + Math.abs(Math.sin(frame / 6)) * 0.6) + ')';
            ctx.lineWidth = 2; ctx.beginPath(); ctx.arc(x + 23, top + 8, 31, 0, Math.PI * 2); ctx.stroke();
        }
    }

    function tick() {
        frame++;
        // Décor
        ctx.fillStyle = '#cfe9f3'; ctx.fillRect(0, 0, W, GROUND);
        ctx.fillStyle = 'rgba(244,197,66,.35)'; ctx.beginPath(); ctx.arc(W - 50, 36, 20, 0, Math.PI * 2); ctx.fill();
        if (frame % 110 === 0) clouds.push({ x: W + 30, y: 20 + Math.random() * 45, r: 10 + Math.random() * 10 });
        ctx.fillStyle = 'rgba(255,255,255,.85)';
        clouds.forEach(c => { c.x -= 0.6; ctx.beginPath(); ctx.arc(c.x, c.y, c.r, 0, 7); ctx.arc(c.x + c.r, c.y + 2, c.r * .8, 0, 7); ctx.arc(c.x - c.r, c.y + 3, c.r * .7, 0, 7); ctx.fill(); });
        clouds = clouds.filter(c => c.x > -40);
        ctx.fillStyle = '#d9b46a'; ctx.fillRect(0, GROUND, W, H - GROUND);
        ctx.fillStyle = 'rgba(255,255,255,.35)';
        for (let i = 0; i < 7; i++) ctx.fillRect(((i * 60) - (frame * speed) % 60), GROUND + 20, 26, 3);

        // Physique
        vy += 0.5; y += vy;
        if (y > GROUND - 22) { y = GROUND - 22; vy = 0; }
        if (y < 8) { y = 8; vy = 0; }
        if (frame % 450 === 0) speed += 0.3;
        if (shield > 0) shield--;
        drawBus(50, y);

        // Obstacles
        const every = Math.max(55, 95 - Math.floor(speed * 4));
        if (frame % every === 0) obstacles.push({ x: W, h: 16 + Math.random() * 20, rock: Math.random() > .7 });
        for (let i = obstacles.length - 1; i >= 0; i--) {
            const o = obstacles[i]; o.x -= speed;
            ctx.fillStyle = o.rock ? '#7a5c4a' : '#5b4033';
            if (o.rock) { ctx.beginPath(); ctx.moveTo(o.x + 12, GROUND - o.h); ctx.lineTo(o.x + 24, GROUND); ctx.lineTo(o.x, GROUND); ctx.fill(); }
            else ctx.fillRect(o.x, GROUND - o.h, 22, o.h);
            if (!shield && o.x < 92 && o.x + 22 > 54 && y + 28 > GROUND - o.h) return end();
            if (o.x < -30) obstacles.splice(i, 1);
        }

        // Nourriture
        if (frame % 70 === 0) foods.push({ x: W, y: 70 + Math.random() * 70, t: Math.floor(Math.random() * 5) });
        ctx.font = '20px serif';
        for (let i = foods.length - 1; i >= 0; i--) {
            const f = foods[i]; f.x -= speed;
            ctx.fillText(['🍗', '🍌', '🥥', '🌶️', '🥘'][f.t], f.x, f.y);
            if (f.x < 96 && f.x > 40 && Math.abs(f.y - (y + 8)) < 24) {
                const pts = f.t === 4 ? 20 : 10;
                score += pts; scoreEl.textContent = score;
                for (let k = 0; k < 8; k++) particles.push({ x: f.x, y: f.y, vx: (Math.random() - .5) * 4, vy: (Math.random() - .5) * 4, l: 20 });
                if (score % 100 === 0) { shield = 150; KY.toast('Boukliye aktive !'); }
                foods.splice(i, 1);
            } else if (f.x < -20) foods.splice(i, 1);
        }
        ctx.fillStyle = '#f4c542';
        particles.forEach(p => { p.x += p.vx; p.y += p.vy; p.l--; ctx.globalAlpha = p.l / 20; ctx.fillRect(p.x, p.y, 3, 3); });
        ctx.globalAlpha = 1;
        particles = particles.filter(p => p.l > 0);
    }

    function start() {
        reset(); playing = true; overlay.hidden = true;
        clearInterval(loop); loop = setInterval(tick, 20);
    }
    function end() {
        playing = false; clearInterval(loop);
        if (score > best) { best = score; bestEl.textContent = best; try { localStorage.setItem(BEST_KEY, best); } catch (e) {} }
        document.getElementById('goTitle').textContent = score >= best && score > 0 ? 'Nouvo rekò : ' + score + ' !' : 'Tap-Tap la fè aksidan';
        document.getElementById('goText').textContent = 'Pwen : ' + score;
        document.getElementById('goBtn').textContent = 'Rejwe';
        overlay.hidden = false;
    }
    function jump() { if (playing && y >= GROUND - 24) vy = -8.2; }

    document.getElementById('goBtn').addEventListener('click', start);
    canvas.addEventListener('pointerdown', (e) => { e.preventDefault(); jump(); });
    document.addEventListener('keydown', (e) => {
        if (!playing || /INPUT|TEXTAREA/.test(document.activeElement.tagName)) return;
        if (e.code === 'Space' || e.code === 'ArrowUp') { e.preventDefault(); jump(); }
    });
    // Pause automatique si le client quitte l'écran
    document.addEventListener('visibilitychange', () => { if (document.hidden && playing) end(); });

    // Premier rendu du décor
    reset(); tick(); clearInterval(loop);
})();
</script>
@endif
@endpush