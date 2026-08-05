@extends('admin.layouts.app')

@section('content')

<div class="stock-form-page">


    <div class="stock-form-card supplier-form-card">



        <!-- HEADER -->

        <div class="stock-form-header">


            <div class="stock-form-icon supplier-icon">

                <i class="fa-solid fa-pen-to-square"></i>

            </div>



            <div>

                <h1>
                    Modifier le Fournisseur
                </h1>


                <p>
                    Mettre à jour les informations du partenaire
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


        <form action="/admin/suppliers/{{ $supplier->id }}"
              method="POST"
              class="stock-form">


            @csrf

            @method('PUT')







            <!-- ENTREPRISE -->


            <div class="form-group">


                <label>
                    Nom de l'Entreprise
                </label>


                <input type="text"
                       name="nom_entreprise"
                       value="{{ old('nom_entreprise',$supplier->nom_entreprise) }}"
                       required>


            </div>








            <!-- CONTACT + PHONE -->


            <div class="form-row">


                <div class="form-group">


                    <label>
                        Nom du Contact
                    </label>


                    <input type="text"
                           name="nom_contact"
                           value="{{ old('nom_contact',$supplier->nom_contact) }}"
                           required>


                </div>






                <div class="form-group">


                    <label>
                        Téléphone
                    </label>


                    <input type="text"
                           name="telephone"
                           value="{{ old('telephone',$supplier->telephone) }}"
                           required>


                </div>



            </div>









            <!-- EMAIL + PRODUCTS -->


            <div class="form-row">


                <div class="form-group">


                    <label>
                        Email
                    </label>


                    <input type="email"
                           name="email"
                           value="{{ old('email',$supplier->email) }}"
                           required>


                </div>






                <div class="form-group">


                    <label>
                        Produits Fournis
                    </label>


                    <input type="text"
                           name="produits_fournis"
                           value="{{ old('produits_fournis',$supplier->produits_fournis) }}">


                </div>



            </div>









            <!-- ADRESSE -->


            <div class="form-group">


                <label>
                    Adresse
                </label>


                <textarea name="adresse"
                          rows="3">{{ old('adresse',$supplier->adresse) }}</textarea>


            </div>









            <!-- BUTTONS -->


            <div class="stock-form-footer">



                <a href="/admin/suppliers"
                   class="btn-cancel">


                    <i class="fa-solid fa-arrow-left"></i>

                    Annuler


                </a>






                <button type="submit"
                        class="btn-save supplier-save">


                    <i class="fa-solid fa-floppy-disk"></i>

                    Mettre à jour


                </button>



            </div>





        </form>




    </div>


</div>

<style>
    /*=====================================
    SUPPLIER CREATE PREMIUM STYLE
=====================================*/


.stock-form-page{

    min-height:90vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:40px;

    background:
    linear-gradient(
        135deg,
        #fff7ed,
        #f8fafc
    );

}



/* CARD */

.supplier-form-card{

    width:100%;

    max-width:850px;

    background:white;

    border-radius:28px;

    padding:40px;

    position:relative;

    overflow:hidden;

    box-shadow:

    0 25px 60px rgba(0,0,0,.12);

    animation:formShow .5s ease;

}



.supplier-form-card::before{

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
        #fbbf24,
        #d97706
    );

}



@keyframes formShow{

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

    padding-bottom:25px;

    border-bottom:1px solid #f1f5f9;

}



.stock-form-header h1{

    margin:0;

    font-size:30px;

    font-weight:900;

    color:#111827;

}



.stock-form-header p{

    margin-top:8px;

    color:#6b7280;

}





/* ICON */


.supplier-icon{

    width:85px;

    height:85px;

    border-radius:25px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:38px;

    color:white;

    background:

    linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );


    box-shadow:

    0 15px 35px rgba(245,158,11,.35);

    animation:floatIcon 3s infinite;

}



@keyframes floatIcon{

    50%{

        transform:translateY(-8px);

    }

}






/* FORM */


.stock-form{

    display:flex;

    flex-direction:column;

    gap:20px;

}



.form-row{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

}



.form-group{

    display:flex;

    flex-direction:column;

    gap:8px;

}



.form-group label{

    font-weight:800;

    color:#374151;

    font-size:14px;

}



.form-group input,
.form-group textarea{


    width:100%;

    padding:15px 18px;

    border-radius:15px;

    border:2px solid #e5e7eb;

    background:#fafafa;

    font-size:15px;

    transition:.3s;

}



.form-group textarea{

    resize:none;

}



.form-group input:hover,
.form-group textarea:hover{

    border-color:#fbbf24;

    background:white;

}



.form-group input:focus,
.form-group textarea:focus{


    outline:none;

    background:white;

    border-color:#f59e0b;

    box-shadow:

    0 0 0 5px rgba(245,158,11,.15);

    transform:translateY(-2px);

}







/* LABEL EFFECT */


.form-group:focus-within label{

    color:#d97706;

}







/* ERROR */

.stock-error{

    background:#fee2e2;

    border-left:5px solid #dc2626;

    padding:15px;

    border-radius:15px;

    color:#991b1b;

    margin-bottom:20px;

    animation:shake .4s;

}



.stock-error ul{

    margin-top:10px;

}



@keyframes shake{

    25%{

        transform:translateX(-8px);

    }

    50%{

        transform:translateX(8px);

    }

}







/* FOOTER BUTTON */

.stock-form-footer{


    margin-top:20px;

    padding-top:25px;

    border-top:1px solid #f1f5f9;

    display:flex;

    justify-content:flex-end;

    gap:15px;

}





/* CANCEL */


.btn-cancel{


    padding:13px 25px;

    border-radius:15px;

    background:#e5e7eb;

    color:#374151;

    font-weight:800;

    text-decoration:none;

    display:flex;

    align-items:center;

    gap:8px;

    transition:.3s;

}



.btn-cancel:hover{

    background:#d1d5db;

    transform:translateY(-3px);

}







/* SAVE */


.supplier-save{


    padding:13px 30px;

    border-radius:15px;

    border:none;

    color:white;

    font-weight:900;

    cursor:pointer;

    display:flex;

    align-items:center;

    gap:10px;


    background:

    linear-gradient(
        135deg,
        #f59e0b,
        #d97706
    );


    box-shadow:

    0 12px 25px rgba(245,158,11,.35);

    transition:.3s;

}



.supplier-save:hover{


    transform:translateY(-4px);


    box-shadow:

    0 18px 35px rgba(245,158,11,.45);


}







/* RESPONSIVE */


@media(max-width:700px){


.stock-form-page{

    padding:15px;

}



.supplier-form-card{

    padding:25px;

}



.stock-form-header{

    flex-direction:column;

    text-align:center;

}



.form-row{

    grid-template-columns:1fr;

}



.stock-form-footer{

    flex-direction:column;

}



.btn-cancel,
.supplier-save{

    justify-content:center;

    width:100%;

}


}
</style>
@endsection