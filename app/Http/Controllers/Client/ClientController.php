<?php

namespace App\Http\Controllers\Client;

use App\Events\NewOrderEvent;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Plat;
use App\Models\RestaurantTable;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Taux de service ajouté au total (10 %).
     * Le même taux est affiché dans le panier du client.
     */
    public const TAUX_SERVICE = 0.10;

    /**
     * Statuts qui terminent le suivi d'une commande côté client.
     */
    private const STATUTS_TERMINES = ['payee', 'annulee', 'fermee'];

    /**
     * Menu de la table + commande en cours éventuelle.
     */
    public function menu(Request $request, $tableId)
    {
        $table = RestaurantTable::findOrFail($tableId);

        $allCategories = Category::with(['plats' => fn ($q) => $q->where('disponible', true)->orderBy('nom')])->get();

        return view('client.menu', [
            'categories'     => $allCategories,
            'allCategories'  => $allCategories,
            'tableId'        => $tableId,
            'table'          => $table,
            'activeCommande' => $this->getActiveCommande($tableId),
            'serviceRate'    => self::TAUX_SERVICE,
        ]);
    }

    /**
     * Ancienne route du panier en session.
     * N'est plus utilisée par l'interface (le panier est envoyé complet au checkout),
     * conservée pour compatibilité.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'plat_id'  => 'required|exists:plats,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $cart = CartService::add(session()->get('cart', []), $request->plat_id, $request->quantite);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'total'   => CartService::total($cart),
            'count'   => array_sum(array_column($cart, 'quantite')),
        ]);
    }

    /**
     * Création de la commande.
     *
     * Le client envoie la liste complète des plats et des quantités.
     * Les prix sont TOUJOURS relus en base (jamais ceux du navigateur).
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'table_id'           => 'required|exists:restaurant_tables,id',
            'note'               => 'nullable|string|max:500',
            'items'              => 'required|array|min:1|max:50',
            'items.*.plat_id'    => 'required|integer',
            'items.*.quantite'   => 'required|integer|min:1|max:50',
        ], [
            'items.required' => 'Panye a vid.',
            'items.min'      => 'Panye a vid.',
        ]);

        // Regroupe les doublons éventuels
        $quantites = [];
        foreach ($data['items'] as $it) {
            $quantites[$it['plat_id']] = ($quantites[$it['plat_id']] ?? 0) + $it['quantite'];
        }

        $plats = Plat::whereIn('id', array_keys($quantites))->where('disponible', true)->get()->keyBy('id');

        $manquants = array_diff(array_keys($quantites), $plats->keys()->all());
        if ($manquants) {
            $noms = Plat::whereIn('id', $manquants)->pluck('nom')->implode(', ');
            return response()->json([
                'success'   => false,
                'message'   => ($noms ?: 'Yon plat') . ' pa disponib ankò. Retire l nan panye a.',
                'manquants' => array_values($manquants),
            ], 422);
        }

        $commande = DB::transaction(function () use ($data, $quantites, $plats) {
            $sousTotal = 0;
            foreach ($quantites as $platId => $q) {
                $sousTotal += $this->prix($plats[$platId]) * $q;
            }
            $service = round($sousTotal * self::TAUX_SERVICE);

            $commande = Commande::create([
                'restaurant_table_id' => $data['table_id'],
                'total'               => $sousTotal + $service,
                'statut'              => 'nouvelle',
                'note'                => $data['note'] ?? null,
                'archived'            => false,
            ]);

            foreach ($quantites as $platId => $q) {
                CommandeItem::create([
                    'commande_id' => $commande->id,
                    'plat_id'     => $platId,
                    'quantite'    => $q,
                    'prix'        => $this->prix($plats[$platId]),
                ]);
            }

            return $commande;
        });

        $commande->load(['table', 'items.plat']);
        session()->forget('cart');

        // Si le serveur temps réel (Reverb) est arrêté, la commande reste valide :
        // on ne fait pas échouer la requête (sinon le client renverrait la commande).
        try {
            broadcast(new NewOrderEvent($commande))->toOthers();
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Kòmand lan voye',
            'commande_id' => $commande->id,
            'total'       => $commande->total,
        ]);
    }

    /**
     * Statut d'une commande (interrogé toutes les quelques secondes
     * par la page de suivi — fonctionne sans internet).
     */
    public function statut($tableId, $commandeId)
    {
        $commande = Commande::where('id', $commandeId)
            ->where('restaurant_table_id', $tableId)
            ->firstOrFail(['id', 'statut', 'archived', 'updated_at']);

        return response()->json([
            'id'      => $commande->id,
            'statut'  => $commande->statut,
            'termine' => in_array($commande->statut, self::STATUTS_TERMINES, true) || (bool) $commande->archived,
        ])->header('Cache-Control', 'no-store');
    }

    /**
     * Ajout de plats à une commande existante (pas encore payée).
     */
    public function addToExistingOrder(Request $request, $tableId, $commandeId)
    {
        $request->validate([
            'plat_id'  => 'required|exists:plats,id',
            'quantite' => 'required|integer|min:1|max:50',
        ]);

        $commande = Commande::where('id', $commandeId)
            ->where('restaurant_table_id', $tableId)
            ->whereNotIn('statut', ['payee', 'annulee', 'fermee', 'archivee'])
            ->firstOrFail();

        $plat = Plat::where('disponible', true)->findOrFail($request->plat_id);

        DB::transaction(function () use ($commande, $plat, $request) {
            $item = CommandeItem::where('commande_id', $commande->id)->where('plat_id', $plat->id)->first();

            if ($item) {
                $item->increment('quantite', $request->quantite);
            } else {
                CommandeItem::create([
                    'commande_id' => $commande->id,
                    'plat_id'     => $plat->id,
                    'quantite'    => $request->quantite,
                    'prix'        => $this->prix($plat),
                ]);
            }

            $sousTotal = $commande->items()->get()->sum(fn ($i) => $i->prix * $i->quantite);
            $commande->total = $sousTotal + round($sousTotal * self::TAUX_SERVICE);
            $commande->save();
        });

        return response()->json([
            'success'     => true,
            'message'     => 'Plat ajoute nan kòmand #' . $commande->id,
            'commande_id' => $commande->id,
            'total'       => $commande->total,
        ]);
    }

    /**
     * Fiche d'un plat.
     */
    public function showPlat($tableId, $id)
    {
        $table = RestaurantTable::findOrFail($tableId);

        $plat = Plat::with('category')->where('disponible', true)->findOrFail($id);

        $relatedPlats = Plat::with('category')
            ->where('category_id', $plat->category_id)
            ->where('id', '!=', $plat->id)
            ->where('disponible', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('client.plat-detail', [
            'table'        => $table,
            'tableId'      => $tableId,
            'plat'         => $plat,
            'relatedPlats' => $relatedPlats,
            'serviceRate'  => self::TAUX_SERVICE,
        ]);
    }

    /**
     * /waiting/{table} sans numéro de commande : redirige vers la dernière commande active.
     */
    public function waiting($tableId)
    {
        $commande = $this->getActiveCommande($tableId);

        if (!$commande) {
            return redirect('/menu/' . $tableId);
        }

        return redirect('/waiting/' . $tableId . '/' . $commande->id);
    }

    /**
     * Prix effectif d'un plat (promotion si présente).
     */
    private function prix(Plat $plat): float
    {
        return (float) ($plat->prix_promo ?: $plat->prix);
    }

    /**
     * Dernière commande active d'une table.
     */
    private function getActiveCommande($tableId)
    {
        return Commande::where('restaurant_table_id', $tableId)
            ->where('archived', false)
            ->whereNotIn('statut', self::STATUTS_TERMINES)
            ->latest()
            ->first();
    }
}