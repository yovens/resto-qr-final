<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="theme-color"
          content="#ff6b00">

    <title>
        @yield('title','Restaurant Kay-Y')
    </title>

    <meta name="description"
          content="Restaurant Kay-Y QR Menu">

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet"
          href="{{ asset('css/client.css') }}">

    @stack('styles')

</head>

<body>

<div id="page-loader">

    <div class="loader-logo">

        🍽️

    </div>

    <span>Chargement...</span>

</div>

<div id="toastContainer"></div>

<div class="app-wrapper">

    {{-- HEADER --}}

    <header class="hero">

        <div class="hero-overlay"></div>

        <div class="hero-content">

            <div class="hero-top">

                <div class="restaurant">

                    <div class="restaurant-logo">

                        🍽️

                    </div>

                    <div>

                        <h1>

                            Restaurant Kay-Y

                        </h1>

                        <span>

                            Cuisine Haïtienne • Pizza • Grillades

                        </span>

                    </div>

                </div>

                <button class="theme-btn"
                        id="themeToggle">

                    <i class="fa-solid fa-moon"></i>

                </button>

            </div>

            <div class="hero-middle">

                <div class="table-card">

                    <div>

                        <small>Votre Table</small>

                        <h2>

                            Table

                            {{ $tableId }}

                        </h2>

                    </div>

                    <div class="wifi">

                        <i class="fa-solid fa-wifi"></i>

                    </div>

                </div>

                <div class="hero-title">

                    <h2>

                        Bon Appétit 👋

                    </h2>

                    <p>

                        Découvrez nos meilleurs plats préparés avec passion.

                    </p>

                </div>

            </div>

            <div class="search-wrapper">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input

                    type="text"

                    id="searchDish"

                    placeholder="Rechercher un plat...">

            </div>

        </div>

    </header>

    <main>

        @yield('content')

    </main>

</div>

<br>
<br>
<br>
<br>

<nav class="mobile-nav">

    <button onclick="goMenu()">
        <i class="fa-solid fa-house"></i>
        <span>Accueil</span>
    </button>

    <button onclick="openCart()">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Panier</span>
        <span id="navCartCount" class="nav-cart-badge" style="display: none;">0</span>
    </button>

    <button onclick="showToast('Favoris bientôt disponible')">
        <i class="fa-solid fa-heart"></i>
        <span>Favoris</span>
    </button>

    <!-- Bouton Commande ki te nan floating cart la -->
    <button onclick="goWaiting()">
        <i class="fa-solid fa-bell-concierge"></i>
        <span>Commande</span>
    </button>

</nav>


<script src="{{ asset('js/client.js') }}"></script>

@stack('scripts')

<script>

window.addEventListener('load',function(){

document.getElementById('page-loader').classList.add('hide');

});

const toggle=document.getElementById("themeToggle");

toggle?.addEventListener("click",()=>{

document.body.classList.toggle("dark");

localStorage.setItem(

"theme",

document.body.classList.contains("dark")

?

"dark"

:

"light"

);

});

if(localStorage.getItem("theme")==="dark"){

document.body.classList.add("dark");

}

</script>
<style>
    /* Styling pou ti wonn kantite atik la sou mobile-nav la */
.mobile-nav button {
    position: relative; /* Enpòtan pou pozisyon ti wonn nan */
}

.mobile-nav .nav-cart-badge {
    position: absolute;
    top: 5px;
    right: 22%;
    background: #ff4757;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
    display: none;
}
</style>
</body>

</html>