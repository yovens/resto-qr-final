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
     * Montre meni an ak filtrage kategori
     */
  public function menu(Request $request, $tableId)
{
    $table = RestaurantTable::findOrFail($tableId);
    
    // 1. Chèche kòmand aktif
    $activeCommande = Commande::where('restaurant_table_id', $tableId)
        ->where('archived', false)
        ->latest()
        ->first();

    // 2. Chèche TOUT kategori yo ak plat yo ladan yo
    // Pa mete 'where' sou ID kategori a isit la
    $allCategories = Category::with(['plats' => function($q) {
        $q->where('disponible', true);
    }])->get();

    // 3. Si gen yon filtre, nou filtre koleksyon an an memwa (sa pa retire lòt kategori yo)
    $categories = $allCategories;
    if ($request->has('category') && !empty($request->category)) {
        $categories = $allCategories->where('id', $request->category);
    }

    return view('client.menu', [
        'categories' => $categories,    // Sa a ap itilize pou lis plat yo
        'allCategories' => $allCategories, // Sa a se sa w ap itilize pou bouton yo (li pa janm chanje)
        'tableId' => $tableId,
        'table' => $table,
        'commandeId' => $activeCommande ? $activeCommande->id : null
    ]);
}
    /**
     * Ajouter atik nan sesyon panier
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'plat_id' => 'required|exists:plats,id',
            'quantite' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $cart = CartService::add($cart, $request->plat_id, $request->quantite);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'total' => CartService::total($cart)
        ]);
    }

    /**
     * Validation commande
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'note' => 'nullable|string|max:500'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Panier vide'], 400);
        }

        // Kreyasyon kòmand
        $commande = Commande::create([
            'restaurant_table_id' => $request->table_id,
            'total' => CartService::total($cart),
            'statut' => 'nouvelle',
            'note' => $request->note
        ]);

        // Kreyasyon detay kòmand yo
        foreach ($cart as $platId => $item) {
            CommandeItem::create([
                'commande_id' => $commande->id,
                'plat_id' => $platId,
                'quantite' => $item['quantite'],
                'prix' => $item['prix']
            ]);
        }

        session()->forget('cart');

        // Notifye kwizin nan
        broadcast(new NewOrderEvent($commande))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Commande envoyée ✔',
            'commande_id' => $commande->id
        ]);
    }

    /**
 * Détail d'un plat
 */
public function showPlat($tableId, $id)
{
    $table = RestaurantTable::findOrFail($tableId);


    $plat = Plat::with('category')
        ->where('disponible', true)
        ->findOrFail($id);



    // Plat menm kategori
    $relatedPlats = Plat::where('category_id', $plat->category_id)
        ->where('id','!=',$plat->id)
        ->where('disponible',true)
        ->limit(4)
        ->get();



    return view('client.plat-detail', [

        'table'=>$table,

        'tableId'=>$tableId,

        'plat'=>$plat,

        'relatedPlats'=>$relatedPlats

    ]);
}
}