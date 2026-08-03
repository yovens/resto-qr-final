<h1>FACTURE RESTAURANT</h1>

<p>Commande #{{ $commande->id }}</p>
<p>Table: {{ $commande->restaurant_table_id }}</p>

<hr>

@foreach($commande->items as $item)
    <p>
        {{ $item->plat->nom }}
        x {{ $item->quantite }}
        = {{ $item->prix * $item->quantite }} HTG
    </p>
@endforeach

<hr>

<h3>Total: {{ $commande->total }} HTG</h3>