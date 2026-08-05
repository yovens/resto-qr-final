@extends('admin.layouts.app')

@section('content')

<div class="stock-form-page">


    <!-- CARD FORM -->

    <div class="stock-form-card">


        <!-- HEADER -->

        <div class="stock-form-header">

            <div class="stock-form-icon">

                <i class="fa-solid fa-boxes-stacked"></i>

            </div>


            <div>

                <h1>
                    Ajouter un article au stock
                </h1>

                <p>
                    Ajouter un nouveau produit ou ingrédient
                </p>

            </div>

        </div>





        <!-- ERRORS -->

        @if($errors->any())


        <div class="stock-error">


            <strong>
                Erreurs détectées :
            </strong>


            <ul>


                @foreach($errors->all() as $error)


                    <li>
                        {{ $error }}
                    </li>


                @endforeach


            </ul>


        </div>


        @endif





        <!-- FORM -->

        <form action="/admin/stock"
              method="POST"
              class="stock-form">


            @csrf





            <!-- NOM PRODUIT -->

            <div class="form-group">


                <label>
                    Nom du Produit / Ingrédient
                </label>


                <input type="text"
                       name="nom"
                       value="{{ old('nom') }}"
                       placeholder="Ex: Riz, Poulet, Huile..."
                       required>


            </div>






            <!-- UNITE -->

            <div class="form-group">


                <label>
                    Unité
                    <span>
                        (kg, litre, unité...)
                    </span>
                </label>


                <input type="text"
                       name="unite"
                       value="{{ old('unite') }}"
                       placeholder="Ex: kg"
                       required>


            </div>







            <!-- QUANTITES -->

            <div class="form-row">



                <div class="form-group">


                    <label>
                        Quantité Initiale
                    </label>


                    <input type="number"
                           step="0.01"
                           name="quantite_actuelle"
                           value="{{ old('quantite_actuelle',0) }}"
                           required>


                </div>





                <div class="form-group">


                    <label>
                        Seuil d'Alerte
                    </label>


                    <input type="number"
                           step="0.01"
                           name="seuil_alerte"
                           value="{{ old('seuil_alerte',5) }}"
                           required>


                </div>



            </div>








            <!-- BUTTONS -->


            <div class="stock-form-footer">


                <a href="/admin/stock"
                   class="btn-cancel">

                    <i class="fa-solid fa-arrow-left"></i>

                    Annuler

                </a>





                <button type="submit"
                        class="btn-save">


                    <i class="fa-solid fa-check"></i>

                    Enregistrer


                </button>



            </div>




        </form>



    </div>



</div>

<style>
    /*================================
    STOCK CREATE FORM
================================*/


.stock-form-page{

    min-height:80vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:30px;

}



.stock-form-card{

    width:100%;

    max-width:650px;

    background:white;

    border-radius:22px;

    padding:35px;

    box-shadow:
    0 15px 40px rgba(0,0,0,.08);

    border:1px solid #e5e7eb;

}





.stock-form-header{

    display:flex;

    align-items:center;

    gap:15px;

    margin-bottom:30px;

}



.stock-form-icon{

    width:55px;

    height:55px;

    border-radius:15px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );

    color:white;

    font-size:25px;

}



.stock-form-header h1{

    margin:0;

    font-size:25px;

    font-weight:800;

    color:#1f2937;

}



.stock-form-header p{

    margin-top:5px;

    color:#6b7280;

}





/* ERROR */


.stock-error{

    background:#fee2e2;

    color:#b91c1c;

    border-left:5px solid #dc2626;

    padding:15px;

    border-radius:12px;

    margin-bottom:20px;

}




.stock-error ul{

    margin-top:8px;

}





/* FORM */


.stock-form{

    display:flex;

    flex-direction:column;

    gap:20px;

}



.form-group{

    display:flex;

    flex-direction:column;

    gap:8px;

}



.form-group label{

    font-weight:700;

    color:#374151;

    font-size:14px;

}



.form-group label span{

    color:#9ca3af;

    font-weight:500;

}



.form-group input{

    width:100%;

    padding:13px 15px;

    border:1px solid #d1d5db;

    border-radius:12px;

    outline:none;

    transition:.3s;

    font-size:15px;

}



.form-group input:focus{

    border-color:#f59e0b;

    box-shadow:
    0 0 0 3px rgba(245,158,11,.15);

}





.form-row{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

}







/* FOOTER BUTTONS */


.stock-form-footer{

    display:flex;

    justify-content:flex-end;

    gap:15px;

    padding-top:20px;

    border-top:1px solid #e5e7eb;

}




.btn-cancel,
.btn-save{

    padding:12px 22px;

    border-radius:12px;

    font-weight:700;

    text-decoration:none;

    display:flex;

    align-items:center;

    gap:8px;

    transition:.3s;

}




.btn-cancel{

    background:#e5e7eb;

    color:#374151;

}



.btn-cancel:hover{

    background:#d1d5db;

}





.btn-save{

    border:none;

    cursor:pointer;

    background:linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );

    color:white;

    box-shadow:
    0 8px 20px rgba(245,158,11,.25);

}



.btn-save:hover{

    transform:translateY(-3px);

}







@media(max-width:600px){


.stock-form-page{

    padding:15px;

}


.stock-form-card{

    padding:20px;

}


.form-row{

    grid-template-columns:1fr;

}


.stock-form-footer{

    flex-direction:column;

}


.btn-cancel,
.btn-save{

    justify-content:center;

}


}
</style>
@endsection