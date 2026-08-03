@extends('client.layouts.app')


@section('title',$plat->nom)



@section('content')


<div class="detail-page">



<div class="detail-image">


<img src="{{ $plat->image 
? asset('images/'.$plat->image)
: asset('images/default-food.jpg') }}">



@if($plat->is_populaire)

<span class="detail-badge">

🔥 Populaire

</span>

@endif



</div>





<div class="detail-content">


<div class="category-name">


{{ $plat->category->nom }}


</div>



<h1>

{{ $plat->nom }}

</h1>



<p class="description">

{{ $plat->description }}

</p>




<div class="info-box">


<div>

⏱️

<strong>

{{ $plat->temps_preparation ?? 15 }}

</strong>

min

</div>



<div>

🔥

Très demandé

</div>



</div>







<div class="price-box">


@if($plat->prix_promo)


<del>

{{ number_format($plat->prix,0) }}

HTG

</del>



<h2>

{{ number_format($plat->prix_promo,0) }}

HTG

</h2>


@else


<h2>

{{ number_format($plat->prix,0) }}

HTG

</h2>


@endif



</div>







<button
class="detail-add"

onclick="
addToCart(
{{ $plat->id }},
'{{ addslashes($plat->nom) }}',
{{ $plat->prix }},
'{{ $plat->image }}'
)">


🛒 Ajouter au panier


</button>




</div>






{{-- AUTRES PLATS --}}


@if($relatedPlats->count())


<section class="related">


<h2>

Vous aimerez aussi

</h2>



<div class="related-grid">


@foreach($relatedPlats as $item)


<a href="{{route('client.plat.show',
[
'tableId'=>$tableId,
'id'=>$item->id
])}}"
class="mini-card">



<img src="{{asset('images/'.$item->image)}}">


<div>

<h4>

{{$item->nom}}

</h4>


<strong>

{{number_format($item->prix,0)}}

HTG

</strong>


</div>


</a>


@endforeach


</div>



</section>


@endif





</div>


<style>
/* ============================= */
/* MOBILE NAVIGATION */
/* ============================= */

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



<script>
const TABLE_ID = "{{ $tableId ?? 1 }}";

let cart = JSON.parse(
    localStorage.getItem('restaurant_cart')
) || {};

function goMenu(){
    window.location.href = "/menu/"+TABLE_ID;
}

function goWaiting(){
    let commandeId = localStorage.getItem("commande_id");
    if(commandeId){
        window.location.href = "/waiting/" + TABLE_ID + "/" + commandeId;
    } else {
        showToast("Aucune commande active");
    }
}

function saveCart(){
    localStorage.setItem('restaurant_cart', JSON.stringify(cart));
}

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

function addToCart(id, name, price, image){
    if(cart[id]){
        cart[id].qty++;
    } else {
        cart[id] = { id: id, name: name, price: Number(price), image: image, qty: 1 };
    }
    saveCart();
    renderCart();

    // Sync ak session Laravel la
    fetch('/cart/add',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({ plat_id: id, quantite: 1 })
    }).catch(error => console.error("Cart error:", error));

    showToast("🍽️ "+name+" ajouté");
}

function changeQty(id, value){
    if(!cart[id]) return;
    cart[id].qty += value;
    if(cart[id].qty <= 0){
        delete cart[id];
    }
    saveCart();
    renderCart();
}

function renderCart(){
    let html = "";
    let total = 0;
    let count = 0;

    Object.values(cart).forEach(item => {
        count += item.qty;
        total += item.price * item.qty;

        html += `
        <div class="cart-product">
            <img src="/images/${item.image || 'default.jpg'}">
            <div class="cart-info">
                <h4>${item.name}</h4>
                <p>${item.price} HTG</p>
            </div>
            <div class="qty-box">
                <button onclick="changeQty(${item.id},-1)">−</button>
                <span>${item.qty}</span>
                <button onclick="changeQty(${item.id},1)">+</button>
            </div>
        </div>
        `;
    });

    let cartItemsEl = document.getElementById('cartItems');
    if(cartItemsEl){
        cartItemsEl.innerHTML = html || `
        <div class="empty-cart">
            🛒
            <h3>Panier vide</h3>
        </div>
        `;
    }

    let service = total * 0.10;
    let grandTotal = total + service;

    let countEl = document.getElementById('cartCount');
    if(countEl) countEl.innerText = count;

    let navBadge = document.getElementById('navCartCount');
    if (navBadge) {
        if (count > 0) {
            navBadge.innerText = count;
            navBadge.style.display = 'inline-block';
        } else {
            navBadge.style.display = 'none';
        }
    }

    let totalEl = document.getElementById('cartTotal');
    if(totalEl) totalEl.innerText = total + " HTG";

    let subtotalEl = document.getElementById('subtotal');
    if(subtotalEl) subtotalEl.innerText = total + " HTG";

    let serviceEl = document.getElementById('service');
    if(serviceEl) serviceEl.innerText = service.toFixed(0) + " HTG";

    let grandTotalEl = document.getElementById('grandTotal');
    if(grandTotalEl) grandTotalEl.innerText = grandTotal.toFixed(0) + " HTG";
}

function sendOrder(){
    if(Object.keys(cart).length === 0){
        alert("Votre panier est vide");
        return;
    }

    let note = "";
    let noteInput = document.getElementById('orderNote'); // Bon ID an kounye a
    if(noteInput){
        note = noteInput.value;
    }

    fetch('/checkout',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({
            table_id: TABLE_ID,
            note: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            cart = {};
            localStorage.removeItem('restaurant_cart');
            localStorage.setItem("commande_id", data.commande_id);
            renderCart();
            closeCart();
            
            document.getElementById('successModal')?.classList.add('show');
            setTimeout(() => {
                window.location.href = "/waiting/" + TABLE_ID;
            }, 3000);
        } else {
            alert(data.message || "Erreur commande");
        }
    })
    .catch(error => {
        console.error(error);
        alert("Erreur serveur");
    });
}

function showToast(message){
    let box = document.createElement('div');
    box.className = "toast";
    box.innerHTML = message;
    document.body.appendChild(box);
    setTimeout(() => { box.remove(); }, 2500);
}

document.addEventListener('DOMContentLoaded', () => {
    renderCart();
});
</script>
@endsection