<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PaiementController extends Controller
{


    /**
     * Liste tout paiements
     */
    public function index()
    {


        $paiements = Paiement::with('commande')
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate(15);



        return view(
            'caisse.paiements',
            compact('paiements')
        );


    }







    /**
     * Page encaissement
     */
    public function create($commandeId)
    {


        $commande = Commande::findOrFail(
            $commandeId
        );



        return view(
            'caisse.encaisser',
            compact('commande')
        );


    }








    /**
     * Enregistrer paiement
     */
    public function store(Request $request)
    {


        $request->validate([


            'commande_id'
            =>'required|exists:commandes,id',


            'mode_paiement'
            =>'required|in:Espèces,Carte,MonCash,NatCash,Virement',


            'montant'
            =>'required|numeric|min:0'


        ]);






        DB::transaction(function() use ($request){



            $commande = Commande::findOrFail(
                $request->commande_id
            );







            Paiement::create([


                'commande_id'=>
                    $commande->id,


                'montant'=>
                    $request->montant,


                'mode_paiement'=>
                    $request->mode_paiement,


                'caissier'=>
                    Auth::user()->name ?? 'Admin',



                'numero_facture'=>

                    'FAC-'
                    .date('Ymd')
                    .'-'
                    .rand(1000,9999)


            ]);








            $commande->update([


                'statut'=>
                    'payee'


            ]);



        });








        return redirect()

            ->route('caisse.dashboard')

            ->with(

                'success',

                'Paiement enregistré avec succès.'

            );


    }








    /**
     * Facture
     */
    public function facture($id)
    {


        $paiement = Paiement::with([

            'commande.items.plat'

        ])

        ->findOrFail($id);





        return view(

            'caisse.facture',

            compact('paiement')

        );


    }



}