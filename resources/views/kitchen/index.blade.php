<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuisine LIVE - Gestion des Commandes</title>
    @vite(['resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --steel-950: #12161a;
            --steel-900: #1b2126;
            --steel-800: #262e34;
            --steel-700: #333d45;
            --paper: #f3ede0;
            --paper-line: #ddd4bd;
            --ink: #2a2620;
            --ink-soft: #6b6558;
            --new: #e8493f;
            --prep: #f2a33d;
            --ready: #3fae63;
            --muted: #8b95a1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                repeating-linear-gradient(135deg, rgba(255,255,255,0.02) 0px, rgba(255,255,255,0.02) 1px, transparent 1px, transparent 8px),
                var(--steel-950);
            color: #e7e6e2;
            min-height: 100vh;
            padding: 28px 28px 60px;
        }

        .pass-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            border-bottom: 2px solid var(--steel-700);
            padding-bottom: 18px;
            margin-bottom: 28px;
        }
        .pass-header h1 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 1.9rem;
            margin: 0;
        }
        .pass-header h1 span { color: var(--prep); }
        .pass-clock {
            font-family: 'Roboto Mono', monospace;
            font-size: 1rem;
            color: var(--muted);
        }

        .kitchen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 22px;
        }

        /* --- Ticket card, styled like a printed kitchen chit --- */
        .ticket {
            background: var(--paper);
            color: var(--ink);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 24px rgba(0,0,0,0.35);
            overflow: hidden;
            transition: box-shadow 0.2s ease;
        }

        .ticket__perforation {
            height: 10px;
            background-image: radial-gradient(circle at 10px 5px, var(--steel-950) 3px, transparent 3.5px);
            background-size: 20px 10px;
            background-color: var(--paper);
        }

        .ticket__band {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            color: #fff;
            background: var(--new);
        }
        .ticket.en_preparation .ticket__band { background: var(--prep); }
        .ticket.prete .ticket__band { background: var(--ready); }

        .ticket__num {
            font-family: 'Roboto Mono', monospace;
            font-size: 1.15rem;
            letter-spacing: 0.03em;
        }
        .ticket__status {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.9;
        }

        .ticket__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px 0;
        }
        .ticket__table {
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            color: var(--ink-soft);
            text-transform: uppercase;
        }
        .ticket__table strong {
            font-family: 'Oswald', sans-serif;
            font-size: 1.2rem;
            color: var(--ink);
            margin-left: 4px;
        }
        .ticket__timer {
            font-family: 'Roboto Mono', monospace;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--ink-soft);
            border: 1px solid var(--paper-line);
            border-radius: 4px;
            padding: 2px 8px;
        }
        .ticket--warn .ticket__timer { color: #a8620a; border-color: #a8620a; }
        .ticket--late .ticket__timer {
            color: #fff;
            background: var(--new);
            border-color: var(--new);
            animation: pulse 1.4s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.55; }
        }
        @media (prefers-reduced-motion: reduce) {
            .ticket--late .ticket__timer { animation: none; }
        }

        .ticket__note {
            margin: 12px 16px 0;
            background: #fbe8a6;
            border-left: 3px solid #a8620a;
            padding: 8px 10px;
            font-size: 0.88rem;
            font-style: italic;
            color: #5a3d05;
        }
        .ticket__note-flag {
            display: block;
            font-style: normal;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            margin-bottom: 2px;
        }

        .ticket__items {
            list-style: none;
            margin: 14px 16px;
            padding: 10px 0 0;
            border-top: 1px dashed var(--paper-line);
            flex-grow: 1;
        }
        .ticket__items li {
            display: flex;
            gap: 8px;
            padding: 4px 0;
            font-size: 1rem;
        }
        .ticket__items .qty {
            font-family: 'Roboto Mono', monospace;
            font-weight: 700;
            min-width: 28px;
        }

        .ticket__actions {
            display: flex;
            gap: 8px;
            padding: 0 16px 16px;
        }
        .ticket__actions form { flex: 1; }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.85rem;
            color: #fff;
            transition: filter 0.15s ease, transform 0.05s ease;
        }
        .btn:hover { filter: brightness(1.1); }
        .btn:active { transform: scale(0.98); }
        .btn:focus-visible { outline: 3px solid #fff; outline-offset: 2px; }
        .btn--prep { background: var(--prep); }
        .btn--ready { background: var(--ready); }
        .btn.is-loading { opacity: 0.75; cursor: wait; }

        .pass-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 1.1rem;
            border: 1px dashed var(--steel-700);
            border-radius: 8px;
        }

        /* Sound toggle, styled like a kitchen switch */
        #btnUnlock {
            position: fixed;
            top: 22px;
            right: 22px;
            padding: 12px 18px;
            background: var(--steel-800);
            color: #fff;
            border: 1px solid var(--steel-700);
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            z-index: 9999;
        }
        #btnUnlock:hover { filter: brightness(1.15); }
    </style>
</head>
<body>

<div class="pass-header">
    <h1>Cuisine <span>LIVE</span></h1>
    <div class="pass-clock" id="passClock"></div>
</div>

<div class="kitchen-grid">
    @forelse($commandes as $commande)
    <div class="ticket {{ $commande->statut }}" data-created="{{ $commande->created_at->timestamp }}" data-statut="{{ $commande->statut }}">
        <div class="ticket__perforation"></div>
        <div class="ticket__band">
            <span class="ticket__num">#{{ str_pad($commande->id, 3, '0', STR_PAD_LEFT) }}</span>
            <span class="ticket__status">{{ str_replace('_', ' ', $commande->statut) }}</span>
        </div>

        <div class="ticket__meta">
            <div class="ticket__table">Table<strong>{{ $commande->table->numero ?? '—' }}</strong></div>
            <div class="ticket__timer" data-timer>00:00</div>
        </div>

        @if(!empty($commande->note))
            <div class="ticket__note">
                <span class="ticket__note-flag">Note client</span>
                {{ $commande->note }}
            </div>
        @endif

        <ul class="ticket__items">
            @foreach($commande->items as $item)
                <li><span class="qty">{{ $item->quantite }}×</span><span class="name">{{ $item->plat->nom ?? 'Plat supprimé' }}</span></li>
            @endforeach
        </ul>

        <div class="ticket__actions">
            @if($commande->statut == 'nouvelle')
                <form method="POST" action="/cuisine/update/{{ $commande->id }}">
                    @csrf
                    <input type="hidden" name="statut" value="en_preparation">
                    <button type="submit" class="btn btn--prep" onclick="this.classList.add('is-loading'); this.innerText='En cours…'">Lancer en préparation</button>
                </form>
            @endif

            @if($commande->statut != 'prete')
                <form method="POST" action="/cuisine/update/{{ $commande->id }}">
                    @csrf
                    <input type="hidden" name="statut" value="prete">
                    <button type="submit" class="btn btn--ready">Marquer prête</button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div class="pass-empty">Aucune commande en attente</div>
    @endforelse
</div>

<!-- AUDIO -->
<audio id="notifSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

<button id="btnUnlock">🔊 Activer le son</button>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sound = document.getElementById('notifSound');
    const btn = document.getElementById('btnUnlock');

    let unlocked = localStorage.getItem('soundUnlocked') === 'true';
    if (unlocked) btn.style.display = 'none';

    btn.onclick = function () {
        sound.play()
            .then(() => {
                sound.pause();
                sound.currentTime = 0;
                unlocked = true;
                localStorage.setItem('soundUnlocked', 'true');
                btn.style.display = 'none';
                alert("✅ Son activé");
            })
            .catch(err => console.log(err));
    };

    // Pusher
    const pusher = new Pusher(
        '{{ config("broadcasting.connections.reverb.key") }}',
        {
            wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
            wsPort: {{ config("broadcasting.connections.reverb.options.port") }},
            forceTLS: false,
            disableStats: true,
            cluster: 'mt1'
        }
    );

    const channel = pusher.subscribe('kitchen');

    channel.bind('new-order', function (e) {
        console.log("🔥 Nouvelle commande", e);

        if (localStorage.getItem('soundUnlocked') === 'true') {
            sound.currentTime = 0;
            sound.play()
                .then(() => { sound.onended = function () { location.reload(); }; })
                .catch(err => {
                    console.log("Erreur son:", err);
                    setTimeout(() => location.reload(), 5000);
                });
        } else {
            setTimeout(() => location.reload(), 5000);
        }
    });

    pusher.connection.bind('connected', () => console.log("✅ WebSocket connecté"));

    // --- Live clock in the header ---
    const clockEl = document.getElementById('passClock');
    function updateClock() {
        clockEl.textContent = new Date().toLocaleTimeString('fr-FR');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // --- Per-ticket elapsed timer, escalating color as an order ages ---
    const WARN_AFTER = 10 * 60; // seconds
    const LATE_AFTER = 15 * 60; // seconds

    function pad(n) { return n.toString().padStart(2, '0'); }

    function updateTimers() {
        document.querySelectorAll('.ticket[data-created]').forEach(ticket => {
            const created = parseInt(ticket.dataset.created, 10);
            const statut = ticket.dataset.statut;
            const timerEl = ticket.querySelector('[data-timer]');
            if (!created || !timerEl) return;

            const elapsed = Math.max(0, Math.floor(Date.now() / 1000) - created);
            const mins = Math.floor(elapsed / 60);
            const secs = elapsed % 60;
            timerEl.textContent = pad(mins) + ':' + pad(secs);

            ticket.classList.remove('ticket--warn', 'ticket--late');
            if (statut !== 'prete') {
                if (elapsed >= LATE_AFTER) ticket.classList.add('ticket--late');
                else if (elapsed >= WARN_AFTER) ticket.classList.add('ticket--warn');
            }
        });
    }
    updateTimers();
    setInterval(updateTimers, 1000);
});
</script>

</body>
</html>