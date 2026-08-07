@extends('caisse.layouts.app')

@section('title','Tableau de bord')

@section('content')

<div class="dashboard">

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

                <span>Chiffre d'affaires</span>

                <h2 id="caCounter">

                    {{ number_format($chiffreAffairesJour,2) }}

                </h2>

                <small>Aujourd'hui</small>

            </div>

        </div>

        <div class="stat-card orders">

            <div class="icon">

                <i class="fa-solid fa-bell-concierge"></i>

            </div>

            <div>

                <span>Commandes prêtes</span>

                <h2 id="readyCounter">

                    {{ $countPretes }}

                </h2>

                <small>À encaisser</small>

            </div>

        </div>

        <div class="stat-card attente">

            <div class="icon">

                <i class="fa-solid fa-clock"></i>

            </div>

            <div>

                <span>En attente</span>

                <h2>

                    {{ $countEnAttente }}

                </h2>

                <small>Cuisine</small>

            </div>

        </div>

        <div class="stat-card paiement">

            <div class="icon">

                <i class="fa-solid fa-credit-card"></i>

            </div>

            <div>

                <span>Paiements</span>

                <h2>

                    {{ $countPayeesJour }}

                </h2>

                <small>Aujourd'hui</small>

            </div>

        </div>

    </div>

        <div class="analytics-grid">

        <div class="chart-card">

            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-chart-line"></i>

                    Évolution du chiffre d'affaires

                </h3>

            </div>

            <canvas id="salesChart" height="120"></canvas>

        </div>

        <div class="mini-stats">

            <div class="mini-card success">

                <div>

                    <small>Espèces</small>

                    <h2>62%</h2>

                </div>

                <i class="fa-solid fa-money-bill-wave"></i>

            </div>

            <div class="mini-card primary">

                <div>

                    <small>Carte bancaire</small>

                    <h2>24%</h2>

                </div>

                <i class="fa-solid fa-credit-card"></i>

            </div>

            <div class="mini-card warning">

                <div>

                    <small>MonCash</small>

                    <h2>9%</h2>

                </div>

                <i class="fa-solid fa-mobile-screen-button"></i>

            </div>

            <div class="mini-card purple">

                <div>

                    <small>NatCash</small>

                    <h2>5%</h2>

                </div>

                <i class="fa-solid fa-wallet"></i>

            </div>

        </div>

    </div>

    <div class="analytics-grid two">

        <div class="chart-card">

            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-chart-pie"></i>

                    Répartition des paiements

                </h3>

            </div>

            <canvas id="paymentChart" height="180"></canvas>

        </div>

        <div class="chart-card">

            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-chart-column"></i>

                    Commandes encaissées

                </h3>

            </div>

            <canvas id="ordersChart" height="180"></canvas>

        </div>

    </div>
        <div class="table-card">

        <div class="card-header">

            <div>

                <h3>

                    <i class="fa-solid fa-receipt"></i>

                    Commandes prêtes à encaisser

                </h3>

                <small>

                    Toutes les commandes terminées par la cuisine.

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

                        <th>Montant</th>

                        <th>Statut</th>

                        <th>Heure</th>

                        <th>Mode</th>

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

                            <span class="table-number">

                                🍽️ Table

                                {{ $commande->restaurant_table_id }}

                            </span>

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

                            {{ \Carbon\Carbon::parse($commande->created_at)->format('H:i') }}

                        </td>

                        <td>

                            <span class="badge-mode">

                                À définir

                            </span>

                        </td>

                        <td>

                            <a

                                href="{{ url('/caisse/encaisser/'.$commande->id) }}"

                                class="btn-pay">

                                <i class="fa-solid fa-cash-register"></i>

                                Encaisser

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td

                            colspan="7"

                            class="empty">

                            <i

                                class="fa-solid fa-circle-check"

                                style="font-size:50px;color:#22c55e;margin-bottom:15px;display:block;">

                            </i>

                            Aucune commande en attente d'encaissement.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>
        <div class="analytics-grid two-columns">

        <!-- Derniers paiements -->

        <div class="table-card">

            <div class="card-header">

                <div>

                    <h3>

                        <i class="fa-solid fa-credit-card"></i>

                        Derniers paiements

                    </h3>

                    <small>

                        Historique des paiements enregistrés aujourd'hui.

                    </small>

                </div>

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

                                <strong>

                                    FAC-{{ str_pad($paiement->id,5,'0',STR_PAD_LEFT) }}

                                </strong>

                            </td>

                            <td>

                                #{{ $paiement->commande_id }}

                            </td>

                            <td>

                                <strong class="amount">

                                    {{ number_format($paiement->montant,2) }}

                                    HTG

                                </strong>

                            </td>

                            <td>

                                @switch($paiement->mode_paiement)

                                    @case('Espèces')

                                    <span class="badge badge-success">

                                        💵 Espèces

                                    </span>

                                    @break

                                    @case('Carte')

                                    <span class="badge badge-primary">

                                        💳 Carte

                                    </span>

                                    @break

                                    @case('MonCash')

                                    <span class="badge badge-warning">

                                        📱 MonCash

                                    </span>

                                    @break

                                    @case('NatCash')

                                    <span class="badge badge-purple">

                                        👛 NatCash

                                    </span>

                                    @break

                                    @default

                                    <span class="badge">

                                        {{ $paiement->mode_paiement }}

                                    </span>

                                @endswitch

                            </td>

                            <td>

                                {{ \Carbon\Carbon::parse($paiement->created_at)->format('d/m/Y H:i') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td

                                colspan="5"

                                class="empty">

                                Aucun paiement enregistré.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Résumé des moyens de paiement -->

        <div class="table-card">

            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-wallet"></i>

                    Répartition des paiements

                </h3>

            </div>

            <div class="payment-summary">

                <div class="payment-item">

                    <div class="left">

                        💵 Espèces

                    </div>

                    <strong>

                        {{ $cashCount ?? 0 }}

                    </strong>

                </div>

                <div class="payment-item">

                    <div class="left">

                        💳 Carte bancaire

                    </div>

                    <strong>

                        {{ $cardCount ?? 0 }}

                    </strong>

                </div>

                <div class="payment-item">

                    <div class="left">

                        📱 MonCash

                    </div>

                    <strong>

                        {{ $moncashCount ?? 0 }}

                    </strong>

                </div>

                <div class="payment-item">

                    <div class="left">

                        👛 NatCash

                    </div>

                    <strong>

                        {{ $natcashCount ?? 0 }}

                    </strong>

                </div>

                <div class="payment-item">

                    <div class="left">

                        🏦 Virement

                    </div>

                    <strong>

                        {{ $virementCount ?? 0 }}

                    </strong>

                </div>

            </div>

        </div>

    </div>
        <!-- Footer caisse -->

    <div class="caisse-footer">

        <div>

            <i class="fa-solid fa-shield-halved"></i>

            Système de caisse sécurisé

        </div>


        <div>

            <i class="fa-solid fa-clock"></i>

            Mise à jour automatique

        </div>


        <div>

            Version Resto Kay-Y v1.0

        </div>

    </div>



    <!-- Notifications Toast -->

    <div id="toastContainer"></div>



@endsection



@push('scripts')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<script>


// Evolution chiffre d'affaires

const salesCtx = document.getElementById('salesChart');


if(salesCtx){


    new Chart(salesCtx, {


        type:'line',


        data:{


            labels:@json($salesLabels ?? []),


            datasets:[{


                label:'Chiffre d’affaires',


                data:@json($salesData ?? []),


                tension:.4,


                fill:true


            }]


        },


        options:{


            responsive:true,


            plugins:{


                legend:{


                    display:false

                }


            }


        }


    });


}




// Répartition paiement


const paymentCtx = document.getElementById('paymentChart');


if(paymentCtx){


    new Chart(paymentCtx,{


        type:'doughnut',


        data:{


            labels:[

                'Espèces',

                'Carte',

                'MonCash',

                'NatCash'

            ],


            datasets:[{


                data:[

                    {{ $cashPercent ?? 62 }},

                    {{ $cardPercent ?? 24 }},

                    {{ $moncashPercent ?? 9 }},

                    {{ $natcashPercent ?? 5 }}

                ]


            }]


        },


        options:{


            responsive:true,


            plugins:{


                legend:{


                    position:'bottom'


                }


            }


        }


    });


}




// Commandes encaissées


const ordersCtx = document.getElementById('ordersChart');


if(ordersCtx){


    new Chart(ordersCtx,{


        type:'bar',


        data:{


            labels:@json($orderLabels ?? []),


            datasets:[{


                label:'Commandes',


                data:@json($orderData ?? [])


            }]


        },


        options:{


            responsive:true,


            plugins:{


                legend:{


                    display:false


                }


            }


        }


    });


}





// Toast notification


function showToast(message,type='success'){



    let toast=document.createElement('div');


    toast.className="toast "+type;


    toast.innerHTML=`


        <i class="fa-solid fa-bell"></i>

        ${message}


    `;



    document

    .getElementById('toastContainer')

    .appendChild(toast);




    setTimeout(()=>{


        toast.remove();


    },4000);



}




@if(session('success'))

showToast("{{ session('success') }}");


@endif



@if(session('error'))

showToast("{{ session('error') }}",'error');


@endif




// Rafraîchissement automatique toutes les 30 secondes


setInterval(()=>{


    console.log("Actualisation caisse...");


},30000);



</script>


@endpush