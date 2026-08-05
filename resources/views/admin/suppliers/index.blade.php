@extends('admin.layouts.app')

@section('content')

<div class="supplier-page">


    <!-- HEADER -->

    <div class="supplier-header">


        <div>

            <h1>
                Gestion des Fournisseurs
            </h1>


            <p>
                Liste de tous les partenaires et fournisseurs du restaurant
            </p>

        </div>




        <a href="/admin/suppliers/create"
           class="supplier-btn">


            <i class="fa-solid fa-truck-field"></i>

            Nouveau Fournisseur


        </a>


    </div>







    <!-- SUCCESS -->

    @if(session('success'))


    <div class="supplier-success">


        <i class="fa-solid fa-circle-check"></i>

        {{ session('success') }}


    </div>


    @endif







    <!-- TABLE -->


    <div class="supplier-table-card">


        <table class="supplier-table">


            <thead>


                <tr>


                    <th>
                        Entreprise
                    </th>


                    <th>
                        Contact
                    </th>


                    <th>
                        Téléphone
                    </th>


                    <th>
                        Produits Fournis
                    </th>


                    <th>
                        Actions
                    </th>


                </tr>


            </thead>





            <tbody>



            @forelse($suppliers as $sup)



                <tr>



                    <td>


                        <div class="company-name">


                            <div class="company-icon">

                                <i class="fa-solid fa-building"></i>

                            </div>


                            {{ $sup->nom_entreprise }}


                        </div>


                    </td>






                    <td>

                        {{ $sup->nom_contact }}

                    </td>





                    <td>

                        <span class="phone-badge">

                            <i class="fa-solid fa-phone"></i>

                            {{ $sup->telephone }}

                        </span>


                    </td>







                    <td>


                        <span class="product-badge">


                            {{ $sup->produits_fournis ?? 'Général' }}


                        </span>


                    </td>








                    <td>


                        <div class="supplier-actions">



                            <a href="/admin/suppliers/{{ $sup->id }}"
                               class="action-view"
                               title="Voir">


                                <i class="fa-solid fa-eye"></i>


                            </a>





                            <a href="/admin/suppliers/{{ $sup->id }}/edit"
                               class="action-edit"
                               title="Modifier">


                                <i class="fa-solid fa-pen-to-square"></i>


                            </a>






                            <form action="/admin/suppliers/{{ $sup->id }}"
                                  method="POST"
                                  onsubmit="return confirm('Voulez-vous vraiment supprimer ce fournisseur ?');">


                                @csrf

                                @method('DELETE')



                                <button class="action-delete"
                                        title="Supprimer">


                                    <i class="fa-solid fa-trash"></i>


                                </button>



                            </form>




                        </div>


                    </td>



                </tr>





            @empty



                <tr>


                    <td colspan="5"
                        class="empty-data">


                        Aucun fournisseur enregistré pour le moment.


                    </td>


                </tr>



            @endforelse



            </tbody>



        </table>


    </div>







    <!-- PAGINATION -->


    <div class="supplier-pagination">


        {{ $suppliers->links() }}


    </div>



</div>
<style>
    /*=================================
    SUPPLIERS MANAGEMENT
=================================*/


.supplier-page{

    padding:30px;

}





.supplier-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:30px;

}



.supplier-header h1{

    font-size:28px;

    font-weight:900;

    color:#1f2937;

}



.supplier-header p{

    color:#6b7280;

}





.supplier-btn{

    background:

    linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );


    color:white;

    padding:13px 22px;

    border-radius:14px;

    font-weight:800;

    text-decoration:none;

    display:flex;

    gap:10px;

    align-items:center;

    box-shadow:
    0 10px 25px rgba(245,158,11,.25);

    transition:.3s;

}



.supplier-btn:hover{

    transform:translateY(-3px);

}






/* SUCCESS */


.supplier-success{

    background:#ecfdf5;

    color:#047857;

    border-left:5px solid #10b981;

    padding:16px;

    border-radius:15px;

    margin-bottom:20px;

    font-weight:700;

}






/* TABLE */


.supplier-table-card{

    background:white;

    border-radius:22px;

    overflow:hidden;

    box-shadow:
    0 15px 40px rgba(0,0,0,.08);

    border:1px solid #e5e7eb;

}



.supplier-table{

    width:100%;

    border-collapse:collapse;

}



.supplier-table thead{

    background:#f9fafb;

}



.supplier-table th{

    padding:18px;

    text-transform:uppercase;

    font-size:12px;

    color:#374151;

}



.supplier-table td{

    padding:18px;

    border-top:1px solid #f3f4f6;

    color:#4b5563;

}



.supplier-table tbody tr{

    transition:.3s;

}



.supplier-table tbody tr:hover{

    background:#fffbeb;

}







/* COMPANY */


.company-name{

    display:flex;

    align-items:center;

    gap:12px;

    font-weight:800;

    color:#111827;

}



.company-icon{

    width:40px;

    height:40px;

    border-radius:12px;

    background:#fef3c7;

    color:#d97706;

    display:flex;

    justify-content:center;

    align-items:center;

}






/* BADGES */


.phone-badge{

    background:#eff6ff;

    color:#2563eb;

    padding:7px 12px;

    border-radius:30px;

    font-size:13px;

    font-weight:700;

}



.product-badge{

    background:#fffbeb;

    color:#b45309;

    border:1px solid #fde68a;

    padding:7px 14px;

    border-radius:30px;

    font-size:12px;

    font-weight:800;

}






/* ACTIONS */


.supplier-actions{

    display:flex;

    justify-content:center;

    gap:15px;

}



.supplier-actions a,
.action-delete{

    font-size:18px;

    transition:.3s;

    border:none;

    background:none;

    cursor:pointer;

}



.supplier-actions a:hover,
.action-delete:hover{

    transform:scale(1.2);

}



.action-view{

    color:#2563eb;

}


.action-edit{

    color:#d97706;

}


.action-delete{

    color:#dc2626;

}





.empty-data{

    padding:40px;

    text-align:center;

    color:#6b7280;

}





@media(max-width:768px){


.supplier-header{

    flex-direction:column;

    align-items:flex-start;

    gap:20px;

}



.supplier-table-card{

    overflow-x:auto;

}


.supplier-table{

    min-width:900px;

}


}
</style>

@endsection