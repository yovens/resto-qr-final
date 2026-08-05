<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Connexion - Resto Kay-Y</title>


<script src="https://cdn.tailwindcss.com"></script>


<style>

body{

    background:
    linear-gradient(
        135deg,
        #fff7ed,
        #fef3c7
    );

}


.glass{

    backdrop-filter: blur(15px);

}


.food-bg{

    background:

    linear-gradient(
        rgba(0,0,0,.45),
        rgba(0,0,0,.55)
    ),

    url('https://images.unsplash.com/photo-1515003197210-e0cd71810b5f')
    center/cover;

}


.input-style{

    transition:.3s;

}


.input-style:focus{

    transform:translateY(-2px);

}


</style>


</head>


<body class="min-h-screen flex items-center justify-center p-6">



<div class="w-full max-w-6xl grid md:grid-cols-2 bg-white rounded-3xl overflow-hidden shadow-2xl">





<!-- ==========================
        LEFT SIDE
========================== -->


<div class="food-bg hidden md:flex flex-col justify-center p-12 text-white">


<div>


<h1 class="text-5xl font-black mb-5">

🍽️ Resto Kay-Y

</h1>



<p class="text-xl leading-relaxed mb-8">

Votre solution complète pour gérer votre restaurant,
vos commandes et votre stock facilement.

</p>





<div class="space-y-5">


<div class="flex items-center gap-4">

<div class="bg-amber-500 w-12 h-12 rounded-full flex items-center justify-center text-xl">

🍴

</div>


<div>

<h3 class="font-bold text-lg">
Gestion des commandes
</h3>

<p class="text-sm text-gray-200">
Suivez vos commandes en temps réel.
</p>

</div>


</div>







<div class="flex items-center gap-4">

<div class="bg-amber-500 w-12 h-12 rounded-full flex items-center justify-center text-xl">

📦

</div>


<div>

<h3 class="font-bold text-lg">
Gestion du stock
</h3>

<p class="text-sm text-gray-200">
Contrôlez vos produits facilement.
</p>

</div>


</div>








<div class="flex items-center gap-4">

<div class="bg-amber-500 w-12 h-12 rounded-full flex justify-center items-center text-xl">

🚚

</div>


<div>

<h3 class="font-bold text-lg">
Gestion fournisseurs
</h3>

<p class="text-sm text-gray-200">
Gardez vos partenaires organisés.
</p>

</div>


</div>



</div>




</div>


</div>







<!-- ==========================
        RIGHT LOGIN
========================== -->


<div class="p-10 md:p-14 flex flex-col justify-center">





<div class="text-center mb-8">


<div class="mx-auto w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center text-4xl mb-4">

🍽️

</div>



<h2 class="text-3xl font-black text-gray-800">

Bienvenue

</h2>



<p class="text-gray-500 mt-2">

Connectez-vous à votre espace administration

</p>



</div>









@if ($errors->any())


<div class="mb-5 p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl">


<ul class="text-sm">


@foreach ($errors->all() as $error)

<li>
{{ $error }}
</li>

@endforeach


</ul>


</div>


@endif









<form method="POST" action="{{ route('login') }}" class="space-y-5">


@csrf






<div>


<label class="block font-semibold text-gray-700 mb-2">

Email

</label>


<input

type="email"

name="email"

value="{{ old('email') }}"

required

class="input-style w-full px-5 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-amber-500 outline-none"

placeholder="admin@resto.com"


>


</div>







<div>


<label class="block font-semibold text-gray-700 mb-2">

Mot de passe

</label>


<input

type="password"

name="password"

required

class="input-style w-full px-5 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-amber-500 outline-none"

placeholder="••••••••"


>


</div>








<div class="flex justify-between items-center">


<label class="flex items-center gap-2 text-sm text-gray-600">


<input type="checkbox"
name="remember"
class="rounded text-amber-600">


Se souvenir de moi


</label>


</div>









<button

class="w-full py-4 rounded-xl text-white font-bold text-lg shadow-lg transition

bg-gradient-to-r from-amber-500 to-orange-600

hover:scale-[1.02]"

>


<i class="fa-solid fa-right-to-bracket"></i>

Se connecter


</button>







</form>






<p class="text-center text-sm text-gray-400 mt-8">

© {{ date('Y') }} Resto Kay-Y - Tous droits réservés

</p>





</div>






</div>



</body>

</html>