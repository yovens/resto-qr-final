<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\RestaurantTable;

class WaitingController extends Controller
{
    public function index($tableId, $commandeId = null)
    {
        $table = RestaurantTable::findOrFail($tableId);

        // Tout kòmand aktif sou tab sa (ki poko peye oswa anile)
        $commandesActives = Commande::with('items.plat')
            ->where('restaurant_table_id', $tableId)
            ->whereNotIn('statut', ['payee', 'annulee', 'fermee'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($commandeId) {
            $commande = Commande::with('items.plat')
                ->where('id', $commandeId)
                ->where('restaurant_table_id', $tableId)
                ->firstOrFail();
        } else {
            // Dènye kòmand aktif la, oswa null
            $commande = $commandesActives->first();
        }

        // Si pa gen kòmand ditou, voye l nan meni a
        if (!$commande && !$commandeId) {
            return redirect('/menu/' . $tableId)->with('info', 'Ou pa gen kòmand aktif sou tab sa a.');
        }

        return view('client.waiting', [
            'table' => $table,
            'tableId' => $tableId,
            'commande' => $commande,
            'commandesActives' => $commandesActives,
            'hasMultipleOrders' => $commandesActives->count() > 1
        ]);
    }
}