<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Commande;
use App\Models\Paiement;

class PaiementController extends Controller
{
    /**
     * ============================================================
     * LISTE DES PAIEMENTS
     * ============================================================
     */
    public function index()
    {
        $paiements = Paiement::with([
                'commande'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view(
            'caisse.paiements',
            compact('paiements')
        );
    }


    /**
     * ============================================================
     * PAGE D'ENCAISSEMENT
     * ============================================================
     */
    public function create($commandeId)
    {
        /*
        |--------------------------------------------------------------------------
        | Récupérer uniquement la commande
        | qui est prête à être encaissée
        |--------------------------------------------------------------------------
        */

        $commande = Commande::with([
                'items.plat',
                'table'
            ])
            ->where('id', $commandeId)
            ->where('statut', 'prete')
            ->firstOrFail();


        return view(
            'caisse.encaisser',
            compact('commande')
        );
    }


    /**
     * ============================================================
     * ENREGISTRER LE PAIEMENT
     * ============================================================
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'commande_id' => [
                'required',
                'integer',
                'exists:commandes,id'
            ],

            'mode_paiement' => [
                'required',
                'in:Espèces,Carte,MonCash,NatCash,Virement'
            ],

            'montant' => [
                'required',
                'numeric',
                'min:0.01'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Transaction DB
        |--------------------------------------------------------------------------
        */

        $paiement = DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Récupérer la commande
            |--------------------------------------------------------------------------
            */

            $commande = Commande::lockForUpdate()
                ->findOrFail(
                    $validated['commande_id']
                );


            /*
            |--------------------------------------------------------------------------
            | Vérifier que la commande est prête
            |--------------------------------------------------------------------------
            */

            if ($commande->statut !== 'prete') {

                abort(
                    422,
                    'Cette commande n’est plus disponible pour encaissement.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Vérifier le montant
            |--------------------------------------------------------------------------
            */

            if (
                (float) $validated['montant']
                !=
                (float) $commande->total
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'montant' =>
                            'Le montant du paiement doit être égal au total de la commande.'
                    ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Générer numéro facture
            |--------------------------------------------------------------------------
            */

            $numeroFacture =
                'FAC-'
                . now()->format('Ymd')
                . '-'
                . str_pad(
                    (Paiement::count() + 1),
                    5,
                    '0',
                    STR_PAD_LEFT
                );


            /*
            |--------------------------------------------------------------------------
            | Créer paiement
            |--------------------------------------------------------------------------
            */

            $paiement = Paiement::create([

                'commande_id' =>
                    $commande->id,

                'montant' =>
                    $validated['montant'],

                'mode_paiement' =>
                    $validated['mode_paiement'],

                'caissier' =>
                    Auth::user()->name ?? 'Admin',

                'numero_facture' =>
                    $numeroFacture,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Changer statut commande
            |--------------------------------------------------------------------------
            */

            $commande->update([

                'statut' => 'payee'

            ]);


            return $paiement;
        });


        /*
        |--------------------------------------------------------------------------
        | Redirection vers facture
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'caisse.facture',
                $paiement->id
            )
            ->with(
                'success',
                'Paiement enregistré avec succès.'
            );
    }


    /**
     * ============================================================
     * AFFICHER LA FACTURE
     * ============================================================
     */
    public function facture($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Charger paiement + commande + articles
        |--------------------------------------------------------------------------
        */

        $paiement = Paiement::with([

            'commande',

            'commande.items',

            'commande.items.plat',

            'commande.table'

        ])
        ->findOrFail($id);


        return view(
            'caisse.facture',
            compact('paiement')
        );
    }
}