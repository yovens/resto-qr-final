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

        if ($commandeId) {
            $commande = Commande::where('id', $commandeId)->where('restaurant_table_id', $tableId)->firstOrFail();
        } else {
            $commande = Commande::with('items.plat')
    ->where('restaurant_table_id', $tableId)
    ->latest()
    ->first();
        }

        return view('client.waiting', [
            'table' => $table,
            'tableId' => $tableId,
            'commande' => $commande
        ]);
    }
}