@extends('client.layouts.app')


@section('title','Attente commande')



@section('content')



<div class="waiting-page">



<div class="waiting-header">


<div class="chef-animation">

👨‍🍳

</div>


<h1>

Votre commande arrive !

</h1>


<p>

Table {{ $tableId }}

</p>


</div>







<div class="order-status-card">


<h3>

Commande #{{ $commande->id ?? '---' }}

</h3>



<div class="status">

<div class="step done">
📝
<br>
Reçue
</div>


<div class="line"></div>


<div id="stepCuisine"
class="step
@if($commande->statut=='en_preparation') active @endif
@if($commande->statut=='prete') done @endif">

👨‍🍳

<br>

Cuisine

</div>


<div class="line"></div>


<div id="stepReady"
class="step
@if($commande->statut=='prete') done @endif">

🍽️

<br>

Prête

</div>



</div>



<div class="timer">

⏱️

<span id="timer">

20:00

</span>

</div>

<p id="statusText">

📝 Commande reçue

</p>



<p>

Temps moyen de préparation

</p>
<br>
<br>
<hr>

<h2>🍽 Votre commande</h2>

@if($commande && $commande->items->count())

    @foreach($commande->items as $item)

        <div class="order-item">

            <span>
                {{ $item->plat->nom }}
            </span>

            <strong>
                x {{ $item->quantite }}
            </strong>

        </div>

    @endforeach

    <hr>

    <div class="order-total">

        <strong>Total :</strong>

        {{ number_format($commande->total,2) }} HTG

    </div>

    @if($commande->note)

        <div class="order-note">

            📝 {{ $commande->note }}

        </div>

    @endif

@endif

</div>









{{-- GAME --}}


<div class="game-box">


<h2>

🎮 Petit jeu pendant l'attente

</h2>


<p>

Cliquez les plats le plus vite possible !

</p>



<div id="game">


</div>


<h3>

Score:

<span id="score">

0

</span>


</h3>


<button onclick="startGame()">

▶ Commencer

</button>



</div>








</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>

const STATUS = "{{ $commande->statut }}";
const CREATED_AT = {{ $commande->created_at->timestamp }};
const TABLE_ID = "{{ $tableId }}";
const COMMANDE_ID = "{{ $commande->id }}";


/*=====================================
=            PUSHER CONFIG            =
=====================================*/

const pusher = new Pusher(
'{{ config("broadcasting.connections.reverb.key") }}',
{
    wsHost:'{{ config("broadcasting.connections.reverb.options.host") }}',
    wsPort:{{ config("broadcasting.connections.reverb.options.port") }},
    forceTLS:false,
    disableStats:true,
    cluster:'mt1'
}
);

const channel =
pusher.subscribe(
'commande.' + COMMANDE_ID
);



/*=====================================
=            TIMER                    =
=====================================*/

const PREPARATION_TIME = 20 * 60;

let timer = null;

function updateTimer(){

    // Si deja prête
    if(document.getElementById("stepReady").classList.contains("done")){

        document.getElementById("timer").innerHTML="00:00";

        return;

    }

    let now = Math.floor(Date.now()/1000);

    let elapsed = now - CREATED_AT;

    let remaining = PREPARATION_TIME - elapsed;

    if(remaining <= 0){

        remaining = 0;

    }

    let min = Math.floor(remaining/60);

    let sec = remaining%60;

    document.getElementById("timer").innerHTML =
    min + ":" + (sec<10?"0":"") + sec;

}

updateTimer();

timer = setInterval(updateTimer,1000);



/*=====================================
= INITIAL STATUS FROM DATABASE        =
=====================================*/

if(STATUS=="en_preparation"){

    document
    .getElementById("stepCuisine")
    .classList.add("active");

    document
    .getElementById("statusText")
    .innerHTML=
    "👨‍🍳 Votre commande est en préparation";

}

if(STATUS=="prete"){

    clearInterval(timer);

    document
    .getElementById("stepCuisine")
    .classList.remove("active");

    document
    .getElementById("stepCuisine")
    .classList.add("done");

    document
    .getElementById("stepReady")
    .classList.add("done");

    document
    .getElementById("timer")
    .innerHTML="00:00";

    document
    .getElementById("statusText")
    .innerHTML=
    "🍽️ Votre commande est prête.";

}



/*=====================================
=            GAME                     =
=====================================*/

let score=0;

function startGame(){

score=0;

document
.getElementById("score")
.innerHTML=0;

let game=
document.getElementById("game");

game.innerHTML="";

const foods=[
"🍕",
"🍔",
"🍟",
"🌮",
"🍗",
"🍰",
"🍩",
"🥤"
];

for(let i=0;i<15;i++){

let btn=
document.createElement("button");

btn.className="food-btn";

btn.innerHTML=
foods[
Math.floor(
Math.random()*foods.length
)
];

btn.onclick=function(){

score++;

document
.getElementById("score")
.innerHTML=score;

this.remove();

};

game.appendChild(btn);

}

}



/*=====================================
=            MENU                     =
=====================================*/

function goMenu(){

window.location.href=
"/menu/"+TABLE_ID;

}



/*=====================================
=            CART                     =
=====================================*/

function openCart(){

const panel=
document.getElementById("cartPanel");

const overlay=
document.getElementById("cartOverlay");

if(panel){

panel.classList.add("show");

}

if(overlay){

overlay.classList.add("show");

}

document.body.classList.add("cart-open");

}



/*=====================================
=            TOAST                    =
=====================================*/

function showToast(message){

let box=
document.createElement("div");

box.className="toast";

box.innerHTML=message;

document.body.appendChild(box);

setTimeout(function(){

box.remove();

},3000);

}



/*=====================================
=      ORDER ACCEPTED EVENT           =
=====================================*/

channel.bind(
'order-accepted',
function(e){

document
.getElementById("stepCuisine")
.classList.add("active");

document
.getElementById("statusText")
.innerHTML=
"👨‍🍳 Votre commande est en préparation";

showToast(
"👨‍🍳 Le chef prépare votre commande."
);

}
);



/*=====================================
=        ORDER READY EVENT            =
=====================================*/

channel.bind(
'order-ready',
function(e){

clearInterval(timer);

document
.getElementById("stepCuisine")
.classList.remove("active");

document
.getElementById("stepCuisine")
.classList.add("done");

document
.getElementById("stepReady")
.classList.add("done");

document
.getElementById("statusText")
.innerHTML=
"🍽️ Votre commande est prête.";

document
.getElementById("timer")
.innerHTML="00:00";

showToast(
"🍽️ Votre commande est prête."
);

setTimeout(function(){

Swal.fire({

icon:'success',

title:'Commande prête',

text:'Le serveur va vous apporter votre commande.',

confirmButtonText:'OK'

});

},500);

}
);



/*=====================================
=       PUSHER DEBUG                  =
=====================================*/

pusher.connection.bind(
'connected',
function(){

console.log(
'✅ Connected'
);

});

channel.bind_global(function(event,data){

console.log(
'Event:',
event,
data
);

});

</script>

<style>
/* ============================= */
/* MOBILE NAVIGATION */
/* ============================= */
.status{
    display:flex;
    align-items:center;
    justify-content:center;
    margin:30px 0;
}

.step{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#ececec;
    color:#888;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    transition:.4s;
}

.line{
    flex:1;
    height:4px;
    background:#ddd;
}

/* Etape active */

.step.active{

    background:#ff9800;

    color:#fff;

    transform:scale(1.08);

    box-shadow:0 0 20px rgba(255,152,0,.45);

}

/* Etape terminée */

.step.done{

    background:#28a745;

    color:#fff;

    transform:scale(1.08);

    box-shadow:0 0 20px rgba(40,167,69,.45);

}
.mobile-nav{

    position:fixed;

    bottom:0;
    left:0;
    right:0;

    height:75px;

    background:white;

    display:flex;

    justify-content:space-around;

    align-items:center;

    border-radius:25px 25px 0 0;

    box-shadow:
    0 -8px 25px rgba(0,0,0,.12);


    /* IMPORTANT */
    z-index:900;


    padding:8px 10px;

    transition:.3s ease;

}



/* Lè panier ouvè */

body.cart-open .mobile-nav{

    transform:translateY(120%);

}

.floating-cart {
    position: fixed;
    bottom: 95px; /* Mete l yon ti kras anwo mobile-nav la pou l pa kole ak li */
    right: 20px;
    background: #ff5e00;
    color: white;
    padding: 10px 18px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    z-index: 890; /* Dèyè mobile-nav la (900) oswa ajiste l selon bezwen w */
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.floating-cart:hover {
    transform: scale(1.05);
}

.floating-cart .cart-icon {
    position: relative;
    font-size: 20px;
}

.floating-cart #cartCount {
    position: absolute;
    top: -8px;
    right: -10px;
    background: #ff4757;
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 50%;
    font-weight: bold;
}

.floating-cart .cart-text {
    display: flex;
    flex-direction: column;
}

.floating-cart .cart-text small {
    font-size: 10px;
    opacity: 0.9;
}

.floating-cart .cart-text strong {
    font-size: 13px;
}

/* Lè panye a louvri, nou ka fè floating-cart la disparèt tou pou l pa rete sou tèt panye a */
body.cart-open .floating-cart {
    transform: translateY(100px);
    opacity: 0;
    pointer-events: none;
}

/* BUTTON */

.mobile-nav button{

    flex:1;

    border:none;

    background:transparent;

    display:flex;

    flex-direction:column;

    align-items:center;

    justify-content:center;

    gap:5px;

    color:#777;

    cursor:pointer;

    transition:.3s;

    font-size:12px;

}



/* ICON */

.mobile-nav i{

    font-size:22px;

    transition:.3s;

}

.order-item{

display:flex;

justify-content:space-between;

padding:12px 0;

border-bottom:1px solid #eee;

font-size:16px;

}

.order-total{

margin-top:15px;

font-size:20px;

font-weight:bold;

color:#ff6b00;

}

.order-note{

margin-top:15px;

background:#fff3cd;

padding:15px;

border-radius:10px;

}

/* TEXT */

.mobile-nav span{

    font-size:12px;

    font-weight:600;

}



/* HOVER */

.mobile-nav button:hover{

    color:#ff5e00;

}


.mobile-nav button:hover i{

    transform:translateY(-5px);

    color:#ff5e00;

}



/* ACTIVE */

.mobile-nav button.active{

    color:#ff5e00;

}


.mobile-nav button.active i{

    background:#ff5e00;

    color:white;

    width:45px;

    height:45px;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

}



/* CART NOTIFICATION */

.mobile-nav button:nth-child(2){

    position:relative;

}



.mobile-nav button:nth-child(2)::after{

    content:"";

    position:absolute;

    top:8px;

    right:25px;

    width:10px;

    height:10px;

    background:#ff4757;

    border-radius:50%;

    display:none;

}



/* BODY */

body{

    padding-bottom:100px;

}



/* MOBILE ONLY */

@media(max-width:768px){

    .mobile-nav{

        display:flex;

    }

}



/* DESKTOP HIDE */

@media(min-width:769px){

    .mobile-nav{

        display:none;

    }

}
</style>

@endsection