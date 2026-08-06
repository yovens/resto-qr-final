@extends('admin.layouts.app')

@section('content')

<div class="reports-page">

    <!-- ==========================================
                    HEADER
    =========================================== -->

    <div class="reports-header">

        <div class="reports-title">

            <div class="reports-icon">
                <i class="fa-solid fa-chart-pie"></i>
            </div>

            <div>

                <h1>Rapport Global & Financier</h1>

                <p>
                    Consultez les performances financières du restaurant,
                    les ventes mensuelles, annuelles ainsi que les statistiques
                    générales des commandes.
                </p>

            </div>

        </div>

        <!-- FILTRES -->

        <form action="/admin/reports"
              method="GET"
              class="filter-form">

            <div class="filter-group">

                @php

                    $months = [

                        '01'=>'Janvier',
                        '02'=>'Février',
                        '03'=>'Mars',
                        '04'=>'Avril',
                        '05'=>'Mai',
                        '06'=>'Juin',
                        '07'=>'Juillet',
                        '08'=>'Août',
                        '09'=>'Septembre',
                        '10'=>'Octobre',
                        '11'=>'Novembre',
                        '12'=>'Décembre'

                    ];

                @endphp

                <select name="month"
                        class="filter-select"
                        onchange="this.form.submit()">

                    @foreach($months as $num => $name)

                        <option value="{{ $num }}"
                            {{ $selectedMonth==$num ? 'selected' : '' }}>

                            {{ $name }}

                        </option>

                    @endforeach

                </select>

                <select name="year"
                        class="filter-select"
                        onchange="this.form.submit()">

                    @foreach($years as $yr)

                        <option value="{{ $yr }}"
                            {{ $selectedYear==$yr ? 'selected' : '' }}>

                            {{ $yr }}

                        </option>

                    @endforeach

                </select>

            </div>

        </form>

    </div>

    <!-- ==========================================
                    STATS
    =========================================== -->

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-info">

                <h2>
                    {{ number_format($ventesMois,2) }}
                    HTG
                </h2>

                <p>Ventes du mois</p>

            </div>

            <div class="stat-icon green">

                <i class="fa-solid fa-calendar-days"></i>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-info">

                <h2>

                    {{ number_format($ventesAnnee,2) }}

                    HTG

                </h2>

                <p>

                    Ventes {{ $selectedYear }}

                </p>

            </div>

            <div class="stat-icon orange">

                <i class="fa-solid fa-chart-line"></i>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-info">

                <h2>

                    {{ number_format($totalVentesGlobal,2) }}

                    HTG

                </h2>

                <p>

                    Chiffre d'affaires

                </p>

            </div>

            <div class="stat-icon blue">

                <i class="fa-solid fa-wallet"></i>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-info">

                <h2>

                    {{ $commandesMoisCount }}

                </h2>

                <p>

                    Commandes du mois

                </p>

            </div>

            <div class="stat-icon red">

                <i class="fa-solid fa-receipt"></i>

            </div>

        </div>

    </div>
<!-- ==========================================
                GRAPHIQUE DES VENTES
=========================================== -->

<div class="report-card chart-card">

    <div class="card-header">

        <div>
            <h2>
                <i class="fa-solid fa-chart-line"></i>
                Évolution des ventes
            </h2>

            <p>
                Performance mensuelle de l'année {{ $selectedYear }}
            </p>
        </div>

    </div>


    <div class="chart-container">

        <canvas id="salesChart"></canvas>

    </div>

</div>
    <!-- ==========================================
                    RAPPORT MENSUEL
    =========================================== -->

    <div class="report-card">

        <div class="card-header">

            <div>

                <h2>

                    <i class="fa-solid fa-chart-column"></i>

                    Résumé Mensuel

                </h2>

                <p>

                    Année :

                    <strong>{{ $selectedYear }}</strong>

                </p>

            </div>

            <span class="badge-count">

                {{ count($monthlyStats) }}

                Mois

            </span>

        </div>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>Mois</th>

                        <th>Nombre de commandes</th>

                        <th class="text-right">

                            Chiffre d'affaires

                        </th>

                    </tr>

                </thead>

                <tbody>

                @forelse($monthlyStats as $stat)

                    <tr>

                        <td>

                            <strong>

                                {{ $months[str_pad($stat->mois,2,'0',STR_PAD_LEFT)] ?? $stat->mois }}

                            </strong>

                        </td>

                        <td>

                            <span class="badge-blue">

                                {{ $stat->total_commandes }}

                                commandes

                            </span>

                        </td>

                        <td class="text-right">

                            <strong class="amount">

                                {{ number_format($stat->total_ventes,2) }}

                                HTG

                            </strong>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3">

                            <div class="empty-box">

                                <i class="fa-solid fa-folder-open"></i>

                                <h3>

                                    Aucun rapport disponible

                                </h3>

                                <p>

                                    Aucune commande payée n'a été enregistrée
                                    durant l'année

                                    {{ $selectedYear }}.

                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

const ctx = document.getElementById('salesChart');


new Chart(ctx, {

    type: 'line',


    data: {

        labels: [

            @foreach($monthlyStats as $stat)

                "{{ $months[str_pad($stat->mois,2,'0',STR_PAD_LEFT)] ?? $stat->mois }}",

            @endforeach

        ],


        datasets: [

            {

                label: "Ventes HTG",

                data: [

                    @foreach($monthlyStats as $stat)

                        {{ $stat->total_ventes }},

                    @endforeach

                ],


                borderWidth:3,

                tension:0.4,

                fill:true,

                backgroundColor:"rgba(37,99,235,0.15)",

                borderColor:"#2563eb",

                pointRadius:5,

                pointBackgroundColor:"#2563eb"

            }

        ]

    },


    options:{


        responsive:true,


        maintainAspectRatio:false,


        plugins:{


            legend:{


                display:true,


                position:'top'


            }

        },


        scales:{


            y:{


                beginAtZero:true,


                ticks:{


                    callback:function(value){

                        return value+" HTG";

                    }


                }

            }

        }

    }


});

</script>
<style>
/* =========================================
        GRAPHIQUE RAPPORT
========================================= */


.report-card{

    background:white;

    border-radius:22px;

    overflow:hidden;

    box-shadow:0 15px 40px rgba(0,0,0,.06);

    border:1px solid #ececec;

    margin-bottom:35px;

}



.chart-card{

    padding-bottom:25px;

}



.chart-container{

    height:380px;

    padding:25px 35px;

}



.chart-container canvas{

    width:100%!important;

    height:100%!important;

}



/* Animation */

.chart-card{

    animation:fadeChart .5s ease;

}



@keyframes fadeChart{


    from{

        opacity:0;

        transform:translateY(20px);

    }


    to{

        opacity:1;

        transform:translateY(0);

    }


}
    /* Collez ici le CSS du rapport fourni précédemment */
/*=========================================
            RAPPORTS PAGE
=========================================*/

.reports-page,
.notifications-page{
    width:100%;
    padding:30px;
}

/*=========================================
            HEADER
=========================================*/

.notifications-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    flex-wrap:wrap;
    margin-bottom:35px;
}

.notifications-title{
    display:flex;
    align-items:center;
    gap:20px;
}

.notifications-icon{
    width:80px;
    height:80px;
    border-radius:22px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:34px;
    box-shadow:0 15px 35px rgba(37,99,235,.25);
}

.notifications-title h1{
    font-size:30px;
    color:#1f2937;
    font-weight:800;
    margin-bottom:6px;
}

.notifications-title p{
    color:#6b7280;
    font-size:15px;
    line-height:1.6;
}

/*=========================================
            FILTER
=========================================*/

.filter-form{
    display:flex;
    gap:15px;
}

.filter-group{
    display:flex;
    gap:15px;
}

.filter-select{
    min-width:170px;
    padding:13px 18px;
    border-radius:14px;
    border:1px solid #d1d5db;
    background:#fff;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.35s;
}

.filter-select:focus{
    outline:none;
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
}

/*=========================================
            STATS
=========================================*/

.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:25px;
    margin-bottom:35px;
}

.stat-card{
    background:#fff;
    border-radius:20px;
    padding:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 15px 40px rgba(0,0,0,.06);
    transition:.35s;
    border:1px solid #ececec;
}

.stat-card:hover{
    transform:translateY(-8px);
}

.stat-info h2{
    font-size:30px;
    font-weight:800;
    color:#111827;
    margin-bottom:8px;
}

.stat-info p{
    color:#6b7280;
    font-weight:600;
}

.stat-icon{
    width:72px;
    height:72px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:28px;
}

.green{
    background:linear-gradient(135deg,#10b981,#059669);
}

.orange{
    background:linear-gradient(135deg,#f59e0b,#d97706);
}

.blue{
    background:linear-gradient(135deg,#3b82f6,#2563eb);
}

.red{
    background:linear-gradient(135deg,#ef4444,#dc2626);
}

/*=========================================
            CARD
=========================================*/

.notification-card{
    background:white;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.06);
    border:1px solid #ececec;
}

.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 30px;
    background:#fafafa;
    border-bottom:1px solid #ececec;
}

.card-header h2{
    font-size:22px;
    font-weight:800;
    color:#1f2937;
}

.badge-count{
    background:#2563eb;
    color:#fff;
    padding:8px 18px;
    border-radius:30px;
    font-weight:700;
    font-size:13px;
}

/*=========================================
            TABLE
=========================================*/

.table-responsive{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#f8fafc;
}

thead th{
    padding:18px;
    color:#6b7280;
    text-transform:uppercase;
    font-size:13px;
    letter-spacing:1px;
}

tbody td{
    padding:18px;
    border-top:1px solid #f1f5f9;
}

tbody tr{
    transition:.3s;
}

tbody tr:hover{
    background:#f9fbff;
}

.text-right{
    text-align:right;
}

/*=========================================
            BADGES
=========================================*/

.badge-blue{
    background:#dbeafe;
    color:#1d4ed8;
    padding:8px 15px;
    border-radius:25px;
    font-size:13px;
    font-weight:700;
}

.salary{
    color:#059669;
    font-size:17px;
    font-weight:800;
}

/*=========================================
            EMPTY
=========================================*/

.empty-box{
    padding:60px 20px;
    text-align:center;
}

.empty-box i{
    font-size:60px;
    color:#cbd5e1;
    margin-bottom:20px;
}

.empty-box h3{
    font-size:22px;
    color:#374151;
    margin-bottom:10px;
}

.empty-box p{
    color:#6b7280;
}

/*=========================================
            RESPONSIVE
=========================================*/

@media(max-width:992px){

.notifications-header{
    flex-direction:column;
    align-items:flex-start;
}

.filter-group{
    width:100%;
}

.filter-select{
    width:100%;
}

}

@media(max-width:768px){

.notifications-title{
    flex-direction:column;
    text-align:center;
}

.notifications-title h1{
    font-size:24px;
}

.stats-grid{
    grid-template-columns:1fr;
}

.card-header{
    flex-direction:column;
    gap:10px;
    align-items:flex-start;
}

table{
    min-width:700px;
}

}
</style>

@endsection



