<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commande;

use App\Events\OrderAcceptedEvent;
use App\Events\OrderReadyEvent;

class KitchenController extends Controller
{
    public function index()
    {
      $commandes = Commande::with('items.plat','table')
    ->whereIn('statut', [
        'nouvelle',
        'en_preparation'
    ])
    ->latest()
    ->get();

        return view('kitchen.index', compact('commandes'));
    }

public function updateStatus(Request $request, $id)
{
    $commande = Commande::findOrFail($id);

    $commande->statut = $request->statut;

    if ($request->statut == 'en_preparation') {

        broadcast(new OrderAcceptedEvent($commande));

    }

    if ($request->statut == 'prete') {

        broadcast(new OrderReadyEvent($commande));

    }

    $commande->save();

    return back()->with('success','Statut mis à jour');
}
}