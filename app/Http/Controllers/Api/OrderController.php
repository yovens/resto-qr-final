<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * LISTE DES COMMANDES DU CLIENT
     *
     * GET /api/v1/commandes
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $commandes = Commande::where('client_id', $user->id)
            ->with([
                'items.plat'
            ])
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'commandes' => $commandes,
        ]);
    }

    /**
     * CREATE ORDER
     *
     * POST /api/v1/commandes
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_table_id' => [
                'nullable',
                'integer',
                'exists:restaurant_tables,id',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.plat_id' => [
                'required',
                'integer',
                'exists:plats,id',
            ],

            'items.*.quantite' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Vérification de la table
        |--------------------------------------------------------------------------
        */

        if (empty($validated['restaurant_table_id'])) {
            return response()->json([
                'message' => 'Une table est nécessaire pour cette commande.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Calcul du total côté serveur
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;

        $itemsToInsert = [];

        foreach ($validated['items'] as $item) {

            $plat = Plat::where('id', $item['plat_id'])
                ->where('disponible', true)
                ->first();

            if (!$plat) {
                return response()->json([
                    'message' => "Le plat #{$item['plat_id']} n'est plus disponible.",
                ], 422);
            }

            $prix = $plat->prix_promo ?? $plat->prix;

            $quantite = (int) $item['quantite'];

            $subtotal += $prix * $quantite;

            $itemsToInsert[] = [
                'plat_id' => $plat->id,
                'quantite' => $quantite,
                'prix' => $prix,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Création de la commande
        |--------------------------------------------------------------------------
        */

        $commande = DB::transaction(function () use (
            $user,
            $validated,
            $subtotal,
            $itemsToInsert
        ) {

            $commandeId = DB::table('commandes')->insertGetId([

                // Client connecté
                'client_id' => $user->id,

                'restaurant_table_id' =>
                    $validated['restaurant_table_id'],

                'total' => $subtotal,

                'statut' => 'nouvelle',

                'status' => 'nouvelle',

                'archived' => 0,

                'note' => $validated['note'] ?? null,

                'created_at' => now(),

                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Ajout des articles
            |--------------------------------------------------------------------------
            */

            foreach ($itemsToInsert as $item) {

                DB::table('commande_items')->insert([

                    'commande_id' => $commandeId,

                    'plat_id' => $item['plat_id'],

                    'quantite' => $item['quantite'],

                    'prix' => $item['prix'],

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Mise à jour du nombre de ventes du plat
                |--------------------------------------------------------------------------
                */

                DB::table('plats')
                    ->where('id', $item['plat_id'])
                    ->increment(
                        'total_vendu',
                        $item['quantite']
                    );
            }

            return Commande::with([
                'items.plat'
            ])->find($commandeId);
        });

        /*
        |--------------------------------------------------------------------------
        | Réponse
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' => 'Commande créée avec succès.',
            'commande' => $commande,
        ], 201);
    }

    /**
     * SHOW ORDER
     *
     * GET /api/v1/commandes/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Sécurité
        |--------------------------------------------------------------------------
        | Le client peut uniquement voir sa propre commande.
        |--------------------------------------------------------------------------
        */

        $commande = Commande::where('client_id', $user->id)
            ->with([
                'items.plat'
            ])
            ->find($id);

        if (!$commande) {
            return response()->json([
                'message' => 'Commande introuvable.',
            ], 404);
        }

        return response()->json([
            'commande' => $commande,
        ]);
    }

    /**
     * CANCEL ORDER
     *
     * POST /api/v1/commandes/{id}/annuler
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Vérification propriétaire
        |--------------------------------------------------------------------------
        */

        $commande = Commande::where('client_id', $user->id)
            ->find($id);

        if (!$commande) {
            return response()->json([
                'message' => 'Commande introuvable.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification du statut
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $commande->statut,
            ['nouvelle', 'new']
        )) {
            return response()->json([
                'message' => 'Cette commande ne peut plus être annulée.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Annulation
        |--------------------------------------------------------------------------
        */

        $commande->statut = 'annulee';

        $commande->status = 'annulee';

        $commande->save();

        return response()->json([
            'message' => 'Commande annulée.',
            'commande' => $commande,
        ]);
    }
}