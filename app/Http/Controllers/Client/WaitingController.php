<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class WaitingController extends Controller
{
    /**
     * Suivi des commandes.
     *
     * Le client ne voit QUE les commandes passées depuis son téléphone
     * (cookie posé au moment du checkout), et seulement celles d'aujourd'hui.
     * Les commandes des clients précédents à la même table restent invisibles.
     */
    public function index(Request $request, $tableId, $commandeId = null)
    {
        $table = RestaurantTable::findOrFail($tableId);

        // Commandes encore en cours de ce téléphone
        $commandesActives = ClientController::mesCommandes($request, $tableId)
            ->with('items.plat')
            ->latest()
            ->get();

        if ($commandeId) {
            // Une commande précise : elle doit appartenir à ce téléphone
            // (elle peut être déjà servie ou payée, pour afficher « Mèsi »)
            $commande = ClientController::mesCommandes($request, $tableId, false)
                ->with('items.plat')
                ->find($commandeId);
        } else {
            $commande = $commandesActives->first();
        }

        // Rien à suivre sur ce téléphone : retour au menu
        if (!$commande) {
            return redirect('/menu/' . $tableId);
        }

        return view('client.waiting', [
            'table'             => $table,
            'tableId'           => $tableId,
            'commande'          => $commande,
            'commandesActives'  => $commandesActives,
            'hasMultipleOrders' => $commandesActives->count() > 1,
        ]);
    }
}