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
     * Liste JSON pour l'actualisation automatique de l'écran cuisine
     * (fonctionne même si le serveur temps réel est arrêté).
     */
    public function data()
    {
        $commandes = Commande::with(['items.plat', 'table'])
            ->whereIn('statut', self::STATUTS_CUISINE)
            ->where('archived', false)
            ->oldest()
            ->get()
            ->map(fn ($c) => [
                'id'      => $c->id,
                'statut'  => $c->statut,
                'table'   => $c->table->numero ?? null,
                'created' => $c->created_at->timestamp,
                'note'    => $c->note,
                'items'   => $c->items->map(fn ($i) => [
                    'q'   => $i->quantite,
                    'nom' => $i->plat->nom ?? 'Plat supprimé',
                ])->values(),
            ])->values();

        return response()->json($commandes)->header('Cache-Control', 'no-store');
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

        // Une commande déjà encaissée ne doit jamais revenir en arrière.
        if (in_array($commande->statut, ['payee', 'annulee'], true)) {
            return $request->expectsJson()
                ? response()->json(['id' => $commande->id, 'statut' => $commande->statut])
                : back();
        }

        if ($commande->statut !== $data['statut']) {
            $commande->statut = $data['statut'];
            $commande->save();

            // Si le serveur temps réel est arrêté, le statut est quand même enregistré.
            try {
                match ($data['statut']) {
                    'en_preparation' => broadcast(new OrderAcceptedEvent($commande)),
                    'prete'          => broadcast(new OrderReadyEvent($commande)),
                    default          => null,
                };
            } catch (\Throwable $e) {
                report($e);
            }
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
