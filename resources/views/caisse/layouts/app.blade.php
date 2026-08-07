<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>

        Caisse — Resto Kay-Y

    </title>

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Yeseva+One&family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;600;700&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* =====================================================
   RESTO KAY-Y — SYSTÈME DE DESIGN "CAISSE"
   Identité: comptoir de resto, laiton, épices, ticket de caisse.
===================================================== */
:root{
    /* fonds & structure — bois brûlé / espresso, pas de bleu marine corporate */
    --ink:#241C15;
    --ink-2:#2E2318;
    --ink-3:#3B2C1E;
    --paper:#FAF6EF;
    --paper-soft:#F3EBDC;
    --card:#FFFFFF;
    --line:#E8DEC9;
    --text:#241C15;
    --muted:#8A7A63;

    /* accents — laiton, paprika, feuille d'olivier, aubergine */
    --gold:#B8862F;
    --gold-deep:#8F6620;
    --gold-light:#F1DDA6;
    --paprika:#C1440E;
    --paprika-light:#F7DCC9;
    --olive:#4B6B3A;
    --olive-light:#DCE7CE;
    --wine:#8C2A2A;
    --plum:#6C4675;
    --plum-light:#EADCEF;

    --radius-lg:22px;
    --radius-md:16px;
    --shadow-soft:0 10px 28px rgba(36,28,21,.08);
    --shadow-lift:0 16px 34px rgba(36,28,21,.14);

    --font-display:'Yeseva One',serif;
    --font-body:'Inter',sans-serif;
    --font-mono:'IBM Plex Mono',monospace;
}
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:var(--font-body);
}
body{
background:var(--paper);
color:var(--text);
display:flex;
min-height:100vh;
overflow-x:hidden;
}
/* montants, identifiants, heures : toujours en mono, esprit "reçu imprimé" */
.amount,
.table-number,
.premium-table td:first-child strong,
#liveClock b,
.facture-number strong,
.resume-card strong,
.rank-price{
    font-family:var(--font-mono);
    letter-spacing:.2px;
}
/***********************
SIDEBAR
************************/
.sidebar{
position:fixed;
left:0;
top:0;
width:270px;
height:100%;
background:linear-gradient(
180deg,
var(--ink),
var(--ink-2) 55%,
var(--ink-3));
color:#F1E9D8;
padding:25px;
display:flex;
flex-direction:column;
z-index:999;
box-shadow:8px 0 30px rgba(0,0,0,.25);
border-right:1px solid rgba(241,221,166,.08);
}
.logo{
display:flex;
align-items:center;
gap:15px;
margin-bottom:40px;
padding-bottom:22px;
border-bottom:1px solid rgba(241,221,166,.12);
}
.logo-icon{
width:58px;
height:58px;
border-radius:16px;
background:linear-gradient(
135deg,
var(--gold-light),
var(--gold));
display:flex;
align-items:center;
justify-content:center;
font-size:26px;
color:var(--ink);
box-shadow:0 10px 20px rgba(184,134,47,.35);
}
.logo-text h2{
font-family:var(--font-display);
font-size:21px;
font-weight:400;
letter-spacing:.5px;
color:#FBF6E9;
}
.logo-text span{
font-size:12px;
color:#C9B896;
letter-spacing:.3px;
}
.menu{
display:flex;
flex-direction:column;
gap:8px;
margin-top:10px;
}
.menu a{
display:flex;
align-items:center;
gap:15px;
padding:14px 18px;
text-decoration:none;
color:#D8CCB4;
border-radius:14px;
transition:.3s;
font-size:15px;
font-weight:500;
}
.menu a:hover{
background:rgba(241,221,166,.08);
color:#FBF6E9;
transform:translateX(4px);
}
.menu a.active{
background:linear-gradient(
135deg,
var(--gold),
var(--gold-deep));
color:#1C1409;
font-weight:700;
box-shadow:0 10px 22px rgba(184,134,47,.3);
}
.menu i{
width:22px;
font-size:17px;
text-align:center;
}
.sidebar-bottom{
margin-top:auto;
padding-top:20px;
border-top:1px solid rgba(241,221,166,.1);
}
.logout{
background:rgba(140,42,42,.18)!important;
color:#F3B8B8!important;
}
.logout:hover{
background:var(--wine)!important;
color:#fff!important;
}
.main{
margin-left:270px;
flex:1;
display:flex;
flex-direction:column;
min-height:100vh;
}
.topbar{
height:85px;
background:var(--card);
display:flex;
align-items:center;
justify-content:space-between;
padding:0 35px;
box-shadow:0 5px 20px rgba(36,28,21,.05);
position:sticky;
top:0;
z-index:100;
border-bottom:1px solid var(--line);
}
.left-top{
display:flex;
align-items:center;
gap:20px;
}
.left-top h1{
font-family:var(--font-display);
font-size:26px;
font-weight:400;
color:var(--ink);
}
.left-top small{
display:block;
color:var(--muted);
font-size:13px;
margin-top:3px;
}
.right-top{
display:flex;
align-items:center;
gap:20px;
}
.search-box{
position:relative;
}
.search-box input{
width:320px;
padding:12px 20px 12px 45px;
border:1px solid var(--line);
outline:none;
background:var(--paper-soft);
border-radius:40px;
font-size:14px;
transition:.3s;
color:var(--text);
}
.search-box input:focus{
background:white;
border-color:var(--gold);
box-shadow:0 0 0 3px rgba(184,134,47,.15);
}
.search-box i{
position:absolute;
left:18px;
top:50%;
transform:translateY(-50%);
color:var(--muted);
}
.sr-only{
position:absolute;
width:1px;
height:1px;
padding:0;
margin:-1px;
overflow:hidden;
clip:rect(0,0,0,0);
white-space:nowrap;
border:0;
}
.icon-btn{
position:relative;
width:48px;
height:48px;
border-radius:50%;
border:none;
display:flex;
align-items:center;
justify-content:center;
background:var(--paper-soft);
color:var(--ink);
cursor:pointer;
font-size:19px;
transition:.3s;
}
.icon-btn:hover{
background:var(--gold);
color:#1C1409;
transform:translateY(-3px);
}
.badge{
position:absolute;
top:3px;
right:2px;
width:18px;
height:18px;
border-radius:50%;
background:var(--paprika);
color:white;
font-size:11px;
display:flex;
align-items:center;
justify-content:center;
font-weight:bold;
}
.user{
display:flex;
align-items:center;
gap:15px;
padding-left:20px;
border-left:1px solid var(--line);
}
.avatar{
width:50px;
height:50px;
border-radius:50%;
background:linear-gradient(
135deg,
var(--gold-light),
var(--gold));
display:flex;
align-items:center;
justify-content:center;
font-family:var(--font-display);
font-size:19px;
color:var(--ink);
box-shadow:0 8px 18px rgba(184,134,47,.3);
}
.user h3{
font-size:15px;
color:var(--ink);
}
.user small{
display:block;
color:var(--muted);
margin-top:2px;
font-size:12px;
}
.page{
padding:35px;
flex:1;
}
.footer{
background:var(--card);
padding:18px;
text-align:center;
color:var(--muted);
font-size:13px;
border-top:1px solid var(--line);
}
.mobile-toggle{
display:none;
font-size:26px;
cursor:pointer;
background:none;
border:none;
color:inherit;
padding:0;
}
@media(max-width:1100px){
.search-box input{
width:220px;
}
}
@media(max-width:900px){
.sidebar{
left:-270px;
transition:.35s;
}
.sidebar.show{
left:0;
}
.main{
margin-left:0;
}
.mobile-toggle{
display:block;
}
.search-box{
display:none;
}
.topbar{
padding:0 20px;
}
.left-top h1{
font-size:21px;
}
.page{
padding:20px;
}
}
@media(max-width:600px){
.user h3,
.user small{
display:none;
}
.right-top{
gap:10px;
}
}
</style>

</head>

<body>

<div
    class="sidebar"
    id="sidebar">

    <div class="logo">

        <div class="logo-icon">

            <i class="fa-solid fa-cash-register"></i>

        </div>

        <div class="logo-text">

            <h2>Kay-Y</h2>

            <span>Caisse du restaurant</span>

        </div>

    </div>

    <div class="menu">

        <a
            href="{{ url('/caisse/dashboard') }}"
            class="{{ request()->is('caisse/dashboard') ? 'active' : '' }}">

            <i class="fa-solid fa-chart-line"></i>

            Tableau de bord

        </a>

        <a
            href="{{ url('/caisse/paiements') }}"
            class="{{ request()->is('caisse/paiements*') ? 'active' : '' }}">

            <i class="fa-solid fa-credit-card"></i>

            Paiements

        </a>

        <a
            href="{{ url('/caisse/commandes') }}"
            class="{{ request()->is('caisse/commandes*') ? 'active' : '' }}">

            <i class="fa-solid fa-receipt"></i>

            Commandes prêtes

        </a>

        <a
            href="{{ url('/caisse/historique') }}"
            class="{{ request()->is('caisse/historique*') ? 'active' : '' }}">

            <i class="fa-solid fa-clock-rotate-left"></i>

            Historique

        </a>

        <a
            href="{{ url('/admin/dashboard') }}">

            <i class="fa-solid fa-arrow-left"></i>

            Administration

        </a>

    </div>

    <div class="sidebar-bottom">

        <a
            href="{{ route('logout') }}"
            class="menu logout"
            onclick="event.preventDefault();document.getElementById('logout-form').submit();">

            <i class="fa-solid fa-right-from-bracket"></i>

            Déconnexion

        </a>

        <form
            id="logout-form"
            action="{{ route('logout') }}"
            method="POST"
            style="display:none">

            @csrf

        </form>

    </div>

</div>

<div class="main">

    <div class="topbar">

        <div class="left-top">

            <button
                type="button"
                class="fa-solid fa-bars mobile-toggle"
                id="toggleSidebar"
                aria-label="Afficher/masquer le menu"
                aria-expanded="false"
                aria-controls="sidebar"></button>

            <div>

                <h1>

                    @yield('title','Tableau de bord')

                </h1>

                <small>

                    Gestion des paiements du restaurant

                </small>

            </div>

        </div>

        <div class="right-top">

            <div class="search-box">

                <label for="topbarSearch" class="sr-only">Rechercher une commande</label>

                <i class="fa-solid fa-search" aria-hidden="true"></i>

                <input
                    id="topbarSearch"
                    type="search"
                    name="q"
                    placeholder="Rechercher une commande...">

            </div>

            <button type="button" class="icon-btn" aria-label="Notifications (3 non lues)">

                <i class="fa-solid fa-bell" aria-hidden="true"></i>

                <span class="badge" aria-hidden="true">3</span>

            </button>

            <button type="button" class="icon-btn" aria-label="Portefeuille / caisse">

                <i class="fa-solid fa-wallet" aria-hidden="true"></i>

            </button>

            <div class="user">

                <div class="avatar">

                    {{ strtoupper(substr(auth()->user()->name ?? 'C',0,1)) }}

                </div>

                <div>

                    <h3>

                        {{ auth()->user()->name ?? 'Caissier' }}

                    </h3>

                    <small>

                        Caissier

                    </small>

                </div>

            </div>

        </div>

    </div>

    <div class="page">

        @yield('content')

    </div>

    <div class="footer">

        © {{ date('Y') }}
        Restaurant Kay-Y —
        Module Caisse

    </div>

</div>

<script>

const sidebar=document.getElementById("sidebar");
const toggle=document.getElementById("toggleSidebar");

if(toggle){
    toggle.onclick=function(){
        const isOpen=sidebar.classList.toggle("show");
        toggle.setAttribute("aria-expanded",isOpen);
    };
}

/*=========================
HORLOGE
=========================*/

function updateClock(){

let now=new Date();

let options={

weekday:'long',

day:'2-digit',

month:'long',

year:'numeric'

};

let date=

now.toLocaleDateString(
'fr-FR',
options
);

let heure=

now.toLocaleTimeString(
'fr-FR'
);

let el=
document.getElementById(
"liveClock"
);

if(el){

el.innerHTML=
date+
"<br><b>"+heure+"</b>";

}

}

updateClock();

setInterval(
updateClock,
1000
);

/*=========================
NOTIFICATIONS
=========================*/

function showNotification(

title,

message,

color="#B8862F"

){

const notif=

document.createElement(
"div"
);

notif.style.position="fixed";

notif.style.top="30px";

notif.style.right="30px";

notif.style.width="340px";

notif.style.background="white";

notif.style.borderLeft=

"6px solid "+color;

notif.style.padding="18px";

notif.style.borderRadius="15px";

notif.style.boxShadow=

"0 15px 35px rgba(36,28,21,.18)";

notif.style.zIndex="99999";

notif.style.opacity="0";

notif.style.transform=

"translateX(120px)";

notif.style.transition=".45s";

notif.innerHTML=

"<h3 style='margin-bottom:6px;font-family:Inter,sans-serif'>"

+title+

"</h3><div>"+

message+

"</div>";

document.body.appendChild(
notif
);

setTimeout(function(){

notif.style.opacity="1";

notif.style.transform=

"translateX(0)";

},100);

setTimeout(function(){

notif.style.opacity="0";

notif.style.transform=

"translateX(120px)";

setTimeout(function(){

notif.remove();

},500);

},4500);

}

/*=========================
COMPTEURS
=========================*/

function animateCounter(

element,

end,

duration=1200

){

if(!element)return;

let start=0;

let increment=

end/

(duration/16);

let timer=

setInterval(function(){

start+=increment;

if(start>=end){

start=end;

clearInterval(timer);

}

element.innerHTML=

Math.floor(start)

.toLocaleString();

},16);

}

/*=========================
CARTES
=========================*/

document

.querySelectorAll(

".stat-card"

)

.forEach(function(card){

card.addEventListener(

"mouseenter",

function(){

card.style.transform=

"translateY(-8px)";

});

card.addEventListener(

"mouseleave",

function(){

card.style.transform=

"translateY(0)";

});

});

/*=========================
TABLE
=========================*/

document

.querySelectorAll(

"tbody tr"

)

.forEach(function(row){

row.addEventListener(

"mouseenter",

function(){

row.style.background=

"#FBF6E9";

});

row.addEventListener(

"mouseleave",

function(){

row.style.background=

"";

});

});
/*=========================
PUSHER / ECHO
=========================*/

if(window.Echo){

window.Echo
.channel("kitchen")

.listen(".new-order",(e)=>{

showNotification(

"Nouvelle commande",

"Une nouvelle commande vient d'arriver.",

"#4B6B3A"

);

playNotification();

})

.listen(".accepted",(e)=>{

showNotification(

"Commande acceptée",

"La cuisine prépare la commande #"+e.commande.id,

"#C1440E"

);

})

.listen(".ready",(e)=>{

showNotification(

"Commande prête",

"La commande #"+e.commande.id+" est prête à être encaissée.",

"#B8862F"

);

playNotification();

});

}

/*=========================
SON
=========================*/

function playNotification(){

const audio=

document.getElementById(
"notifSound"
);

if(audio){

audio.currentTime=0;

audio.play().catch(()=>{});

}

}

/*=========================
DARK MODE
=========================*/

const darkBtn=document.getElementById("darkMode");

const DARK_CLASS="dark-mode";
const DARK_STORAGE_KEY="caisse-dark";

function setDarkMode(enabled){
    document.body.classList.toggle(DARK_CLASS,enabled);
    localStorage.setItem(DARK_STORAGE_KEY,enabled);
    if(darkBtn){
        darkBtn.setAttribute("aria-pressed",enabled);
    }
}

if(darkBtn){
    darkBtn.onclick=function(){
        setDarkMode(!document.body.classList.contains(DARK_CLASS));
    };
}

if(localStorage.getItem(DARK_STORAGE_KEY)=="true"){
    setDarkMode(true);
}

/*=========================
CHARTS — palette resto (laiton / paprika)
=========================*/

Chart.defaults.font.family="Inter, sans-serif";
Chart.defaults.color="#8A7A63";

if(document.getElementById("salesChart")){

const ctx=

document
.getElementById("salesChart")
.getContext("2d");

const gradient=ctx.createLinearGradient(0,0,0,220);
gradient.addColorStop(0,"rgba(184,134,47,.35)");
gradient.addColorStop(1,"rgba(184,134,47,0)");

new Chart(ctx,{

type:"line",

data:{

labels:[],

datasets:[{

label:"Ventes",

data:[],

borderColor:"#B8862F",

backgroundColor:gradient,

fill:true,

borderWidth:3,

tension:.4

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

},

scales:{

y:{

beginAtZero:true

}

}

}

});

}

/*=========================
TOOLTIPS
=========================*/

document

.querySelectorAll("[data-tooltip]")

.forEach(function(el){

el.addEventListener("mouseenter",function(){

const tip=

document.createElement("div");

tip.className="tooltip-box";

tip.innerHTML=

el.dataset.tooltip;

document.body.appendChild(tip);

const r=

el.getBoundingClientRect();

tip.style.left=

r.left+"px";

tip.style.top=

(r.top-40)+"px";

el._tooltip=tip;

});

el.addEventListener("mouseleave",function(){

if(el._tooltip){

el._tooltip.remove();

}

});

});
/*=========================
INITIALISATION
=========================*/

document.addEventListener("DOMContentLoaded",function(){

    updateClock();

    showNotification(
        "Bienvenue",
        "Module Caisse Kay-Y prêt.",
        "#4B6B3A"
    );

});

</script>

<!-- AUDIO NOTIFICATION -->

<audio
    id="notifSound"
    preload="auto">

    <source
        src="{{ asset('sounds/notification.mp3') }}"
        type="audio/mpeg">

</audio>

<!-- HORLOGE FLOTTANTE -->

<div
    id="liveClock"
    style="
        position:fixed;
        bottom:25px;
        right:25px;
        background:white;
        padding:15px 20px;
        border-radius:18px;
        box-shadow:0 10px 30px rgba(36,28,21,.14);
        text-align:center;
        font-size:13px;
        color:#5A4C3B;
        z-index:999;
        min-width:180px;
        border:1px solid #E8DEC9;
    ">

</div>

<!-- BOUTON DARK MODE -->

<button
    id="darkMode"
    type="button"
    aria-label="Basculer le mode sombre"
    aria-pressed="false"
    style="
        position:fixed;
        bottom:120px;
        right:25px;
        width:58px;
        height:58px;
        border:none;
        border-radius:50%;
        cursor:pointer;
        background:#241C15;
        color:#F1DDA6;
        font-size:21px;
        box-shadow:0 10px 25px rgba(36,28,21,.25);
        transition:.35s;
        z-index:999;
    ">

    <i class="fa-solid fa-moon" aria-hidden="true"></i>

</button>

<!-- BOUTON RETOUR HAUT -->

<button
    id="scrollTop"
    type="button"
    aria-label="Retourner en haut de la page"
    style="
        position:fixed;
        bottom:195px;
        right:25px;
        width:54px;
        height:54px;
        border:none;
        border-radius:50%;
        cursor:pointer;
        background:#B8862F;
        color:#1C1409;
        font-size:19px;
        display:none;
        box-shadow:0 10px 25px rgba(184,134,47,.35);
        z-index:999;
    ">

    <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>

</button>

<script>

/*=========================
SCROLL TOP
=========================*/

const scrollBtn =
document.getElementById("scrollTop");

window.addEventListener("scroll",function(){

    if(window.scrollY>300){

        scrollBtn.style.display="block";

    }else{

        scrollBtn.style.display="none";

    }

});

scrollBtn.onclick=function(){

    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

};

</script>
<style>
/* =====================================================
   RESTO KAY-Y - COMPOSANTS "CAISSE"
===================================================== */
/* ==============================
   GLOBAL CAISSE
============================== */
.caisse-container{
    padding:25px;
    background:var(--paper);
    min-height:100vh;
}
.caisse-title{
    font-family:var(--font-display);
    font-size:26px;
    font-weight:400;
    color:var(--ink);
}
.caisse-subtitle{
    color:var(--muted);
    margin-top:5px;
}
/* ==============================
   CARTE DE BIENVENUE
============================== */
.dashboard{
    display:flex;
    flex-direction:column;
    gap:25px;
}
.welcome-card{
    background:linear-gradient(120deg,var(--ink) 0%,var(--ink-3) 100%);
    color:#F6EFDE;
    border-radius:var(--radius-lg);
    padding:32px 34px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:var(--shadow-lift);
    position:relative;
    overflow:hidden;
}
.welcome-card::after{
    content:"";
    position:absolute;
    inset:0;
    background:radial-gradient(circle at 85% 20%,rgba(184,134,47,.28),transparent 55%);
    pointer-events:none;
}
.welcome-card h2{
    font-family:var(--font-display);
    font-weight:400;
    font-size:26px;
    margin-bottom:8px;
}
.welcome-card p{
    color:#D9CBAE;
    max-width:480px;
    font-size:14.5px;
    line-height:1.5;
}
.welcome-icon{
    font-size:52px;
    color:var(--gold-light);
    opacity:.85;
    z-index:1;
}
/* ==============================
   STATISTICS CARDS
============================== */
.stats-grid{
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
    margin-top:0;
}
.stat-card{
    background:var(--card);
    border-radius:var(--radius-lg);
    padding:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:var(--shadow-soft);
    transition:.3s;
    overflow:hidden;
    position:relative;
    border:1px solid var(--line);
}
.stat-card:hover{
    transform:translateY(-5px);
    box-shadow:var(--shadow-lift);
}
.stat-card .icon{
    width:52px;
    height:52px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    flex-shrink:0;
    margin-right:14px;
}
.stat-card > div:last-child{
    flex:1;
}
.stat-card span{
    color:var(--muted);
    font-size:13px;
}
.stat-card h2{
    margin:6px 0;
    font-family:var(--font-mono);
    font-size:26px;
    font-weight:700;
    color:var(--ink);
}
.stat-card small{
    color:var(--muted);
    font-size:12px;
}
.stat-card i{
    font-size:38px;
    opacity:.22;
}
/* variantes — tuiles du tableau de bord */
.stat-card.revenue{ border-left:5px solid var(--gold); }
.stat-card.revenue .icon{ background:var(--gold-light); color:var(--gold-deep); }
.stat-card.orders{ border-left:5px solid var(--paprika); }
.stat-card.orders .icon{ background:var(--paprika-light); color:var(--paprika); }
.stat-card.attente{ border-left:5px solid var(--plum); }
.stat-card.attente .icon{ background:var(--plum-light); color:var(--plum); }
.stat-card.paiement{ border-left:5px solid var(--olive); }
.stat-card.paiement .icon{ background:var(--olive-light); color:var(--olive); }
/* variantes génériques (autres pages) */
.stat-card.success{ border-left:6px solid var(--olive); }
.stat-card.primary{ border-left:6px solid var(--gold); }
.stat-card.warning{ border-left:6px solid var(--paprika); }
.stat-card.danger{ border-left:6px solid var(--wine); }
.stat-card.purple{ border-left:6px solid var(--plum); }
/* ==============================
   MINI STATS (répartition rapide)
============================== */
.mini-stats{
    display:grid;
    grid-template-columns:1fr;
    gap:14px;
}
.mini-card{
    background:var(--card);
    border-radius:16px;
    padding:18px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:var(--shadow-soft);
    border:1px solid var(--line);
    border-left:4px solid var(--line);
}
.mini-card small{
    color:var(--muted);
    display:block;
    margin-bottom:4px;
    font-size:12px;
}
.mini-card h2{
    font-family:var(--font-mono);
    font-size:20px;
    color:var(--ink);
}
.mini-card i{
    font-size:26px;
    opacity:.5;
}
.mini-card.success{ border-left-color:var(--olive); }
.mini-card.success i{ color:var(--olive); }
.mini-card.primary{ border-left-color:var(--gold); }
.mini-card.primary i{ color:var(--gold-deep); }
.mini-card.warning{ border-left-color:var(--paprika); }
.mini-card.warning i{ color:var(--paprika); }
.mini-card.purple{ border-left-color:var(--plum); }
.mini-card.purple i{ color:var(--plum); }
/* ==============================
   ANALYTICS GRID
============================== */
.analytics-grid{
    display:grid;
    grid-template-columns:
    2fr 1fr;
    gap:20px;
    margin-top:0;
}
.analytics-grid.two,
.analytics-grid.two-columns{
    grid-template-columns:
    repeat(2,1fr);
}
.chart-card,
.table-card{
    background:var(--card);
    border-radius:var(--radius-lg);
    padding:25px;
    box-shadow:var(--shadow-soft);
    border:1px solid var(--line);
}
.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.card-header h3{
    margin:0;
    font-size:17px;
    font-weight:700;
    color:var(--ink);
}
.card-header small{
    color:var(--muted);
    font-size:12.5px;
    display:block;
    margin-top:3px;
}
.card-header i{
    color:var(--gold);
    margin-right:8px;
}
.count-badge{
    background:var(--paper-soft);
    color:var(--ink);
    padding:8px 14px;
    border-radius:30px;
    font-size:13px;
    font-weight:600;
    border:1px solid var(--line);
}
/* =====================================================
   TABLES PREMIUM
===================================================== */
.table-responsive{
    overflow-x:auto;
}
.premium-table{
    width:100%;
    border-collapse:collapse;
}
.premium-table thead{
    background:var(--paper-soft);
}
.premium-table th{
    text-align:left;
    padding:14px 15px;
    font-size:11.5px;
    text-transform:uppercase;
    color:var(--muted);
    letter-spacing:.6px;
    font-weight:700;
}
.premium-table td{
    padding:16px 15px;
    border-bottom:1px solid var(--line);
    color:var(--text);
    font-size:14px;
}
.premium-table tbody tr{
    transition:.25s;
}
.premium-table tbody tr:hover{
    background:#FBF6E9;
}
/* ==============================
   MONTANT
============================== */
.amount{
    color:var(--olive);
    font-size:15.5px;
    font-weight:600;
}
/* ==============================
   TABLE NUMBER
============================== */
.table-number{
    background:var(--gold-light);
    color:var(--gold-deep);
    padding:7px 12px;
    border-radius:10px;
    font-weight:700;
    display:inline-block;
    font-size:13px;
}
/* ==============================
   BADGES — effet "tampon de cuisine"
============================== */
.badge-ready{
    background:var(--olive-light);
    color:#33502A;
    padding:7px 14px;
    border-radius:30px;
    font-size:12.5px;
    font-weight:700;
    letter-spacing:.3px;
    display:inline-flex;
    align-items:center;
    gap:6px;
    border:1px solid rgba(75,107,58,.25);
}
.badge-mode{
    background:var(--paper-soft);
    color:var(--muted);
    padding:7px 12px;
    border-radius:30px;
    font-size:12.5px;
    border:1px solid var(--line);
}
.badge{
    padding:7px 12px;
    border-radius:30px;
    font-weight:700;
    font-size:12.5px;
}
.badge-success{
    background:var(--olive-light);
    color:#33502A;
}
.badge-primary{
    background:var(--gold-light);
    color:var(--gold-deep);
}
.badge-warning{
    background:var(--paprika-light);
    color:#8F350B;
}
.badge-purple{
    background:var(--plum-light);
    color:var(--plum);
}
/* ==============================
   BOUTON ENCAISSER
============================== */
.btn-pay{
    background:
    linear-gradient(
    135deg,
    var(--olive),
    #5C8748
    );
    color:white;
    padding:11px 20px;
    border-radius:14px;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-weight:700;
    font-size:14px;
    border:none;
    cursor:pointer;
    transition:.3s;
    box-shadow:
    0 8px 20px rgba(75,107,58,.28);
}
.btn-pay:hover{
    transform:translateY(-3px);
    color:white;
    box-shadow:
    0 12px 25px rgba(75,107,58,.38);
}
/* ==============================
   PAYMENT SUMMARY
============================== */
.payment-summary{
    display:flex;
    flex-direction:column;
    gap:12px;
}
.payment-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 18px;
    background:var(--paper-soft);
    border-radius:14px;
    transition:.3s;
}
.payment-item:hover{
    background:var(--gold-light);
    transform:translateX(5px);
}
.payment-item strong{
    font-size:19px;
    color:var(--ink);
}
/* ==============================
   EMPTY STATE
============================== */
.empty{
    text-align:center;
    padding:35px;
    color:var(--muted);
    font-weight:600;
}
/* =====================================================
   BEST SELLING - CLASSEMENT DES VENTES
===================================================== */
.sales-ranking{
    display:flex;
    flex-direction:column;
    gap:14px;
}
.rank-item{
    display:flex;
    align-items:center;
    gap:15px;
    background:var(--paper-soft);
    padding:14px;
    border-radius:16px;
    transition:.3s;
}
.rank-item:hover{
    background:var(--gold-light);
    transform:translateX(6px);
}
.rank-number{
    width:40px;
    height:40px;
    border-radius:50%;
    background:var(--ink);
    color:var(--gold-light);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:16px;
    font-family:var(--font-mono);
}
.rank-info{
    flex:1;
    display:flex;
    flex-direction:column;
}
.rank-info strong{
    color:var(--ink);
    font-size:15px;
}
.rank-info small{
    color:var(--muted);
    margin-top:3px;
}
.rank-price{
    font-weight:800;
    color:var(--olive);
}
/* =====================================================
   ACTIVITE TEMPS REEL
===================================================== */
.activity-list{
    display:flex;
    flex-direction:column;
    gap:14px;
}
.activity-item{
    display:flex;
    align-items:center;
    gap:15px;
    padding:14px;
    background:var(--paper-soft);
    border-radius:16px;
}
.activity-icon{
    width:40px;
    height:40px;
    background:var(--olive-light);
    color:var(--olive);
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
}
.activity-item strong{
    display:block;
    color:var(--text);
}
.activity-item small{
    color:var(--muted);
}
/* =====================================================
   PERFORMANCE CARDS
===================================================== */
.performance-grid{
    display:grid;
    grid-template-columns:
    repeat(4,1fr);
    gap:20px;
    margin-top:25px;
}
.performance-card{
    background:var(--card);
    padding:24px;
    border-radius:var(--radius-lg);
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:var(--shadow-soft);
    transition:.3s;
    border:1px solid var(--line);
}
.performance-card:hover{
    transform:translateY(-6px);
}
.performance-card small{
    color:var(--muted);
    display:block;
    margin-bottom:8px;
}
.performance-card h2{
    margin:0;
    font-family:var(--font-mono);
    font-size:22px;
    color:var(--ink);
}
.performance-card i{
    font-size:32px;
    opacity:.22;
}
.performance-card.success{ border-bottom:5px solid var(--olive); }
.performance-card.primary{ border-bottom:5px solid var(--gold); }
.performance-card.warning{ border-bottom:5px solid var(--paprika); }
.performance-card.purple{ border-bottom:5px solid var(--plum); }
/* =====================================================
   FOOTER CAISSE
===================================================== */
.caisse-footer{
    margin-top:30px;
    padding:20px 25px;
    background:var(--card);
    border-radius:var(--radius-lg);
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:var(--muted);
    font-size:13px;
    box-shadow:var(--shadow-soft);
    border:1px solid var(--line);
}
.caisse-footer div{
    display:flex;
    align-items:center;
    gap:8px;
}
.caisse-footer i{
    color:var(--gold);
}
/* =====================================================
   TOAST NOTIFICATION
===================================================== */
#toastContainer{
    position:fixed;
    top:25px;
    right:25px;
    z-index:9999;
    display:flex;
    flex-direction:column;
    gap:15px;
}
.toast{
    min-width:300px;
    padding:16px 20px;
    border-radius:16px;
    background:white;
    display:flex;
    align-items:center;
    gap:12px;
    font-weight:700;
    font-size:14px;
    box-shadow:
    0 15px 35px rgba(36,28,21,.18);
    animation:
    slideIn .4s ease;
    border:1px solid var(--line);
}
.toast.success{
    border-left:6px solid var(--olive);
    color:#33502A;
}
.toast.error{
    border-left:6px solid var(--wine);
    color:var(--wine);
}
@keyframes slideIn{
    from{
        opacity:0;
        transform:translateX(100px);
    }
    to{
        opacity:1;
        transform:translateX(0);
    }
}
/* =====================================================
   LOADING / ANIMATION
===================================================== */
.card-loading{
    animation:
    pulseCard 1.8s infinite;
}
@keyframes pulseCard{
    0%{
        opacity:1;
    }
    50%{
        opacity:.7;
    }
    100%{
        opacity:1;
    }
}
/* =====================================================
   SIGNATURE — bord "ticket de caisse déchiré"
   Utilisé sous les cartes de total/montant clé.
===================================================== */
.torn-edge{
    position:relative;
    padding-bottom:26px!important;
}
.torn-edge::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    bottom:0;
    height:14px;
    background:
    linear-gradient(135deg,var(--paper) 50%,transparent 50%) 0 0/14px 14px repeat-x,
    linear-gradient(-135deg,var(--paper) 50%,transparent 50%) 0 0/14px 14px repeat-x;
    background-position:0 -7px,7px -7px;
}
.dark-mode .torn-edge::after{
    background:
    linear-gradient(135deg,#020617 50%,transparent 50%) 0 0/14px 14px repeat-x,
    linear-gradient(-135deg,#020617 50%,transparent 50%) 0 0/14px 14px repeat-x;
    background-position:0 -7px,7px -7px;
}
/* effet "tampon" pour un badge important (ex. statut Prête sur le résumé) */
.stamp{
    display:inline-block;
    transform:rotate(-4deg);
    border:2px solid currentColor;
    padding:5px 12px;
    border-radius:8px;
    text-transform:uppercase;
    letter-spacing:1px;
    font-size:11.5px;
    font-weight:800;
}
/* =====================================================
   SCROLLBAR PREMIUM
===================================================== */
::-webkit-scrollbar{
    width:8px;
}
::-webkit-scrollbar-track{
    background:var(--paper-soft);
}
::-webkit-scrollbar-thumb{
    background:#C9B896;
    border-radius:20px;
}
::-webkit-scrollbar-thumb:hover{
    background:var(--gold);
}
/* =====================================================
   RESPONSIVE DESIGN
===================================================== */
@media(max-width:1200px){
    .analytics-grid{
        grid-template-columns:1fr;
    }
    .performance-grid{
        grid-template-columns:
        repeat(2,1fr);
    }
}
@media(max-width:768px){
    .caisse-container{
        padding:15px;
    }
    .caisse-title{
        font-size:21px;
    }
    .welcome-card{
        flex-direction:column;
        align-items:flex-start;
        gap:16px;
    }
    .stats-grid{
        grid-template-columns:1fr;
    }
    .analytics-grid.two,
    .analytics-grid.two-columns{
        grid-template-columns:1fr;
    }
    .performance-grid{
        grid-template-columns:1fr;
    }
    .chart-card,
    .table-card{
        padding:18px;
        border-radius:16px;
    }
    .premium-table{
        min-width:700px;
    }
    .card-header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }
    .caisse-footer{
        flex-direction:column;
        gap:15px;
        text-align:center;
    }
    .btn-pay{
        width:100%;
        justify-content:center;
    }
    #toastContainer{
        left:15px;
        right:15px;
        top:15px;
    }
    .toast{
        min-width:auto;
        width:100%;
    }
}
/* =====================================================
   DARK MODE SUPPORT
===================================================== */
.dark-mode{
    --paper:#181310;
    --paper-soft:#221B15;
    --card:#221B15;
    --line:#3A2E22;
    --text:#EFE6D6;
    --muted:#B5A98F;
    --ink:#EFE6D6;
}
.dark-mode body{
    background:#181310;
}
.dark-mode .topbar,
.dark-mode .footer{
    background:#221B15;
    border-color:#3A2E22;
}
.dark-mode .left-top h1,
.dark-mode .user h3{
    color:#F3EBDA;
}
.dark-mode .search-box input{
    background:#181310;
    color:#F3EBDA;
    border-color:#3A2E22;
}
.dark-mode .icon-btn{
    background:#181310;
    color:#EFE6D6;
}
.dark-mode .caisse-container{
    background:#181310;
}
.dark-mode .chart-card,
.dark-mode .table-card,
.dark-mode .performance-card,
.dark-mode .stat-card,
.dark-mode .mini-card,
.dark-mode .caisse-footer,
.dark-mode .facture-card{
    background:#221B15;
    color:#EFE6D6;
    border-color:#3A2E22;
}
.dark-mode .card-header h3,
.dark-mode .performance-card h2,
.dark-mode .rank-info strong,
.dark-mode .stat-card h2,
.dark-mode .mini-card h2{
    color:#F3EBDA;
}
.dark-mode .premium-table td{
    color:#D8CCB4;
    border-color:#3A2E22;
}
.dark-mode .premium-table thead{
    background:#181310;
}
.dark-mode .payment-item,
.dark-mode .rank-item,
.dark-mode .activity-item,
.dark-mode .resume-card,
.dark-mode .facture-info div{
    background:#181310;
}
.dark-mode .payment-item strong{
    color:#F3EBDA;
}
.dark-mode .welcome-card{
    background:linear-gradient(120deg,#0F0B08,#241C15);
}
/* =====================================================
   SMOOTH TRANSITIONS
===================================================== */
*{
    transition:
    background .25s,
    color .25s,
    border .25s;
}
/* =====================================
   PAGE ENCAISSEMENT
===================================== */
.commande-resume{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin:25px 0;
}
.resume-card{
    background:var(--paper-soft);
    padding:20px;
    border-radius:16px;
    display:flex;
    flex-direction:column;
    gap:8px;
    border:1px solid var(--line);
}
.resume-card span{
    color:var(--muted);
    font-size:13px;
}
.resume-card strong{
    font-size:21px;
    color:var(--ink);
}
.payment-title{
    margin:25px 0 15px;
    font-size:16px;
    font-weight:700;
    color:var(--ink);
}
.payment-methods{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:15px;
}
.method-card input{
    display:none;
}
.method-card div{
    padding:20px;
    background:var(--paper-soft);
    border-radius:16px;
    text-align:center;
    cursor:pointer;
    transition:.3s;
    border:2px solid transparent;
}
.method-card span{
    font-size:13.5px;
    font-weight:600;
    color:var(--text);
}
.method-card i{
    display:block;
    font-size:28px;
    margin-bottom:10px;
    color:var(--gold-deep);
}
.method-card:hover div{
    transform:translateY(-5px);
    background:var(--gold-light);
}
.method-card input:checked + div{
    border-color:var(--gold);
    background:var(--gold-light);
}
.validate-pay{
    margin-top:30px;
    width:100%;
    justify-content:center;
    font-size:16px;
    padding:16px;
}
@media(max-width:900px){
    .commande-resume{
        grid-template-columns:1fr;
    }
    .payment-methods{
        grid-template-columns:repeat(2,1fr);
    }
}
@media(max-width:500px){
    .payment-methods{
        grid-template-columns:1fr;
    }
}
/* =====================================
   FACTURE PREMIUM
===================================== */
.facture-card{
    background:var(--card);
    max-width:900px;
    margin:auto;
    padding:35px;
    border-radius:25px;
    box-shadow:var(--shadow-lift);
    border:1px solid var(--line);
}
.facture-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-bottom:20px;
    border-bottom:2px dashed var(--line);
}
.facture-header h1{
    margin:0;
    font-family:var(--font-display);
    font-weight:400;
    font-size:30px;
    color:var(--ink);
}
.facture-header p{
    color:var(--muted);
}
.facture-number{
    text-align:right;
    display:flex;
    flex-direction:column;
}
.facture-number strong{
    color:var(--gold-deep);
    font-size:19px;
}
.facture-info{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin:25px 0;
}
.facture-info div{
    background:var(--paper-soft);
    padding:15px;
    border-radius:14px;
    display:flex;
    flex-direction:column;
}
.facture-info span{
    color:var(--muted);
    font-size:12.5px;
}
.facture-table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
.facture-table th{
    background:var(--paper-soft);
    padding:14px 15px;
    text-align:left;
    font-size:12px;
    text-transform:uppercase;
    color:var(--muted);
}
.facture-table td{
    padding:15px;
    border-bottom:1px solid var(--line);
}
.facture-total{
    margin-top:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.facture-total h2{
    color:var(--olive);
    font-family:var(--font-mono);
}
.facture-footer{
    margin-top:35px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:var(--muted);
    font-size:13px;
}
@media print{
    .sidebar,
    .navbar,
    .btn-pay{
        display:none!important;
    }
    .facture-card{
        box-shadow:none;
    }
}
</style>
</body>

</html>