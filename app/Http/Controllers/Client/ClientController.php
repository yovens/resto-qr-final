<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\RestaurantTable;
use App\Models\Plat;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Services\CartService;
use App\Events\NewOrderEvent;

class ClientController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * Meni an — ak deteksyon kòmand aktif
     * --------------------------------------------------------------------------
     */
    public function menu(Request $request, $tableId)
    {
        $table = RestaurantTable::findOrFail($tableId);

        // Dènye kòmand aktif sou tab sa (pa archivée, pa fini/paye)
        $activeCommande = $this->getActiveCommande($tableId);

        // Tout kategori ak plato disponib
        $allCategories = Category::with(['plats' => function ($q) {
            $q->where('disponible', true);
        }])->get();

        // Filtre si gen paramèt ?category=
        $categories = $allCategories;
        if ($request->has('category') && !empty($request->category)) {
            $categories = $allCategories->where('id', $request->category);
        }

        return view('client.menu', [
            'categories'    => $categories,
            'allCategories' => $allCategories,
            'tableId'       => $tableId,
            'table'         => $table,
            'activeCommande'=> $activeCommande,
            'commandeId'    => $activeCommande ? $activeCommande->id : null
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Ajoute nan panier (session)
     * --------------------------------------------------------------------------
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'plat_id'  => 'required|exists:plats,id',
            'quantite' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $cart = CartService::add($cart, $request->plat_id, $request->quantite);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'total'   => CartService::total($cart),
            'count'   => array_sum(array_column($cart, 'quantite'))
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Checkout — Kreye NOUVO kòmand
     * Si gen yon lòt ki poko pare, li rete la. Client ka gen plizyè kòmand.
     * --------------------------------------------------------------------------
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'note'     => 'nullable|string|max:500'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Panier ou vid'
            ], 400);
        }

        // Kreye nouvo kòmand (nou pa fusione ak ansyen an)
        $commande = Commande::create([
            'restaurant_table_id' => $request->table_id,
            'total'               => CartService::total($cart),
            'statut'              => 'nouvelle',
            'note'                => $request->note,
            'archived'            => false
        ]);

        foreach ($cart as $platId => $item) {
            CommandeItem::create([
                'commande_id' => $commande->id,
                'plat_id'     => $platId,
                'quantite'    => $item['quantite'],
                'prix'        => $item['prix']
            ]);
        }

        $commande->load(['table', 'items.plat']);

        session()->forget('cart');

        broadcast(new NewOrderEvent($commande))->toOthers();

        return response()->json([
            'success'     => true,
            'message'     => 'Kòmand lan voye ✔',
            'commande_id' => $commande->id,
            'total'       => $commande->total
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Ajoute plato nan yon kòmand ki deja egziste (pa poko pare)
     * Itilize sa lè kliyan an gen yon kòmand aktif epi li vle ajoute lòt bagay
     * --------------------------------------------------------------------------
     */
    public function addToExistingOrder(Request $request, $tableId, $commandeId)
    {
        $request->validate([
            'plat_id'  => 'required|exists:plats,id',
            'quantite' => 'required|integer|min:1'
        ]);

        $commande = Commande::where('id', $commandeId)
            ->where('restaurant_table_id', $tableId)
            ->whereNotIn('statut', ['payee', 'annulee', 'fermee', 'archivee'])
            ->firstOrFail();

        $plat = Plat::findOrFail($request->plat_id);

        // Si plat la deja nan kòmand lan, nou ajoute kantite a
        $item = CommandeItem::where('commande_id', $commandeId)
            ->where('plat_id', $plat->id)
            ->first();

        if ($item) {
            $item->quantite += $request->quantite;
            $item->save();
        } else {
            CommandeItem::create([
                'commande_id' => $commandeId,
                'plat_id'     => $plat->id,
                'quantite'    => $request->quantite,
                'prix'        => $plat->prix_promo ?? $plat->prix
            ]);
        }

        // Recalcul total
        $commande->load('items');
        $commande->total = $commande->items->sum(function ($i) {
            return $i->prix * $i->quantite;
        });
        $commande->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Plat ajoute nan kòmand #' . $commande->id,
            'commande_id' => $commande->id,
            'total'       => $commande->total
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Detay plat + plato ki sanble
     * --------------------------------------------------------------------------
     */
    public function showPlat($tableId, $id)
    {
        $table = RestaurantTable::findOrFail($tableId);

        $plat = Plat::with('category')
            ->where('disponible', true)
            ->findOrFail($id);

        $relatedPlats = Plat::where('category_id', $plat->category_id)
            ->where('id', '!=', $plat->id)
            ->where('disponible', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('client.plat-detail', [
            'table'        => $table,
            'tableId'      => $tableId,
            'plat'         => $plat,
            'relatedPlats' => $relatedPlats
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Waiting — redireksyon si pa gen commandeId
     * --------------------------------------------------------------------------
     */
    public function waiting($tableId)
    {
        $commande = $this->getActiveCommande($tableId);

        if (!$commande) {
            return redirect('/menu/' . $tableId)
                ->with('info', 'Ou pa gen kòmand aktif. Kòmanse kòmande!');
        }

        return redirect('/waiting/' . $tableId . '/' . $commande->id);
    }

    /**
     * --------------------------------------------------------------------------
     * Helper prive — dènye kòmand aktif sou yon tab
     * --------------------------------------------------------------------------
     */
    private function getActiveCommande($tableId)
    {
        return Commande::where('restaurant_table_id', $tableId)
            ->where('archived', false)
            ->whereNotIn('statut', ['payee', 'annulee', 'fermee'])
            ->latest()
            ->first();
    }
}