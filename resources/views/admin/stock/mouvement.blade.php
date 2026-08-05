@extends('admin.layouts.app')

@section('content')

<div class="stock-form-page">


    <div class="stock-form-card">



        <!-- HEADER -->

        <div class="stock-form-header">


            <div class="stock-form-icon movement-icon">

                <i class="fa-solid fa-right-left"></i>

            </div>


            <div>

                <h1>
                    Mouvement de Stock
                </h1>


                <p>
                    Enregistrer une entrée ou une sortie de produit
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


        <form action="/admin/stock-mouvement"
              method="POST"
              class="stock-form">


            @csrf







            <!-- PRODUIT -->


            <div class="form-group">


                <label>
                    Sélectionner le Produit
                </label>



                <select name="product_id"
                        required>


                    @foreach($products as $prod)


                    <option value="{{ $prod->id }}">


                        {{ $prod->nom }}

                        (Disponible :
                        {{ $prod->quantite_actuelle }}
                        {{ $prod->unite }})


                    </option>


                    @endforeach



                </select>


            </div>








            <!-- TYPE -->


            <div class="form-group">


                <label>
                    Type de Mouvement
                </label>



                <select name="type"
                        required>



                    <option value="entrant">

                        🟢 Entrant
                        (Ajout au stock / Achat)

                    </option>



                    <option value="sortant">

                        🔴 Sortant
                        (Retrait / Utilisation)

                    </option>



                </select>



            </div>








            <!-- QUANTITE -->


            <div class="form-group">


                <label>
                    Quantité
                </label>


                <input type="number"
                       step="0.01"
                       name="quantite"
                       placeholder="Ex: 10"
                       required>


            </div>









            <!-- MOTIF -->


            <div class="form-group">


                <label>

                    Motif / Commentaire

                    <span>
                        (Optionnel)
                    </span>

                </label>



                <input type="text"
                       name="motif"
                       placeholder="Ex: Achat fournisseur / Cuisine">


            </div>









            <!-- BUTTONS -->


            <div class="stock-form-footer">


                <a href="/admin/stock"
                   class="btn-cancel">


                    <i class="fa-solid fa-arrow-left"></i>

                    Annuler


                </a>







                <button type="submit"
                        class="btn-save movement-save">


                    <i class="fa-solid fa-check"></i>

                    Valider le Mouvement


                </button>



            </div>





        </form>




    </div>



</div>
<style>
    /*=====================================
    PREMIUM STOCK MOVEMENT PAGE
=====================================*/


.stock-form-page{

    min-height:85vh;

    padding:40px;

    display:flex;

    justify-content:center;

    align-items:center;

    background:
    linear-gradient(
        135deg,
        #f8fafc,
        #ecfdf5
    );

}



/* CARD */

.stock-form-card{

    width:100%;

    max-width:650px;

    background:rgba(255,255,255,.95);

    backdrop-filter:blur(10px);

    border-radius:28px;

    padding:40px;

    border:1px solid rgba(255,255,255,.6);

    box-shadow:

    0 25px 60px rgba(0,0,0,.12);

    animation:slideUp .5s ease;

}


@keyframes slideUp{

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


.stock-form-header{

    display:flex;

    align-items:center;

    gap:20px;

    margin-bottom:35px;

}


.stock-form-header h1{

    font-size:30px;

    font-weight:900;

    color:#111827;

    letter-spacing:-.5px;

}


.stock-form-header p{

    color:#6b7280;

    font-size:14px;

}






/* ICON */


.stock-form-icon{

    width:70px;

    height:70px;

    border-radius:22px;

    display:flex;

    justify-content:center;

    align-items:center;

    color:white;

    font-size:30px;

    box-shadow:

    0 15px 30px rgba(16,185,129,.35);

}






/* FORM */


.stock-form{

    display:flex;

    flex-direction:column;

    gap:22px;

}



.form-group{

    position:relative;

}



.form-group label{

    display:block;

    margin-bottom:8px;

    color:#374151;

    font-weight:800;

    font-size:14px;

}



.form-group label span{

    color:#9ca3af;

    font-weight:500;

}







/* INPUT SELECT */


.form-group input,
.form-group select{


    width:100%;


    padding:15px 18px;


    border-radius:15px;


    border:2px solid #e5e7eb;


    background:#fff;


    color:#1f2937;


    font-size:15px;


    transition:.35s;


}





.form-group input:hover,
.form-group select:hover{

    border-color:#10b981;

}



.form-group input:focus,
.form-group select:focus{


    outline:none;


    border-color:#10b981;


    box-shadow:

    0 0 0 5px rgba(16,185,129,.15);


}







/* SELECT ARROW */


.form-group select{

    appearance:none;

    background-image:

    linear-gradient(45deg,transparent 50%,#10b981 50%),
    linear-gradient(135deg,#10b981 50%,transparent 50%);

    background-position:

    calc(100% - 20px) 50%,
    calc(100% - 15px) 50%;

    background-size:

    5px 5px,
    5px 5px;

    background-repeat:no-repeat;

}







/* ERROR */


.stock-error{

    background:#fff1f2;

    border-left:6px solid #ef4444;

    color:#b91c1c;

    padding:18px;

    border-radius:15px;

    margin-bottom:20px;

}







/* FOOTER */


.stock-form-footer{

    margin-top:15px;

    display:flex;

    justify-content:flex-end;

    gap:15px;

}






/* BUTTONS */


.btn-cancel,
.btn-save{


    padding:14px 25px;


    border-radius:15px;


    font-weight:800;


    display:flex;


    align-items:center;


    gap:10px;


    transition:.35s;


}






.btn-cancel{

    background:#f3f4f6;

    color:#374151;

}



.btn-cancel:hover{

    background:#e5e7eb;

    transform:translateY(-3px);

}







.movement-save{


    background:

    linear-gradient(
        135deg,
        #10b981,
        #047857
    );


    color:white;


    border:none;


    cursor:pointer;


}




.movement-save:hover{


    transform:

    translateY(-4px);


    box-shadow:

    0 15px 35px rgba(16,185,129,.35);


}





/* SMALL ANIMATION INPUT */


.form-group input:focus::placeholder{

    opacity:.4;

    transform:translateX(5px);

}



.form-group input::placeholder{

    transition:.3s;

}







/* MOBILE */


@media(max-width:650px){


.stock-form-page{

    padding:15px;

}



.stock-form-card{

    padding:25px;

    border-radius:20px;

}



.stock-form-header h1{

    font-size:22px;

}



.stock-form-footer{

    flex-direction:column;

}



.btn-cancel,
.btn-save{

    justify-content:center;

    width:100%;

}


}
</style>

@endsection