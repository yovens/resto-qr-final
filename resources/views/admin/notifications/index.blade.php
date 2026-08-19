@extends('admin.layouts.app')

@section('content')

<div class="notifications-page">

    <!-- =========================
            HEADER
    ========================== -->
    <div class="notifications-header">

        <div class="notifications-title">

            <div class="notifications-icon">
                <i class="fa-solid fa-bell"></i>
            </div>

            <div>
                <h1>Centre de Notifications & Rappels</h1>
                <p>
                    Suivez les alertes importantes de votre restaurant :
                    stock critique, paiements des employés et rappels quotidiens.
                </p>
            </div>

        </div>


    </div>

    <!-- =========================
            STATISTIQUES
    ========================== -->

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-info">
                <h2>{{ $stockAlerts->count() }}</h2>
                <p>Alertes Stock</p>
            </div>

            <div class="stat-icon red">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-info">
                <h2>{{ $employes->count() }}</h2>
                <p>Salaires à payer</p>
            </div>

            <div class="stat-icon orange">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-info">
                <h2>{{ $stockAlerts->sum('quantite_actuelle') }}</h2>
                <p>Quantité Critique</p>
            </div>

            <div class="stat-icon green">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-info">
                <h2>{{ now()->format('d') }}</h2>
                <p>Jour du mois</p>
            </div>

            <div class="stat-icon blue">
                <i class="fa-solid fa-calendar-day"></i>
            </div>

        </div>

    </div>

    <!-- =========================
        ALERTES STOCK
    ========================== -->

    <div class="notification-card">

        <div class="card-header">

            <h2>
                <i class="fa-solid fa-triangle-exclamation"></i>
                Alertes de Stock Critique
            </h2>

            <span class="badge-count">
                {{ $stockAlerts->count() }}
            </span>

        </div>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>Produit</th>
                        <th>Quantité Actuelle</th>
                        <th>Seuil d'Alerte</th>
                        <th class="text-center">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($stockAlerts as $item)

                    <tr>

                        <td>

                            <strong class="stock-danger">
                                {{ $item->nom }}
                            </strong>

                        </td>

                        <td>

                            <span class="badge-danger">

                                {{ $item->quantite_actuelle }}
                                {{ $item->unite }}

                            </span>

                        </td>

                        <td>

                            {{ $item->seuil_alerte }}
                            {{ $item->unite }}

                        </td>

                        <td class="text-center">

                            <a href="/admin/stock-mouvement"
                               class="btn btn-green">

                                <i class="fa-solid fa-plus"></i>

                                Réapprovisionner

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="4">

                            <div class="empty-box">

                                <i class="fa-solid fa-circle-check"></i>

                                <h3>Aucune alerte de stock</h3>

                                <p>
                                    Tous les produits sont actuellement
                                    au-dessus du seuil critique.
                                </p>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <!-- =========================
        SALAIRES
    ========================== -->

    <div class="notification-card">

        <div class="card-header">

            <h2>

                <i class="fa-solid fa-money-check-dollar"></i>

                Paiement des Salaires

            </h2>

            <span class="badge-count">

                {{ $employes->count() }}

            </span>

        </div>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>Employé</th>
                        <th>Fonction</th>
                        <th>Téléphone</th>
                        <th>Salaire</th>
                        <th class="text-center">Paiement</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($employes as $emp)

                    <tr>

                        <td>

                            <strong>

                                {{ $emp->nom }}
                                {{ $emp->prenom }}

                            </strong>

                        </td>

                        <td>

                            <span class="badge-success">

                                {{ ucfirst($emp->role) }}

                            </span>

                        </td>

                        <td>

                            {{ $emp->telephone }}

                        </td>

                        <td>

                            <strong class="salary">

                                {{ number_format($emp->salaire,2) }}
                                HTG

                            </strong>

                        </td>

                        <td class="text-center">

                            <button class="btn btn-orange"
                                    onclick="alert('Paiement enregistré avec succès pour {{ $emp->nom }}');">

                                <i class="fa-solid fa-check"></i>

                                Marquer payé

                            </button>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5">

                            <div class="empty-box">

                                <i class="fa-solid fa-users"></i>

                                <h3>Aucun employé</h3>

                                <p>
                                    Aucun employé n'est enregistré
                                    dans le système.
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

<style>

    /* Collez ici le CSS que je vous ai fourni précédemment */
/*==================================================
        NOTIFICATIONS PAGE
==================================================*/

.notifications-page{
    padding:30px;
    max-width:1500px;
    margin:auto;
}

/*=========================
        HEADER
=========================*/

.notifications-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
    margin-bottom:35px;
}

.notifications-title{
    display:flex;
    align-items:center;
    gap:20px;
}

.notifications-icon{
    width:75px;
    height:75px;
    border-radius:20px;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    box-shadow:0 15px 35px rgba(245,158,11,.25);
}

.notifications-title h1{
    font-size:30px;
    color:#1f2937;
    font-weight:800;
    margin-bottom:5px;
}

.notifications-title p{
    color:#6b7280;
    font-size:15px;
}

.btn-dashboard{
    background:linear-gradient(135deg,#4b5563,#374151);
    color:white;
    padding:14px 24px;
    border-radius:14px;
    text-decoration:none;
    font-weight:700;
    transition:.35s;
    box-shadow:0 12px 25px rgba(0,0,0,.12);
}

.btn-dashboard:hover{
    transform:translateY(-3px);
}

/*=========================
        STATS
=========================*/

.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:22px;
    margin-bottom:35px;
}

.stat-card{
    background:white;
    border-radius:18px;
    padding:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 12px 35px rgba(0,0,0,.07);
    border:1px solid #ececec;
    transition:.35s;
}

.stat-card:hover{
    transform:translateY(-6px);
}

.stat-info h2{
    font-size:34px;
    color:#111827;
    margin-bottom:5px;
    font-weight:800;
}

.stat-info p{
    color:#6b7280;
    font-weight:600;
}

.stat-icon{
    width:70px;
    height:70px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:28px;
}

.red{
    background:linear-gradient(135deg,#ef4444,#dc2626);
}

.orange{
    background:linear-gradient(135deg,#f59e0b,#d97706);
}

.green{
    background:linear-gradient(135deg,#10b981,#059669);
}

.blue{
    background:linear-gradient(135deg,#3b82f6,#2563eb);
}

/*=========================
        CARDS
=========================*/

.notification-card{
    background:white;
    border-radius:22px;
    box-shadow:0 18px 40px rgba(0,0,0,.07);
    border:1px solid #ececec;
    overflow:hidden;
    margin-bottom:35px;
}

.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 28px;
    background:#fafafa;
    border-bottom:1px solid #ececec;
}

.card-header h2{
    font-size:22px;
    color:#1f2937;
    font-weight:800;
}

.badge-count{
    background:#f59e0b;
    color:white;
    padding:7px 16px;
    border-radius:30px;
    font-size:14px;
    font-weight:700;
}

/*=========================
        TABLE
=========================*/

.table-responsive{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#f9fafb;
}

thead th{
    padding:18px;
    font-size:13px;
    color:#6b7280;
    text-transform:uppercase;
    letter-spacing:1px;
    font-weight:700;
}

tbody td{
    padding:18px;
    border-top:1px solid #f3f4f6;
}

tbody tr{
    transition:.25s;
}

tbody tr:hover{
    background:#fffaf0;
}

.text-center{
    text-align:center;
}

/*=========================
        BADGES
=========================*/

.badge-danger{
    background:#fee2e2;
    color:#dc2626;
    padding:8px 14px;
    border-radius:30px;
    font-size:13px;
    font-weight:700;
}

.badge-success{
    background:#dcfce7;
    color:#15803d;
    padding:8px 14px;
    border-radius:30px;
    font-size:13px;
    font-weight:700;
}

.stock-danger{
    color:#dc2626;
}

.salary{
    color:#059669;
    font-size:16px;
}

/*=========================
        BUTTONS
=========================*/

.btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 18px;
    border-radius:12px;
    text-decoration:none;
    border:none;
    cursor:pointer;
    color:white;
    font-weight:700;
    transition:.35s;
}

.btn:hover{
    transform:translateY(-3px);
}

.btn-green{
    background:linear-gradient(135deg,#10b981,#059669);
    box-shadow:0 12px 25px rgba(16,185,129,.25);
}

.btn-orange{
    background:linear-gradient(135deg,#f59e0b,#d97706);
    box-shadow:0 12px 25px rgba(245,158,11,.25);
}

/*=========================
        EMPTY STATE
=========================*/

.empty-box{
    padding:60px 20px;
    text-align:center;
}

.empty-box i{
    font-size:60px;
    color:#d1d5db;
    margin-bottom:20px;
}

.empty-box h3{
    font-size:22px;
    color:#374151;
    margin-bottom:10px;
}

.empty-box p{
    color:#9ca3af;
}

/*=========================
        RESPONSIVE
=========================*/

@media(max-width:992px){

    .notifications-header{
        flex-direction:column;
        align-items:flex-start;
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

    .card-header{
        flex-direction:column;
        gap:12px;
        align-items:flex-start;
    }

    table{
        min-width:850px;
    }

}
</style>

@endsection