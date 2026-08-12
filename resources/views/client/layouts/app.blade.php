<!DOCTYPE html>
<html lang="ht">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a5f">
    <title>@yield('title','Kay-Y') — Restoran Ayisyen</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=IBM+Plex+Mono:wght@400;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <style>
    :root{
        --bleu-haiti:#1e3a5f;
        --bleu-fonce:#152a45;
        --rouge-brik:#c0392b;
        --rouge-fonce:#96281b;
        --lò:#d4a843;
        --lò-clair:#f4e4c1;
        --kreyòl:#2c1810;
        --terre:#8d6e63;
        --sable:#faf6f0;
        --sable-fonce:#f0e6d6;
        --blan:#ffffff;
        --vèt:#27ae60;
        --font-bistro:'Playfair Display',serif;
        --font-mono:'IBM Plex Mono',monospace;
        --font-body:'Inter',sans-serif;
    }
    
    *{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
    body{
        font-family:var(--font-body);
        background:var(--sable);
        color:var(--kreyòl);
        padding-bottom:90px;
        overflow-x:hidden;
    }
    
    /* Loader */
    #page-loader{
        position:fixed;inset:0;background:var(--bleu-haiti);z-index:99999;
        display:flex;flex-direction:column;align-items:center;justify-content:center;
        transition:opacity .6s ease,visibility .6s;
    }
    #page-loader.hide{opacity:0;visibility:hidden;}
    .loader-logo{
        width:80px;height:80px;background:linear-gradient(135deg,var(--lò),var(--rouge-brik));
        border-radius:24px;display:flex;align-items:center;justify-content:center;
        font-size:40px;animation:pulseLoader 1.5s infinite;margin-bottom:20px;
        box-shadow:0 10px 30px rgba(0,0,0,.3);
    }
    @keyframes pulseLoader{0%,100%{transform:scale(1);}50%{transform:scale(1.08);}}
    #page-loader span{color:var(--lò-clair);font-family:var(--font-bistro);font-size:18px;letter-spacing:2px;}
    
    /* Toast */
    #toastContainer{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:12px;width:90%;max-width:400px;}
    .toast{
        background:var(--blan);color:var(--kreyòl);padding:16px 20px;border-radius:16px;
        font-size:14px;font-weight:600;box-shadow:0 10px 30px rgba(30,58,95,.15);
        border-left:5px solid var(--lò);animation:slideDown .4s ease;font-family:var(--font-body);
        display:flex;align-items:center;gap:10px;
    }
    .toast.error{border-left-color:var(--rouge-brik);}
    @keyframes slideDown{from{opacity:0;transform:translateY(-20px);}to{opacity:1;transform:translateY(0);}}
    
    /* Hero */
    .hero{
        background:linear-gradient(180deg,var(--bleu-haiti) 0%,var(--bleu-fonce) 100%);
        border-radius:0 0 40px 40px;padding:25px 20px 30px;position:relative;overflow:hidden;
        box-shadow:0 10px 40px rgba(30,58,95,.25);margin-bottom:20px;
    }
    .hero::before{
        content:'';position:absolute;top:-50%;right:-20%;width:250px;height:250px;
        background:radial-gradient(circle,rgba(212,168,67,.15) 0%,transparent 70%);
    }
    .hero-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .restaurant{display:flex;align-items:center;gap:12px;}
    .restaurant-logo{
        width:48px;height:48px;background:linear-gradient(135deg,var(--lò),var(--rouge-brik));
        border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;
        box-shadow:0 4px 15px rgba(0,0,0,.2);
    }
    .restaurant h1{font-family:var(--font-bistro);font-size:22px;color:#fff;font-weight:800;letter-spacing:1px;}
    .restaurant span{font-size:11px;color:var(--lò-clair);opacity:.8;text-transform:uppercase;letter-spacing:1px;}
    
    .theme-btn{
        width:42px;height:42px;border-radius:50%;border:1px solid rgba(255,255,255,.15);
        background:rgba(255,255,255,.08);color:#fff;font-size:18px;cursor:pointer;
        transition:.3s;display:flex;align-items:center;justify-content:center;
    }
    .theme-btn:active{transform:scale(.95);}
    
    .hero-middle{margin-bottom:20px;}
    .table-badge{
        display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.1);
        padding:10px 18px;border-radius:50px;border:1px solid rgba(212,168,67,.3);margin-bottom:15px;
    }
    .table-badge small{color:var(--lò-clair);font-size:11px;text-transform:uppercase;letter-spacing:1px;}
    .table-badge h2{font-family:var(--font-bistro);color:#fff;font-size:20px;}
    .table-badge .wifi{color:var(--lò);font-size:18px;}
    .hero-title h2{font-family:var(--font-bistro);font-size:26px;color:#fff;margin-bottom:6px;}
    .hero-title p{color:rgba(255,255,255,.7);font-size:14px;line-height:1.5;}
    
    .search-wrapper{
        position:relative;background:rgba(255,255,255,.1);border-radius:16px;
        border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(10px);
    }
    .search-wrapper i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--lò-clair);}
    .search-wrapper input{
        width:100%;padding:14px 14px 14px 45px;background:transparent;border:none;
        outline:none;color:#fff;font-size:14px;font-family:var(--font-body);
    }
    .search-wrapper input::placeholder{color:rgba(255,255,255,.5);}
    
    /* Commande en cours banner */
    .active-order-banner{
        background:linear-gradient(90deg,var(--vèt),#2ecc71);color:#fff;
        padding:14px 20px;border-radius:16px;margin:0 20px 20px;
        display:flex;align-items:center;justify-content:space-between;
        box-shadow:0 6px 20px rgba(39,174,96,.25);animation:slideDown .5s ease;
    }
    .active-order-banner strong{font-size:14px;display:block;margin-bottom:2px;}
    .active-order-banner small{font-size:12px;opacity:.9;}
    .active-order-banner a{
        background:rgba(255,255,255,.2);color:#fff;padding:8px 16px;border-radius:12px;
        text-decoration:none;font-size:12px;font-weight:700;white-space:nowrap;
    }
    
    /* Mobile Nav */
    .mobile-nav{
        position:fixed;bottom:0;left:0;right:0;height:75px;background:var(--blan);
        display:flex;justify-content:space-around;align-items:center;
        border-radius:25px 25px 0 0;box-shadow:0 -8px 30px rgba(30,58,95,.12);
        z-index:900;padding:8px 10px;
    }
    .mobile-nav button{
        flex:1;border:none;background:transparent;display:flex;flex-direction:column;
        align-items:center;justify-content:center;gap:4px;color:var(--terre);
        cursor:pointer;transition:.3s;font-size:11px;font-weight:600;position:relative;
    }
    .mobile-nav i{font-size:20px;transition:.3s;}
    .mobile-nav button:hover,.mobile-nav button.active{color:var(--rouge-brik);}
    .mobile-nav button:hover i,.mobile-nav button.active i{transform:translateY(-3px);}
    .mobile-nav button.active i{
        background:linear-gradient(135deg,var(--rouge-brik),var(--lò));
        color:#fff;width:44px;height:44px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        box-shadow:0 6px 15px rgba(192,57,43,.3);
    }
    .nav-cart-badge{
        position:absolute;top:2px;right:18%;background:var(--rouge-brik);color:#fff;
        font-size:9px;font-weight:700;padding:2px 6px;border-radius:50%;display:none;
        font-family:var(--font-mono);min-width:18px;text-align:center;
    }
    
    /* Floating cart */
    .floating-cart{
        position:fixed;bottom:95px;right:20px;
        background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));
        color:#fff;padding:14px 22px;border-radius:50px;display:flex;align-items:center;
        gap:12px;cursor:pointer;box-shadow:0 8px 25px rgba(192,57,43,.35);
        z-index:890;transition:.3s;font-family:var(--font-body);border:none;
    }
    .floating-cart:active{transform:scale(.95);}
    .floating-cart .cart-icon{position:relative;font-size:22px;}
    .floating-cart #cartCount{
        position:absolute;top:-10px;right:-10px;background:var(--lò);color:var(--kreyòl);
        font-size:10px;padding:2px 6px;border-radius:50%;font-weight:700;font-family:var(--font-mono);
    }
    .floating-cart .cart-text{display:flex;flex-direction:column;}
    .floating-cart .cart-text small{font-size:10px;opacity:.9;}
    .floating-cart .cart-text strong{font-size:14px;font-family:var(--font-mono);}
    body.cart-open .floating-cart{transform:translateY(100px);opacity:0;pointer-events:none;}
    
    /* Cart Overlay & Panel */
    .cart-overlay{position:fixed;inset:0;background:rgba(30,58,95,.4);z-index:910;opacity:0;visibility:hidden;transition:.3s;backdrop-filter:blur(4px);}
    .cart-overlay.show{opacity:1;visibility:visible;}
    .cart-panel{
        position:fixed;bottom:0;left:0;right:0;background:var(--blan);z-index:920;
        border-radius:30px 30px 0 0;padding:25px;transform:translateY(100%);
        transition:.4s cubic-bezier(.34,1.56,.64,1);max-height:85vh;overflow-y:auto;
        box-shadow:0 -10px 40px rgba(0,0,0,.15);
    }
    .cart-panel.show{transform:translateY(0);}
    .cart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    .cart-header h2{font-family:var(--font-bistro);font-size:22px;color:var(--kreyòl);}
    .cart-header p{color:var(--terre);font-size:13px;margin-top:2px;}
    .cart-header button{background:none;border:none;font-size:24px;color:var(--terre);cursor:pointer;width:40px;height:40px;border-radius:50%;transition:.3s;}
    .cart-header button:active{background:var(--sable-fonce);}
    
    .cart-items{max-height:40vh;overflow-y:auto;margin-bottom:20px;}
    .cart-product{display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid var(--sable-fonce);}
    .cart-product img{width:56px;height:56px;border-radius:14px;object-fit:cover;background:var(--sable-fonce);}
    .cart-info{flex:1;}
    .cart-info h4{font-size:14px;color:var(--kreyòl);margin-bottom:4px;font-weight:600;}
    .cart-info p{font-family:var(--font-mono);font-size:13px;color:var(--rouge-brik);font-weight:600;}
    .qty-box{display:flex;align-items:center;gap:10px;background:var(--sable);border-radius:12px;padding:4px;}
    .qty-box button{width:32px;height:32px;border-radius:10px;border:none;background:var(--blan);color:var(--kreyòl);font-size:16px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.06);transition:.2s;}
    .qty-box button:active{transform:scale(.9);}
    .qty-box span{font-family:var(--font-mono);font-size:14px;font-weight:700;min-width:20px;text-align:center;}
    
    .empty-cart{text-align:center;padding:40px 20px;color:var(--terre);}
    .empty-cart h3{font-size:16px;margin-top:10px;color:var(--kreyòl);}
    
    .order-note{margin-bottom:20px;}
    .order-note label{display:block;font-size:13px;font-weight:600;color:var(--kreyòl);margin-bottom:8px;}
    .order-note textarea{
        width:100%;padding:14px;background:var(--sable);border:1px solid var(--sable-fonce);
        border-radius:16px;resize:none;height:80px;font-family:var(--font-body);font-size:14px;outline:none;
    }
    .order-note textarea:focus{border-color:var(--lò);}
    
    .cart-summary{background:var(--sable);border-radius:20px;padding:20px;margin-bottom:20px;}
    .cart-summary > div{display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px;}
    .cart-summary > div span{color:var(--terre);}
    .cart-summary > div strong{font-family:var(--font-mono);color:var(--kreyòl);}
    .cart-summary hr{border:none;border-top:1px dashed var(--terre);margin:12px 0;opacity:.3;}
    .grand-total{font-size:18px!important;}
    .grand-total strong{color:var(--rouge-brik)!important;font-size:20px;}
    
    .checkout-btn{
        width:100%;padding:18px;background:linear-gradient(135deg,var(--rouge-brik),var(--rouge-fonce));
        color:#fff;border:none;border-radius:18px;font-size:16px;font-weight:700;cursor:pointer;
        box-shadow:0 8px 25px rgba(192,57,43,.3);transition:.3s;font-family:var(--font-body);
    }
    .checkout-btn:active{transform:scale(.98);}
    
    /* Success Modal */
    .success-modal{position:fixed;inset:0;background:rgba(30,58,95,.5);z-index:950;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:.3s;padding:20px;backdrop-filter:blur(6px);}
    .success-modal.show{opacity:1;visibility:visible;}
    .success-card{background:var(--blan);border-radius:28px;padding:35px;text-align:center;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2);transform:scale(.9);transition:.4s cubic-bezier(.34,1.56,.64,1);}
    .success-modal.show .success-card{transform:scale(1);}
    .success-icon{font-size:60px;margin-bottom:15px;animation:bounce 1s infinite;}
    @keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
    .success-card h2{font-family:var(--font-bistro);font-size:24px;color:var(--kreyòl);margin-bottom:8px;}
    .success-card > p{color:var(--terre);margin-bottom:20px;font-size:14px;}
    .waiting-box{background:var(--sable);border-radius:16px;padding:18px;margin-bottom:20px;border:2px dashed var(--lò);}
    .waiting-box strong{display:block;font-family:var(--font-mono);font-size:28px;color:var(--rouge-brik);margin-top:6px;}
    .success-card button{
        width:100%;padding:16px;border-radius:16px;border:none;background:linear-gradient(135deg,var(--vèt),#2ecc71);
        color:#fff;font-size:15px;font-weight:700;cursor:pointer;margin-bottom:10px;box-shadow:0 6px 20px rgba(39,174,96,.25);
    }
    .success-card button.secondary{background:var(--sable);color:var(--kreyòl);box-shadow:none;border:1px solid var(--sable-fonce);}
    
    /* Dark mode */
    body.dark{background:#1a1410;color:#e8dcd4;}
    body.dark .hero{background:linear-gradient(180deg,#0f1f35 0%,#1a1410 100%);}
    body.dark .mobile-nav{background:#241c16;border-top:1px solid #3e3028;}
    body.dark .cart-panel{background:#241c16;}
    body.dark .cart-summary{background:#2a201a;}
    body.dark .cart-product{border-color:#3e3028;}
    body.dark .success-card{background:#241c16;}
    body.dark .waiting-box{background:#2a201a;border-color:#3e3028;}
    
    @media(min-width:769px){
        .mobile-nav{display:none;}
        body{padding-bottom:20px;}
        .app-wrapper{max-width:480px;margin:0 auto;}
    }
    </style>
    @stack('styles')
</head>
<body>

<div id="page-loader">
    <div class="loader-logo">🇭🇹</div>
    <span>Byenveni...</span>
</div>

<div id="toastContainer"></div>
<!-- Dark Mode Toggle -->
<button id="themeToggle" type="button" aria-label="Mode nwa" 
    style="position:fixed;top:18px;right:18px;width:46px;height:46px;border-radius:50%;
    border:1px solid rgba(255,255,255,.2);background:rgba(30,58,95,.55);
    backdrop-filter:blur(10px);color:#fff;font-size:18px;cursor:pointer;
    z-index:1001;display:flex;align-items:center;justify-content:center;
    transition:.3s;box-shadow:0 4px 15px rgba(0,0,0,.2);">
    <i class="fa-solid fa-moon" id="themeIcon"></i>
</button>
<div class="app-wrapper">
    @yield('content')
</div>

<!-- Floating Cart -->
<div class="floating-cart" id="floatingCart" onclick="openCart()">
    <div class="cart-icon">
        <i class="fa-solid fa-basket-shopping"></i>
        <span id="cartCount">0</span>
    </div>
    <div class="cart-text">
        <small>Panier ou</small>
        <strong id="cartTotal">0 HTG</strong>
    </div>
</div>

<!-- Cart Overlay -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>

<!-- Cart Panel -->
<div class="cart-panel" id="cartPanel">
    <div class="cart-header">
        <div>
            <h2>🧺 Panier ou</h2>
            <p>Tab {{ $tableId ?? '---' }}</p>
        </div>
        <button onclick="closeCart()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="cart-items" id="cartItems">
        <div class="empty-cart">
            <div style="font-size:50px;">🍽️</div>
            <h3>Panier ou vid</h3>
            <p>Ajoute plak ou renmen yo</p>
        </div>
    </div>
    <div class="order-note">
        <label>📝 Nòt pou kwizin nan</label>
        <textarea id="orderNote" placeholder="Egzanp: mwens pike, san sòs..."></textarea>
    </div>
    <div class="cart-summary">
        <div><span>Soutotal</span><strong id="subtotal">0 HTG</strong></div>
        <div><span>Sèvis 10%</span><strong id="service">0 HTG</strong></div>
        <hr>
        <div class="grand-total"><span>Total</span><strong id="grandTotal">0 HTG</strong></div>
    </div>
    <button class="checkout-btn" onclick="sendOrder()">
        <i class="fa-solid fa-paper-plane"></i> Voye kòmand lan
    </button>
</div>

<!-- Success Modal -->
<div class="success-modal" id="successModal">
    <div class="success-card">
        <div class="success-icon">🎉</div>
        <h2>Kòmand lan voye!</h2>
        <p>Manje ou a ap vini talè.</p>
        <div class="waiting-box">
            ⏱️ Tan estimasyon
            <strong><span id="waitingTime">20</span> minit</strong>
        </div>
        <button onclick="goWaitingRoom()">
            <i class="fa-solid fa-gamepad"></i> Jwe pandan ou tann
        </button>
        <button class="secondary" onclick="closeSuccess();goMenu();">Retounen nan meni an</button>
    </div>
</div>

<nav class="mobile-nav">
    <button onclick="goMenu()" class="{{ request()->is('menu*') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i>
        <span>Accueil</span>
    </button>
    <button onclick="openCart()">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Panier</span>
        <span id="navCartCount" class="nav-cart-badge">0</span>
    </button>
    <button onclick="showToast('Favori yo disponib talè')">
        <i class="fa-solid fa-heart"></i>
        <span>Favoris</span>
    </button>
    <button onclick="goWaiting()" class="{{ request()->is('waiting*') ? 'active' : '' }}">
        <i class="fa-solid fa-bell-concierge"></i>
        <span>Kòmand</span>
    </button>
</nav>

<script>
const TABLE_ID = "{{ $tableId ?? 1 }}";
let cart = JSON.parse(localStorage.getItem('kayy_cart_'+TABLE_ID)) || {};

function goMenu(){ window.location.href = "/menu/"+TABLE_ID; }
function goWaiting(){
    let cid = localStorage.getItem('kayy_cmd_'+TABLE_ID);
    if(cid){ window.location.href = "/waiting/"+TABLE_ID+"/"+cid; }
    else { showToast("Ou pa gen kòmand aktif"); }
}

function saveCart(){ localStorage.setItem('kayy_cart_'+TABLE_ID, JSON.stringify(cart)); }

function openCart(){
    document.getElementById('cartPanel')?.classList.add('show');
    document.getElementById('cartOverlay')?.classList.add('show');
    document.body.classList.add('cart-open');
}
function closeCart(){
    document.getElementById('cartPanel')?.classList.remove('show');
    document.getElementById('cartOverlay')?.classList.remove('show');
    document.body.classList.remove('cart-open');
}

function addToCart(id,name,price,image){
    if(cart[id]){ cart[id].qty++; }
    else { cart[id] = {id:id,name:name,price:Number(price),image:image,qty:1}; }
    saveCart(); renderCart();
    fetch('/cart/add',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({plat_id:id,quantite:1})
    }).catch(e=>console.error(e));
    showToast("🍽️ "+name+" ajoute");
}

function changeQty(id,val){
    if(!cart[id]) return;
    cart[id].qty += val;
    if(cart[id].qty <= 0) delete cart[id];
    saveCart(); renderCart();
}

function renderCart(){
    let html="",total=0,count=0;
    Object.values(cart).forEach(item=>{
        count+=item.qty; total+=item.price*item.qty;
        html+=`
        <div class="cart-product">
            <img src="/images/${item.image||'default-food.jpg'}" alt="">
            <div class="cart-info">
                <h4>${item.name}</h4>
                <p>${item.price} HTG</p>
            </div>
            <div class="qty-box">
                <button onclick="changeQty(${item.id},-1)">−</button>
                <span>${item.qty}</span>
                <button onclick="changeQty(${item.id},1)">+</button>
            </div>
        </div>`;
    });
    const cItems = document.getElementById('cartItems');
    if(cItems) cItems.innerHTML = html || `<div class="empty-cart"><div style="font-size:50px;">🍽️</div><h3>Panier ou vid</h3><p>Ajoute plak ou renmen yo</p></div>`;
    
    let service = total*0.10, grand = total+service;
    const setTxt = (id,val)=>{const el=document.getElementById(id);if(el)el.innerText=val;};
    setTxt('cartCount',count); setTxt('navCartCount',count);
    const navBadge = document.getElementById('navCartCount');
    if(navBadge) navBadge.style.display = count>0?'inline-block':'none';
    setTxt('cartTotal',total.toLocaleString()+' HTG');
    setTxt('subtotal',total.toLocaleString()+' HTG');
    setTxt('service',service.toFixed(0)+' HTG');
    setTxt('grandTotal',grand.toFixed(0)+' HTG');
}

function sendOrder(){
    if(Object.keys(cart).length===0){ showToast("Panier ou vid"); return; }
    let note=""; const ni=document.getElementById('orderNote'); if(ni) note=ni.value;
    
    fetch('/checkout',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({table_id:TABLE_ID,note:note})
    })
    .then(r=>r.json())
    .then(data=>{
        if(data.success){
            cart={}; saveCart(); renderCart(); closeCart();
            localStorage.setItem('kayy_cmd_'+TABLE_ID, data.commande_id);
            document.getElementById('successModal')?.classList.add('show');
            setTimeout(()=>goWaitingRoom(), 4000);
        } else {
            showToast(data.message || "Erè nan kòmand lan", "error");
        }
    })
    .catch(e=>{ console.error(e); showToast("Erè sèvè","error"); });
}

function goWaitingRoom(){ window.location.href="/waiting/"+TABLE_ID; }
function closeSuccess(){ document.getElementById('successModal')?.classList.remove('show'); }

function showToast(msg,type='success'){
    let box=document.createElement('div');
    box.className='toast'+(type==='error'?' error':'');
    box.innerHTML=(type==='error'?'⚠️ ':'✅ ')+msg;
    document.getElementById('toastContainer')?.appendChild(box);
    setTimeout(()=>box.remove(),3000);
}

window.addEventListener('load',()=>document.getElementById('page-loader')?.classList.add('hide'));
document.addEventListener('DOMContentLoaded',()=>renderCart());


/* ===== DARK MODE ===== */
const themeToggle = document.getElementById("themeToggle");
const themeIcon = document.getElementById("themeIcon");

function applyTheme(){
    const isDark = localStorage.getItem("kayy_theme") === "dark";
    document.body.classList.toggle("dark", isDark);
    if(themeIcon) themeIcon.className = isDark ? "fa-solid fa-sun" : "fa-solid fa-moon";
    if(themeToggle) themeToggle.style.background = isDark ? "rgba(212,168,67,.3)" : "rgba(30,58,95,.55)";
}

if(themeToggle){
    themeToggle.addEventListener("click", function(){
        const willBeDark = !document.body.classList.contains("dark");
        document.body.classList.toggle("dark");
        localStorage.setItem("kayy_theme", willBeDark ? "dark" : "light");
        if(themeIcon) themeIcon.className = willBeDark ? "fa-solid fa-sun" : "fa-solid fa-moon";
        this.style.background = willBeDark ? "rgba(212,168,67,.3)" : "rgba(30,58,95,.55)";
    });
}
// Aplike nan chajman paj
applyTheme();
</script>
@stack('scripts')
</body>
</html>