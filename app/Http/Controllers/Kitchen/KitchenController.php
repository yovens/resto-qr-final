<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\OrderAcceptedEvent;
use App\Events\OrderReadyEvent;
use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Statuts visibles sur l'écran cuisine.
     */
    private const STATUTS_CUISINE = ['nouvelle', 'en_preparation', 'prete'];

    /**
     * Écran cuisine : les commandes du plus ancien au plus récent
     * (la cuisine traite dans l'ordre d'arrivée).
     */
    public function index()
    {
        $commandes = Commande::with(['items.plat', 'table'])
            ->whereIn('statut', self::STATUTS_CUISINE)
            ->where('archived', false)
            ->oldest()
            ->get();

        return view('kitchen.index', compact('commandes'));
    }

    /**
     * Mettre à jour le statut d'une commande.
     * Répond en JSON quand l'appel vient de l'écran cuisine (fetch),
     * sinon redirige comme avant.
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'statut' => 'required|in:nouvelle,en_preparation,prete,servie',
        ]);

        $commande = Commande::with(['items.plat', 'table'])->findOrFail($id);

        if ($commande->statut !== $data['statut']) {
            $commande->statut = $data['statut'];
            $commande->save();

            match ($data['statut']) {
                'en_preparation' => broadcast(new OrderAcceptedEvent($commande)),
                'prete'          => broadcast(new OrderReadyEvent($commande)),
                default          => null,
            };
        }

        if ($request->expectsJson()) {
            return response()->json([
                'id'     => $commande->id,
                'statut' => $commande->statut,
            ]);
        }

        return back()->with('success', 'Statut de la commande mis à jour.');
    }
}