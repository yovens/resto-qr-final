@extends('layouts.app')


@section('content')


<div class="caisse-container">


    <div class="facture-card">



        <div class="facture-header">


            <div>


                <h1>

                    🍽️ Resto Kay-Y

                </h1>


                <p>

                    Cuisine haïtienne traditionnelle

                </p>


            </div>



            <div class="facture-number">


                <strong>

                    {{ $paiement->numero_facture }}

                </strong>


                <small>

                    {{ $paiement->created_at->format('d/m/Y H:i') }}

                </small>


            </div>



        </div>





        <hr>





        <div class="facture-info">


            <div>

                <span>

                    Commande

                </span>


                <strong>

                    #{{ $paiement->commande_id }}

                </strong>


            </div>



            <div>

                <span>

                    Table

                </span>


                <strong>

                    {{ $paiement->commande->restaurant_table_id }}

                </strong>


            </div>



            <div>

                <span>

                    Caissier

                </span>


                <strong>

                    {{ $paiement->caissier }}

                </strong>


            </div>



        </div>







        <table class="facture-table">


            <thead>


                <tr>

                    <th>

                        Article

                    </th>


                    <th>

                        Qté

                    </th>


                    <th>

                        Prix

                    </th>


                    <th>

                        Total

                    </th>


                </tr>


            </thead>



            <tbody>



            @foreach($paiement->commande->items as $item)


                <tr>


                    <td>

                        {{ $item->plat->nom }}

                    </td>


                    <td>

                        {{ $item->quantite }}

                    </td>


                    <td>

                        {{ number_format($item->prix,2) }}

                        HTG

                    </td>


                    <td>


                        {{ number_format($item->prix * $item->quantite,2) }}

                        HTG


                    </td>



                </tr>


            @endforeach



            </tbody>



        </table>








        <div class="facture-total">


            <div>


                Mode paiement :

                <strong>

                    {{ $paiement->mode_paiement }}

                </strong>


            </div>



            <h2>


                Total :

                {{ number_format($paiement->montant,2) }}

                HTG


            </h2>



        </div>








        <div class="facture-footer">


            <p>

                Merci de votre visite ❤️

            </p>


            <button

            onclick="window.print()"

            class="btn-pay">


                <i class="fa-solid fa-print"></i>

                Imprimer


            </button>



        </div>




    </div>



</div>


@endsection