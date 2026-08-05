@extends('admin.layouts.app')

@section('content')

<div class="stock-page">

    <!-- HEADER -->
    <div class="stock-header">

        <div>
            <h1>Gestion des Stocks</h1>
            <p>Suivi des entrées, sorties et alertes de stock</p>
        </div>


        <div class="stock-actions">

            <a href="/admin/stock-mouvement"
               class="stock-btn green">

                <i class="fa-solid fa-right-left"></i>
                Mouvement Stock

            </a>


            <a href="/admin/stock/create"
               class="stock-btn orange">

                <i class="fa-solid fa-plus"></i>
                Nouveau Produit

            </a>

        </div>

    </div>



    <!-- MESSAGE SUCCESS -->

    @if(session('success'))

        <div class="alert-success">

            {{ session('success') }}

        </div>

    @endif




    <!-- ALERT STOCK BAS -->

    @if(isset($alertes) && $alertes->count() > 0)


    <div class="stock-alert">


        <h3>
            <i class="fa-solid fa-triangle-exclamation"></i>

            Alertes de Stock Bas
        </h3>


        <ul>


        @foreach($alertes as $alt)

            <li>

                <strong>{{ $alt->nom }}</strong>

                :
                Il ne reste que

                <strong>
                    {{ $alt->quantite_actuelle }}
                    {{ $alt->unite }}
                </strong>

                (Seuil :
                {{ $alt->seuil_alerte }})

            </li>


        @endforeach


        </ul>


    </div>


    @endif






    <!-- TABLE STOCK -->

    <div class="stock-table-card">


        <table class="stock-table">


            <thead>

                <tr>

                    <th>
                        Nom Produit
                    </th>


                    <th>
                        Unité
                    </th>


                    <th>
                        Quantité
                    </th>


                    <th>
                        Seuil Alerte
                    </th>


                    <th>
                        Statut
                    </th>


                    <th>
                        Actions
                    </th>


                </tr>

            </thead>



            <tbody>



            @forelse($products as $prod)



                <tr>


                    <td>

                        <strong>
                            {{ $prod->nom }}
                        </strong>

                    </td>



                    <td>

                        {{ $prod->unite }}

                    </td>



                    <td>


                    @if($prod->quantite_actuelle <= $prod->seuil_alerte)


                        <strong style="color:#dc2626">

                            {{ $prod->quantite_actuelle }}

                        </strong>


                    @else


                        <strong style="color:#059669">

                            {{ $prod->quantite_actuelle }}

                        </strong>


                    @endif


                    </td>




                    <td>

                        {{ $prod->seuil_alerte }}

                    </td>





                    <td>


                    @if($prod->quantite_actuelle <= $prod->seuil_alerte)


                        <span class="stock-status stock-low">

                            Stock Bas

                        </span>


                    @else


                        <span class="stock-status stock-normal">

                            Normal

                        </span>


                    @endif


                    </td>





                    <td>


                        <a href="/admin/stock/{{ $prod->id }}/edit"
                           class="stock-action"
                           style="color:#d97706"
                           title="Modifier">


                            <i class="fa-solid fa-pen-to-square"></i>


                        </a>





                        <form action="/admin/stock/{{ $prod->id }}"
                              method="POST"
                              style="display:inline"
                              onsubmit="return confirm('Supprimer ce produit ?');">


                            @csrf

                            @method('DELETE')



                            <button type="submit"
                                    class="stock-action"
                                    style="border:none;background:none;color:#dc2626;cursor:pointer"
                                    title="Supprimer">


                                <i class="fa-solid fa-trash"></i>


                            </button>



                        </form>



                    </td>



                </tr>



            @empty



                <tr>


                    <td colspan="6"
                        style="text-align:center;padding:40px;color:#6b7280">


                        Aucun produit dans le stock pour le moment.


                    </td>


                </tr>



            @endforelse



            </tbody>



        </table>


    </div>


</div>

<style>
    /*=================================
    STOCK MANAGEMENT PAGE
=================================*/

.stock-page{
    padding:25px;
    max-width:1400px;
    margin:auto;
}

/* HEADER */

.stock-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.stock-header h1{
    font-size:28px;
    font-weight:800;
    color:#1f2937;
    margin:0;
}

.stock-header p{
    color:#6b7280;
    margin-top:5px;
}


/* BUTTONS */

.stock-actions{
    display:flex;
    gap:12px;
}

.stock-btn{

    padding:12px 20px;
    border-radius:12px;
    color:white;
    font-weight:700;
    text-decoration:none;

    display:flex;
    align-items:center;
    gap:8px;

    transition:.3s;
    box-shadow:0 8px 20px rgba(0,0,0,.12);
}


.stock-btn:hover{

    transform:translateY(-3px);
}


.stock-btn.green{

    background:linear-gradient(
        135deg,
        #10b981,
        #059669
    );
}


.stock-btn.orange{

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );
}


/* SUCCESS ALERT */

.alert-success{

    background:#ecfdf5;
    color:#047857;

    border-left:5px solid #10b981;

    padding:16px;
    border-radius:14px;

    margin-bottom:20px;

    font-weight:600;

}



/* STOCK ALERT BOX */

.stock-alert{

    background:#fff1f2;

    border-left:6px solid #ef4444;

    padding:20px;

    border-radius:16px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);

    margin-bottom:25px;

}


.stock-alert h3{

    color:#b91c1c;

    font-size:18px;

    font-weight:800;

}


.stock-alert li{

    margin:8px 0;

    color:#dc2626;

}



/* TABLE CARD */


.stock-table-card{

    background:white;

    border-radius:20px;

    overflow:hidden;

    box-shadow:
    0 15px 40px rgba(0,0,0,.08);

    border:1px solid #e5e7eb;

}


.stock-table{

    width:100%;

    border-collapse:collapse;

}



.stock-table thead{

    background:#f9fafb;

}



.stock-table th{

    padding:18px;

    font-size:12px;

    text-transform:uppercase;

    color:#374151;

    letter-spacing:.5px;

}



.stock-table td{

    padding:18px;

    color:#4b5563;

    border-top:1px solid #f3f4f6;

}



.stock-table tbody tr{

    transition:.25s;

}



.stock-table tbody tr:hover{

    background:#f9fafb;

    transform:scale(1.01);

}



/* STATUS BADGE */


.stock-status{

    padding:7px 15px;

    border-radius:30px;

    font-size:12px;

    font-weight:800;

}


.stock-low{

    background:#fee2e2;

    color:#dc2626;

}


.stock-normal{

    background:#d1fae5;

    color:#047857;

}



/* ACTION ICONS */


.stock-action{

    font-size:18px;

    transition:.3s;

}


.stock-action:hover{

    transform:scale(1.2);

}



/* RESPONSIVE TABLE */


@media(max-width:768px){


.stock-header{

    flex-direction:column;

    align-items:flex-start;

    gap:20px;

}


.stock-actions{

    width:100%;

    flex-direction:column;

}


.stock-btn{

    justify-content:center;

}


.stock-table-card{

    overflow-x:auto;

}


.stock-table{

    min-width:800px;

}


}
</style>
@endsection