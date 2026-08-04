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
/*=========================================================
=                                                         =
=       RESTO KAY-Y - CUISINE LIVE                        =
=       COMPLETE JAVASCRIPT (CORRIGÉ)                     =
=                                                         =
=========================================================*/


/*=====================================
=       CONFIGURATION                 =
=====================================*/

const sound = document.getElementById("notifSound");
const btn = document.getElementById("btnUnlock");

let unlocked = localStorage.getItem("soundUnlocked") === "true";

if(unlocked && btn){
    btn.style.display = "none";
}



/*=====================================
=       ACTIVER LE SON                =
=====================================*/

if(btn && sound){
    btn.onclick = function(){
        sound.play()
        .then(()=>{
            sound.pause();
            sound.currentTime = 0;
            localStorage.setItem("soundUnlocked", "true");
            btn.style.display = "none";
            alert("✅ Son activé");
        })
        .catch(err=>{
            console.log(err);
        });
    };
}



/*=====================================
=       PUSHER                        =
=====================================*/

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



/*=====================================
=       HORLOGE LIVE                  =
=====================================*/

const clock = document.getElementById('passClock');

function updateClock(){
    if(!clock) return;
    clock.innerHTML = new Date().toLocaleTimeString('fr-FR');
}

updateClock();
setInterval(updateClock, 1000);



/*=====================================
=       TIMER DES TICKETS             =
=====================================*/

const WARNING_TIME = 10 * 60;
const DANGER_TIME = 15 * 60;

function pad(n){
    return n.toString().padStart(2, '0');
}

function updateTicketTimers(){
    document.querySelectorAll('.ticket[data-created]').forEach(ticket => {
        const created = parseInt(ticket.dataset.created);
        const statut = ticket.dataset.statut;
        const timer = ticket.querySelector('[data-timer]');

        if(!timer) return;

        const elapsed = Math.floor(Date.now() / 1000) - created;
        const min = Math.floor(elapsed / 60);
        const sec = elapsed % 60;

        timer.innerHTML = pad(min) + ":" + pad(sec);

        ticket.classList.remove("ticket--warn", "ticket--late");

        if(statut !== "prete"){
            if(elapsed >= DANGER_TIME){
                ticket.classList.add("ticket--late");
            } else if(elapsed >= WARNING_TIME){
                ticket.classList.add("ticket--warn");
            }
        }
    });
}

updateTicketTimers();
setInterval(updateTicketTimers, 1000);



/*=====================================
=       LECTURE VOCALE                =
=====================================*/

/*=====================================
=       LECTURE VOCALE (CORRIGÉE)     =
=====================================*/

function speakOrder(e){
    console.log("📢 speakOrder te resevwa:", e);

    if(!('speechSynthesis' in window)){
        console.log("Speech API indisponible");
        return;
    }

    let plats = "";

    // Si e gen items oubyen e.commande.items
    let itemsList = e.items ? e.items : (e.commande && e.commande.items ? e.commande.items : []);

    if(Array.isArray(itemsList) && itemsList.length > 0){
        let itemsArray = [];
        itemsList.forEach(item => {
            let quantite = item.quantite ? item.quantite : 1;
            let nomPlat = item.nom ? item.nom : (item.plat && item.plat.nom ? item.plat.nom : '');
            
            if(nomPlat) {
                itemsArray.push(quantite + " " + nomPlat);
            }
        });
        plats = itemsArray.join(", ");
    }

    let numeroTable = 'N/A';
    if(e.table && e.table.numero){
        numeroTable = e.table.numero;
    } else if(e.commande && e.commande.table && e.commande.table.numero){
        numeroTable = e.commande.table.numero;
    }

    let texte = "Attention. Nouvelle commande. Table numéro " + numeroTable + ".";
    
    if(plats !== ""){
        texte += " " + plats + ".";
    }
    
    texte += " Merci.";

    console.log("Texte prononcé:", texte);

    speechSynthesis.cancel();
    const voice = new SpeechSynthesisUtterance(texte);
    voice.lang = "fr-FR";
    voice.rate = 0.9;
    voice.pitch = 1;
    voice.volume = 1;

    speechSynthesis.speak(voice);
}


/*=====================================
=       DEBUG                         =
=====================================*/

pusher.connection.bind('connected', () => {
    console.log("✅ WebSocket connecté");
});



/*=====================================
=       NOUVELLE COMMANDE             =
=====================================*/
channel.bind('new-order', function(e){
    console.log("🔥 Lòt evènman 'new-order' resevwa:", e);

    addOrderCard(e);

function finishAction(){

    speakOrder(e);

    if("speechSynthesis" in window){

        speechSynthesis.cancel();

        const plats = e.commande.items
            .map(item => item.quantite + " " + item.plat.nom)
            .join(", ");

        const texte =
            "Attention. Nouvelle commande de la table numéro " +
            e.commande.table.numero +
            ". " +
            plats +
            ". Merci.";

        const voice = new SpeechSynthesisUtterance(texte);

        voice.lang = "fr-FR";
        voice.rate = 0.9;
        voice.pitch = 1;
        voice.volume = 1;

        // 🔥 Reload sèlman lè vwa a fini
        voice.onend = function(){

            location.reload();

        };

        speechSynthesis.speak(voice);

    }else{

        location.reload();

    }

}

    if(localStorage.getItem("soundUnlocked") === "true" && sound){
        sound.currentTime = 0;
        sound.play()
        .then(function(){
            sound.onended = function(){
                finishAction();
            };
        })
        .catch(function(){
            finishAction();
        });
    } else {
        finishAction();
    }
});



/*=====================================
=       COMMANDE ACCEPTÉE             =
=====================================*/

channel.bind('order-accepted', function(e){
    console.log("👨‍🍳 Préparation", e);

    if(localStorage.getItem("soundUnlocked") === "true" && sound){
        sound.currentTime = 0;
        sound.play().catch(()=>{});
    }

    // Sipozé jere si ID a nan e.id oubyen e.commande.id
    const commandeId = e.id ? e.id : (e.commande ? e.commande.id : null);
    const card = document.getElementById("order-" + commandeId);
    
    if(card){
        card.dataset.statut = "en_preparation";
        const badge = card.querySelector(".badge");
        if(badge){
            badge.innerHTML = "👨‍🍳 En préparation";
            badge.style.background = "#fff3cd";
            badge.style.color = "#d97706";
        }
    }

    showToast("👨‍🍳 Commande #" + (commandeId || '') + " en préparation");
});



/*=====================================
=       COMMANDE PRÊTE                =
=====================================*/

channel.bind('order-ready', function(e){
    console.log("🍽️ Prête", e);

    if(localStorage.getItem("soundUnlocked") === "true" && sound){
        sound.currentTime = 0;
        sound.play().catch(()=>{});
    }

    const commandeId = e.id ? e.id : (e.commande ? e.commande.id : null);
    const card = document.getElementById("order-" + commandeId);
    
    if(card){
        card.style.transition = ".6s";
        card.style.opacity = "0";
        card.style.transform = "scale(.8)";
        setTimeout(function(){
            card.remove();
        }, 600);
    }

    showToast("✅ Commande #" + (commandeId || '') + " prête");
});



/*=====================================
=       DEBUG GLOBAL                  =
=====================================*/

channel.bind_global(function(event, data){
    console.log("📡 EVENT :", event, data);
});



/*=====================================
=       TOAST PREMIUM                 =
=====================================*/

function showToast(message){
    const toast = document.createElement("div");
    toast.className = "live-toast";
    toast.innerHTML = message;
    document.body.appendChild(toast);

    setTimeout(function(){
        toast.classList.add("show");
    }, 100);

    setTimeout(function(){
        toast.classList.remove("show");
        setTimeout(function(){
            toast.remove();
        }, 400);
    }, 3500);
}



/*=====================================
=       AJOUT D'UNE COMMANDE          =
=====================================*/

function addOrderCard(e){
    const list = document.getElementById("order-list");
    if(!list) return;

    const commandeId = e.id ? e.id : (e.commande ? e.commande.id : null);
    if(!commandeId) return;

    if(document.getElementById("order-" + commandeId)){
        return;
    }

    let plats = "";
    let itemsList = e.items ? e.items : (e.commande && e.commande.items ? e.commande.items : []);

    if(Array.isArray(itemsList)){
        itemsList.forEach(function(item){
            let nomPlat = item.nom ? item.nom : (item.plat ? item.plat.nom : 'Plat');
            plats += `
            <div class="item-row">
                <span>${nomPlat}</span>
                <b>x${item.quantite}</b>
            </div>
            `;
        });
    }

    let numeroTable = 'N/A';
    if(e.table && e.table.numero){
        numeroTable = e.table.numero;
    } else if(e.commande && e.commande.table && e.commande.table.numero){
        numeroTable = e.commande.table.numero;
    }

    const html = `
    <div
    class="ticket"
    id="order-${commandeId}"
    data-created="${Math.floor(Date.now()/1000)}"
    data-statut="nouvelle"
    >
        <div class="ticket-header">
            <h3>🍽️ Commande #${commandeId}</h3>
            <span class="badge">Nouvelle</span>
        </div>
        <div class="ticket-table">
            🪑 Table <b>${numeroTable}</b>
        </div>
        <div class="ticket-items">
            ${plats}
        </div>
        <div class="ticket-footer">
            ⏱️ <span data-timer>00:00</span>
        </div>
    </div>
    `;

    list.insertAdjacentHTML("afterbegin", html);
    updateTicketTimers();
}



/*=====================================
=       ANIMATION CARD                =
=====================================*/

document.addEventListener("animationend", function(e){
    if(e.target.classList.contains("ticket")){
        e.target.classList.remove("pulse");
    }
});



/*=====================================
=       RELOAD INTELLIGENT            =
=====================================*/

let reloadTimer = null;

function smartReload(){
    clearTimeout(reloadTimer);
    reloadTimer = setTimeout(function(){
        location.reload();
    }, 500);
}

document.addEventListener("visibilitychange", function(){
    if(document.visibilityState === "visible"){
        smartReload();
    }
});

window.addEventListener("focus", function(){
    smartReload();
});



/*=====================================
=       TEST VOCALE                   =
=====================================*/

window.testVoice = function(){
    speakOrder({
        table: {
            numero: 5
        },
        items: [
            {
                quantite: 2,
                nom: "Pizza"
            },
            {
                quantite: 1,
                nom: "Hamburger"
            }
        ]
    });
};



/*=====================================
=       FIN                           =
=====================================*/

console.log("🍽️ Cuisine LIVE Premium Ready");
</script>

</body>
</html>