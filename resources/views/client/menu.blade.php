@extends('client.layouts.app')

@section('title', 'Menu - Restaurant Kay-Y')

@section('content')

<section class="menu-page">


    {{-- CATEGORY FILTER --}}
    <div class="category-section">

        <div class="section-title">

            <h2>
                🍽️ Notre Menu
            </h2>

            <span>
                Découvrez nos spécialités
            </span>

        </div>


        <div class="category-scroll">


            <a href="/menu/{{ $tableId }}"
               class="category-chip 
               {{ !request('category') ? 'active' : '' }}">

                <span>🔥</span>
                Tous

            </a>


            @foreach($allCategories as $cat)

                <a href="/menu/{{ $tableId }}?category={{ $cat->id }}"
                   class="category-chip
                   {{ request('category') == $cat->id ? 'active':'' }}">


                    @if(str_contains(strtolower($cat->nom),'pizza'))
                        🍕
                    @elseif(str_contains(strtolower($cat->nom),'boisson'))
                        🥤
                    @elseif(str_contains(strtolower($cat->nom),'dessert'))
                        🍰
                    @elseif(str_contains(strtolower($cat->nom),'plat'))
                        🍗
                    @else
                        🥗
                    @endif


                    {{ $cat->nom }}


                </a>

            @endforeach


        </div>

    </div>




    {{-- MENU LIST --}}

    <div class="menu-container">


        @foreach($categories as $cat)


            @if($cat->plats->count())


            <div class="category-block">


                <div class="category-header">


                    <h3>

                        @if($cat->nom)
                            {{ $cat->nom }}
                        @endif

                    </h3>


                    <span>

                        {{ $cat->plats->count() }} plats

                    </span>


                </div>





                <div class="dish-grid">



                @foreach($cat->plats as $plat)



                <article class="dish-card"
                         data-name="{{ strtolower($plat->nom) }}">



                    {{-- IMAGE --}}

                    <div class="dish-image">


                        <img src="{{ $plat->image 
                        ? asset('images/'.$plat->image)
                        : asset('images/default-food.jpg') }}"
                        alt="{{ $plat->nom }}">



                        @if($plat->is_populaire)

                            <span class="badge popular">

                                🔥 Populaire

                            </span>

                        @endif



                        @if($plat->prix_promo)

                            <span class="badge promo">

                                Promo

                            </span>

                        @endif



                        <button class="favorite-btn">

                            ❤️

                        </button>



                    </div>





                    {{-- CONTENT --}}

                    <div class="dish-content">



                        <h4>

                            {{ $plat->nom }}

                        </h4>



                        <p>

                            {{ Str::limit($plat->description,70) }}

                        </p>




                        <div class="dish-info">


                            <span>

                                ⏱️ 
                                {{ $plat->temps_preparation ?? 15 }}
                                min

                            </span>


                            <span>

                                ⭐ 4.8

                            </span>


                        </div>





                        <div class="dish-footer">



                            <div class="price">


                                @if($plat->prix_promo)

                                    <del>

                                        {{ number_format($plat->prix,0) }}
                                        HTG

                                    </del>

                                    <strong>

                                        {{ number_format($plat->prix_promo,0) }}
                                        HTG

                                    </strong>

                                @else


                                    <strong>

                                        {{ number_format($plat->prix,0) }}
                                        HTG

                                    </strong>


                                @endif



                            </div>



                            <div class="actions">

<a href="{{ route('client.plat.show',
[
'tableId'=>$tableId,
'id'=>$plat->id
]) }}"
class="detail-btn">

👁️

</a>


                                <button
                                class="add-btn"
                                onclick="
                                addToCart(
                                {{ $plat->id }},
                                '{{ addslashes($plat->nom) }}',
                                {{ $plat->prix }},
                                '{{ $plat->image }}'
                                )">


                                    +
                                    Ajouter


                                </button>


                            </div>


                        </div>


                    </div>



                </article>



                @endforeach



                </div>


            </div>


            @endif


        @endforeach



    </div>


</section>

{{-- ============================= --}}
{{-- FLOATING CART BUTTON --}}
{{-- ============================= --}}


<br>
<br>
<br>
<br>
<br>
<br>
<br>



{{-- ============================= --}}
{{-- OVERLAY --}}
{{-- ============================= --}}
<div class="cart-overlay"
     id="cartOverlay"
     onclick="closeCart()">

</div>



{{-- ============================= --}}
{{-- CART PANEL --}}
{{-- ============================= --}}


<div class="cart-panel"
     id="cartPanel">


    <div class="cart-header">


        <div>

            <h2>
                🛒 Mon panier
            </h2>

            <p>
                Table {{ $tableId }}
            </p>

        </div>


        <button onclick="closeCart()">

            ✕

        </button>


    </div>





    <div class="cart-items"
         id="cartItems">


        <div class="empty-cart">

            🛍️

            <h3>
                Votre panier est vide
            </h3>

            <p>
                Ajoutez vos plats préférés
            </p>

        </div>


    </div>







    {{-- NOTE CUISINE --}}

    <div class="order-note">


        <label>

            📝 Note pour la cuisine

        </label>


        <textarea
        id="orderNote"
        placeholder="Ex: moins épicé, sans sauce..."></textarea>


    </div>






    {{-- TOTAL --}}

    <div class="cart-summary">


        <div>

            <span>
                Sous-total
            </span>

            <strong id="subtotal">

                0 HTG

            </strong>


        </div>



        <div>


            <span>
                Service 10%
            </span>


            <strong id="service">

                0 HTG

            </strong>


        </div>



        <hr>



        <div class="grand-total">


            <span>
                Total
            </span>


            <strong id="grandTotal">

                0 HTG

            </strong>


        </div>


    </div>







    <button class="checkout-btn"
            onclick="sendOrder()">


        ✅ Envoyer la commande


    </button>






</div>







{{-- ============================= --}}
{{-- ORDER SUCCESS --}}
{{-- ============================= --}}


<div class="success-modal"
     id="successModal">


    <div class="success-card">


        <div class="success-icon">

            🎉

        </div>


        <h2>

            Commande envoyée !

        </h2>


        <p>

            Votre repas arrive bientôt.

        </p>



        <div class="waiting-box">


            ⏱️ Temps estimé


            <strong>

                <span id="waitingTime">
                    20
                </span>

                minutes

            </strong>


        </div>





        <button onclick="goWaitingRoom()">


            🎮 Patienter avec un jeu


        </button>




        <button class="secondary"
                onclick="closeSuccess()">


            Retour au menu


        </button>



    </div>


</div>



<script>

const TABLE_ID = "{{ $tableId }}";


let cart = JSON.parse(
    localStorage.getItem('restaurant_cart')
) || {};

function goMenu(){

    window.location.href =
    "/menu/"+TABLE_ID;

}



function goWaiting(){

    let commandeId = localStorage.getItem(
        "commande_id"
    );


    if(commandeId){

        window.location.href =
        "/waiting/"
        +TABLE_ID+
        "/"
        +commandeId;

    }
    else{

        showToast(
        "Aucune commande active"
        );

    }

}

/*
|--------------------------------------------------------------------------
| SAVE CART LOCAL
|--------------------------------------------------------------------------
*/

function saveCart(){

    localStorage.setItem(
        'restaurant_cart',
        JSON.stringify(cart)
    );

}



/*
|--------------------------------------------------------------------------
| OPEN / CLOSE CART
|--------------------------------------------------------------------------
*/

function openCart(){

    document
    .getElementById('cartPanel')
    .classList.add('show');


    document
    .getElementById('cartOverlay')
    .classList.add('show');

}



function closeCart(){

    document
    .getElementById('cartPanel')
    .classList.remove('show');


    document
    .getElementById('cartOverlay')
    .classList.remove('show');

}




/*
|--------------------------------------------------------------------------
| ADD TO CART
|--------------------------------------------------------------------------
*/

function addToCart(id,name,price,image){



    if(cart[id]){


        cart[id].qty++;


    }
    else{


        cart[id]={

            id:id,

            name:name,

            price:Number(price),

            image:image,

            qty:1

        };


    }



    saveCart();


    renderCart();



    /*
    |--------------------------------------------------------------------------
    | Sync Laravel Session
    |--------------------------------------------------------------------------
    */


    fetch('/cart/add',{


        method:'POST',


        headers:{


            'Content-Type':'application/json',


            'X-CSRF-TOKEN':
            '{{ csrf_token() }}'


        },


        body:JSON.stringify({


            plat_id:id,


            quantite:1


        })


    })

    .then(response=>response.json())


    .then(data=>{


        console.log(
            "Laravel cart:",
            data
        );


    })

    .catch(error=>{


        console.error(
            "Cart error:",
            error
        );


    });





    showToast(
        "🍽️ "+name+" ajouté"
    );


}





/*
|--------------------------------------------------------------------------
| CHANGE QUANTITY
|--------------------------------------------------------------------------
*/


function changeQty(id,value){



    if(!cart[id])
        return;



    cart[id].qty += value;



    if(cart[id].qty <=0){


        delete cart[id];


    }



    saveCart();


    renderCart();



}





/*
|--------------------------------------------------------------------------
| RENDER CART
|--------------------------------------------------------------------------
*/
function renderCart(){

let html="";
let total=0;
let count=0;

Object.values(cart).forEach(item=>{
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

// Sekirite pou chak Eleman yo pou yo pa voye erè si yo pa nan paj la
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

/*
|--------------------------------------------------------------------------
| SEND ORDER
|--------------------------------------------------------------------------
*/


function sendOrder(){



if(Object.keys(cart).length===0){


    alert(
        "Votre panier est vide"
    );


    return;


}



let note = "";
let noteInput = document.getElementById('orderNote'); // Chanje noteInput an orderNote

if(noteInput){
    note = noteInput.value;
}




fetch('/checkout',{


method:'POST',



headers:{


'Content-Type':'application/json',


'X-CSRF-TOKEN':
'{{ csrf_token() }}'


},



body:JSON.stringify({



table_id:TABLE_ID,


note:note



})



})



.then(response=>response.json())



.then(data=>{



console.log(
"Checkout:",
data
);



if(data.success){



cart={};



localStorage.removeItem(
'restaurant_cart'
);
localStorage.setItem(
    "commande_id",
    data.commande_id
);


renderCart();



closeCart();



document
.getElementById('successModal')
.classList.add('show');



/*
    Aller salle attente après 3 secondes
*/


setTimeout(()=>{


goWaitingRoom();



},3000);



}



else{


alert(
data.message || 
"Erreur commande"
);



}



})



.catch(error=>{


console.error(
error
);


alert(
"Erreur serveur"
);



});



}





/*
|--------------------------------------------------------------------------
| WAITING ROOM
|--------------------------------------------------------------------------
*/


function goWaitingRoom(){


window.location.href =

"/waiting/"+TABLE_ID;


}




/*
|--------------------------------------------------------------------------
| SUCCESS MODAL
|--------------------------------------------------------------------------
*/


function closeSuccess(){


document
.getElementById('successModal')
.classList.remove('show');


}




/*
|--------------------------------------------------------------------------
| TOAST
|--------------------------------------------------------------------------
*/


function showToast(message){



let box=document.createElement('div');



box.className="toast";



box.innerHTML=message;



document.body.appendChild(box);




setTimeout(()=>{


box.remove();


},2500);



}





/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/


document.addEventListener(
'DOMContentLoaded',
()=>{


renderCart();


}

);



</script>

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
@endsection