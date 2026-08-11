@extends('caisse.layouts.app')

@section('content')


<div class="caisse-container">


    <div class="table-card paiement-box">


        <div class="card-header">


            <div>

                <h3>

                    <i class="fa-solid fa-cash-register"></i>

                    Encaissement commande

                </h3>


                <small>

                    Validation du paiement client

                </small>

            </div>


            <span class="badge-ready">

                Commande #{{ $commande->id }}

            </span>


        </div>





        <div class="commande-resume">


            <div class="resume-card">


                <span>

                    🍽️ Table

                </span>


                <strong>

                    {{ $commande->restaurant_table_id }}

                </strong>


            </div>





            <div class="resume-card">


                <span>

                    💰 Total

                </span>


                <strong class="amount">


                    {{ number_format($commande->total,2) }}

                    HTG


                </strong>


            </div>





            <div class="resume-card">


                <span>

                    🕒 Heure

                </span>


                <strong>


                    {{ $commande->created_at->format('H:i') }}


                </strong>


            </div>


        </div>







        <form method="POST" action="{{ route('caisse.paiement') }}">


            @csrf



            <input

                type="hidden"

                name="commande_id"

                value="{{ $commande->id }}">





            <input

                type="hidden"

                name="montant"

                value="{{ $commande->total }}">







            <h4 class="payment-title">


                Choisir le mode de paiement


            </h4>





            <div class="payment-methods">





                <label class="method-card">


                    <input

                    type="radio"

                    name="mode_paiement"

                    value="Espèces"

                    required>



                    <div>


                        <i class="fa-solid fa-money-bill-wave"></i>


                        <span>

                            Espèces

                        </span>


                    </div>


                </label>







                <label class="method-card">


                    <input

                    type="radio"

                    name="mode_paiement"

                    value="Carte">



                    <div>


                        <i class="fa-solid fa-credit-card"></i>


                        <span>

                            Carte bancaire

                        </span>


                    </div>


                </label>







                <label class="method-card">


                    <input

                    type="radio"

                    name="mode_paiement"

                    value="MonCash">



                    <div>


                        <i class="fa-solid fa-mobile-screen-button"></i>


                        <span>

                            MonCash

                        </span>


                    </div>


                </label>








                <label class="method-card">


                    <input

                    type="radio"

                    name="mode_paiement"

                    value="NatCash">



                    <div>


                        <i class="fa-solid fa-wallet"></i>


                        <span>

                            NatCash

                        </span>


                    </div>


                </label>







                <label class="method-card">


                    <input

                    type="radio"

                    name="mode_paiement"

                    value="Virement">



                    <div>


                        <i class="fa-solid fa-building-columns"></i>


                        <span>

                            Virement

                        </span>


                    </div>


                </label>



            </div>







            <button

                type="submit"

                class="btn-pay validate-pay">


                <i class="fa-solid fa-check"></i>


                Confirmer le paiement


            </button>





        </form>



    </div>



</div>


@endsection