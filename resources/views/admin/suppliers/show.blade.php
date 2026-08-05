@extends('admin.layouts.app')

@section('content')

<div class="supplier-show-page">


    <div class="supplier-profile-card">



        <!-- HEADER PROFILE -->


        <div class="supplier-profile-header">


            <div class="supplier-logo">


                <i class="fa-solid fa-building"></i>


            </div>



            <h1>
                {{ $supplier->nom_entreprise }}
            </h1>



            <p>
                Fournisseur Partenaire
            </p>


        </div>







        <!-- INFORMATION -->


        <div class="supplier-info-list">





            <div class="supplier-info-item">


                <div class="info-label">

                    <i class="fa-solid fa-user"></i>

                    Contact

                </div>


                <strong>
                    {{ $supplier->nom_contact }}
                </strong>


            </div>






            <div class="supplier-info-item">


                <div class="info-label">

                    <i class="fa-solid fa-envelope"></i>

                    Email

                </div>


                <strong>
                    {{ $supplier->email }}
                </strong>


            </div>







            <div class="supplier-info-item">


                <div class="info-label">

                    <i class="fa-solid fa-phone"></i>

                    Téléphone

                </div>


                <strong>
                    {{ $supplier->telephone }}
                </strong>


            </div>








            <div class="supplier-info-item">


                <div class="info-label">

                    <i class="fa-solid fa-box"></i>

                    Produits Fournis

                </div>



                <span class="supplier-product-tag">

                    {{ $supplier->produits_fournis ?? 'Non spécifié' }}

                </span>


            </div>









            <div class="supplier-address">


                <div class="info-label">


                    <i class="fa-solid fa-location-dot"></i>

                    Adresse


                </div>



                <strong>

                    {{ $supplier->adresse ?? 'Aucune adresse renseignée' }}

                </strong>



            </div>





        </div>









        <!-- ACTIONS -->


        <div class="supplier-show-actions">



            <a href="/admin/suppliers"
               class="show-back-btn">


                <i class="fa-solid fa-arrow-left"></i>

                Retour


            </a>







            <a href="/admin/suppliers/{{ $supplier->id }}/edit"
               class="show-edit-btn">


                <i class="fa-solid fa-pen-to-square"></i>

                Modifier


            </a>




        </div>




    </div>



</div>
<style>
    /*====================================
    SUPPLIER PROFILE SHOW
====================================*/


.supplier-show-page{

    min-height:85vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:40px;

    background:

    linear-gradient(
        135deg,
        #fffbeb,
        #f8fafc
    );

}





.supplier-profile-card{


    width:100%;


    max-width:650px;


    background:white;


    border-radius:30px;


    padding:40px;


    box-shadow:


    0 25px 60px rgba(0,0,0,.12);


    position:relative;


    overflow:hidden;


    animation:showCard .5s ease;


}



.supplier-profile-card::before{


    content:"";


    position:absolute;


    top:0;


    left:0;


    width:100%;


    height:6px;


    background:


    linear-gradient(
        90deg,
        #f59e0b,
        #d97706
    );


}





@keyframes showCard{


    from{

        opacity:0;

        transform:translateY(30px);

    }


    to{

        opacity:1;

        transform:translateY(0);

    }

}








/* HEADER */


.supplier-profile-header{


    text-align:center;


    margin-bottom:35px;


}



.supplier-logo{


    width:100px;


    height:100px;


    margin:auto;


    border-radius:50%;


    display:flex;


    justify-content:center;


    align-items:center;


    font-size:45px;


    color:#b45309;


    background:#fef3c7;


    box-shadow:


    inset 0 5px 15px rgba(0,0,0,.08);


}





.supplier-profile-header h1{


    margin-top:20px;


    font-size:30px;


    font-weight:900;


    color:#111827;


}





.supplier-profile-header p{


    color:#6b7280;


}








/* INFO */


.supplier-info-list{


    display:flex;


    flex-direction:column;


    gap:15px;


}



.supplier-info-item,
.supplier-address{


    background:#f9fafb;


    padding:18px;


    border-radius:18px;


    display:flex;


    justify-content:space-between;


    align-items:center;


    transition:.3s;


}



.supplier-info-item:hover,
.supplier-address:hover{


    background:#fffbeb;


    transform:translateX(5px);


}





.info-label{


    color:#6b7280;


    font-weight:700;


    display:flex;


    gap:10px;


    align-items:center;


}



.info-label i{


    color:#f59e0b;


}








.supplier-product-tag{


    background:#fef3c7;


    color:#b45309;


    padding:8px 15px;


    border-radius:30px;


    font-weight:800;


}









/* BUTTONS */


.supplier-show-actions{


    margin-top:35px;


    display:flex;


    justify-content:center;


    gap:15px;


}




.show-back-btn,
.show-edit-btn{


    padding:13px 25px;


    border-radius:15px;


    display:flex;


    align-items:center;


    gap:10px;


    text-decoration:none;


    font-weight:800;


    transition:.3s;


}





.show-back-btn{


    background:#6b7280;


    color:white;


}



.show-edit-btn{


    background:


    linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );


    color:white;


}



.show-back-btn:hover,
.show-edit-btn:hover{


    transform:translateY(-4px);


}







@media(max-width:600px){


.supplier-show-page{

    padding:15px;

}



.supplier-profile-card{

    padding:25px;

}



.supplier-info-item{

    flex-direction:column;

    align-items:flex-start;

    gap:10px;

}



.supplier-show-actions{

    flex-direction:column;

}



.show-back-btn,
.show-edit-btn{

    justify-content:center;

}


}
</style>

@endsection