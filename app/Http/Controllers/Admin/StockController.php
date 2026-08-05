<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::all();
        // Alèt pou pwodwi ki anba oswa egal ak nivo rapèl yo
        $alertes = Product::whereColumn('quantite_actuelle', '<=', 'seuil_alerte')->get();
        return view('admin.stock.index', compact('products', 'alertes'));
    }

    public function create()
    {
        return view('admin.stock.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'unite' => 'required|string|max:50',
            'quantite_actuelle' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|numeric|min:0',
        ]);

        Product::create($request->all());

        return redirect('/admin/stock')->with('success', 'Produit ajouté au stock avec succès !');
    }

    public function edit(Product $stock)
    {
        $product = $stock;
        return view('admin.stock.edit', compact('product'));
    }

    public function update(Request $request, Product $stock)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'unite' => 'required|string|max:50',
            'seuil_alerte' => 'required|numeric|min:0',
        ]);

        $stock->update($request->only(['nom', 'unite', 'seuil_alerte']));

        return redirect('/admin/stock')->with('success', 'Produit mis à jour avec succès !');
    }

    public function destroy(Product $stock)
    {
        $stock->delete();
        return redirect('/admin/stock')->with('success', 'Produit supprimé du stock !');
    }

    // Mouvman Stock (Antre / Sòti)
    public function mouvementForm()
    {
        $products = Product::all();
        return view('admin.stock.mouvement', compact('products'));
    }

    public function mouvementStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrant,sortant',
            'quantite' => 'required|numeric|min:0.01',
            'motif' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->type == 'entrant') {
            $product->quantite_actuelle += $request->quantite;
        } else {
            if ($product->quantite_actuelle < $request->quantite) {
                return back()->withErrors(['quantite' => 'Stock insuffisante pour cette sortie !'])->withInput();
            }
            $product->quantite_actuelle -= $request->quantite;
        }

        $product->save();

        StockMovement::create($request->all());

        return redirect('/admin/stock')->with('success', 'Mouvement de stock enregistré avec succès !');
    }
}