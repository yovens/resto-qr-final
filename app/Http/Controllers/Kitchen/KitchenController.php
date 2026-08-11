<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Events\OrderAcceptedEvent;
use App\Events\OrderReadyEvent;

class KitchenController extends Controller
{
    /**
     * Afficher les commandes en cuisine
     */
    public function index()
    {
        $commandes = Commande::with([
            'items.plat',
            'table'
        ])
        ->whereIn('statut', [
            'nouvelle',
            'en_preparation'
        ])
        ->latest()
        ->get();

        return view(
            'kitchen.index',
            compact('commandes')
        );
    }


    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'statut' => 'required|in:nouvelle,en_preparation,prete'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Nouveau statut
        |--------------------------------------------------------------------------
        */

        $nouveauStatut = $request->statut;


        /*
        |--------------------------------------------------------------------------
        | Commande acceptée par la cuisine
        |--------------------------------------------------------------------------
        */

        if ($nouveauStatut === 'en_preparation') {

            $commande->statut = 'en_preparation';

            $commande->save();

            broadcast(
                new OrderAcceptedEvent($commande)
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Commande prête
        |--------------------------------------------------------------------------
        |
        | IMPORTANT :
        | Le statut "prete" est celui utilisé par la caisse.
        |
        */

        elseif ($nouveauStatut === 'prete') {

            $commande->statut = 'prete';

            $commande->save();

            broadcast(
                new OrderReadyEvent($commande)
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Autre statut
        |--------------------------------------------------------------------------
        */

        else {

            $commande->statut = $nouveauStatut;

            $commande->save();
        }


        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Statut de la commande mis à jour avec succès.'
        );
    }
}
