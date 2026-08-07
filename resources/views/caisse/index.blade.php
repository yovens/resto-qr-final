@extends('caisse.layouts.app')


@section('content')


<div class="welcome-card">

    <div>

        <h2>
            Bonjour {{ auth()->user()->name }}
        </h2>


        <p>
            Bienvenue dans votre espace de caisse.
            Consultez les commandes prêtes et encaissez les paiements.
        </p>

    </div>


    <div class="welcome-icon">

        <i class="fa-solid fa-cash-register"></i>

    </div>

</div>





<div class="stats-grid">


    <div class="stat-card revenue">

        <div class="icon">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>


        <div>

            <span>
                Chiffre d'affaires
            </span>


            <h2>
                {{ number_format($chiffreAffairesJour,2) }}
                HTG
            </h2>


            <small>
                Aujourd'hui
            </small>

        </div>

    </div>





    <div class="stat-card orders">


        <div class="icon">

            <i class="fa-solid fa-bell-concierge"></i>

        </div>


        <div>

            <span>
                Commandes prêtes
            </span>


            <h2>
                {{ $countPretes }}
            </h2>


            <small>
                À encaisser
            </small>


        </div>


    </div>





    <div class="stat-card attente">


        <div class="icon">

            <i class="fa-solid fa-clock"></i>

        </div>


        <div>

            <span>
                En attente
            </span>


            <h2>
                {{ $countEnAttente }}
            </h2>


            <small>
                Cuisine
            </small>

        </div>


    </div>





    <div class="stat-card paiement">


        <div class="icon">

            <i class="fa-solid fa-credit-card"></i>

        </div>


        <div>

            <span>
                Paiements
            </span>


            <h2>
                {{ $countPayeesJour }}
            </h2>


            <small>
                Aujourd'hui
            </small>

        </div>


    </div>


</div>







<!-- COMMANDES PRETES A ENCAISSER -->


<div class="table-card">


    <div class="card-header">


        <div>

            <h3>

                <i class="fa-solid fa-receipt"></i>

                Commandes prêtes à encaisser

            </h3>


            <small>

                Commandes terminées par la cuisine.

            </small>

        </div>



        <span class="count-badge">

            {{ $countPretes }}

            commande(s)

        </span>


    </div>





    <div class="table-responsive">


        <table class="premium-table">


            <thead>

                <tr>

                    <th>#</th>

                    <th>Table</th>

                    <th>Total</th>

                    <th>Statut</th>

                    <th>Heure</th>

                    <th>Action</th>

                </tr>


            </thead>




            <tbody>



            @forelse($commandesPretes as $commande)



                <tr>


                    <td>

                        <strong>
                            #{{ $commande->id }}
                        </strong>

                    </td>



                    <td>

                        🍽️ Table

                        {{ $commande->restaurant_table_id }}

                    </td>




                    <td>

                        <strong class="amount">

                            {{ number_format($commande->total,2) }}

                            HTG

                        </strong>

                    </td>




                    <td>

                        <span class="badge-ready">

                            <i class="fa-solid fa-circle-check"></i>

                            Prête

                        </span>

                    </td>




                    <td>

                        {{ $commande->created_at->format('H:i') }}

                    </td>




                    <td>


                        <a

                        href="{{ route('caisse.encaisser',$commande->id) }}"

                        class="btn-pay">


                            <i class="fa-solid fa-cash-register"></i>

                            Encaisser


                        </a>


                    </td>



                </tr>



            @empty



                <tr>

                    <td colspan="6" class="empty">


                        <i class="fa-solid fa-circle-check"></i>


                        <br>


                        Aucune commande prête.


                    </td>

                </tr>



            @endforelse




            </tbody>


        </table>


    </div>


</div>








<!-- DERNIERS PAIEMENTS -->


<div class="table-card">


<div class="card-header">


<h3>

<i class="fa-solid fa-credit-card"></i>

Derniers paiements

</h3>


</div>





<div class="table-responsive">


<table class="premium-table">


<thead>

<tr>

<th>Facture</th>

<th>Commande</th>

<th>Montant</th>

<th>Mode</th>

<th>Date</th>

</tr>

</thead>




<tbody>


@forelse($derniersPaiements as $paiement)



<tr>


<td>

FAC-{{ str_pad($paiement->id,5,'0',STR_PAD_LEFT) }}

</td>



<td>

#{{ $paiement->commande_id }}

</td>



<td>

{{ number_format($paiement->montant,2) }}

HTG

</td>



<td>

{{ $paiement->mode_paiement }}

</td>



<td>

{{ $paiement->created_at->format('d/m/Y H:i') }}

</td>


</tr>



@empty


<tr>

<td colspan="5">

Aucun paiement.

</td>


</tr>


@endforelse



</tbody>


</table>


</div>


</div>







<!-- RESUME PAIEMENT -->


<div class="table-card">


<h3>

<i class="fa-solid fa-wallet"></i>

Répartition paiements

</h3>



<p>
💵 Espèces :
{{ $cashCount ?? 0 }}
</p>


<p>
💳 Carte :
{{ $cardCount ?? 0 }}
</p>


<p>
📱 MonCash :
{{ $moncashCount ?? 0 }}
</p>


<p>
👛 NatCash :
{{ $natcashCount ?? 0 }}
</p>


<p>
🏦 Virement :
{{ $virementCount ?? 0 }}
</p>



</div>






@endsection



@push('scripts')


<script>

console.log("Caisse dashboard OK");

</script>


@endpush