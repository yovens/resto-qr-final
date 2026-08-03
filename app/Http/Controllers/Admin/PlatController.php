<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Category;
use Illuminate\Http\Request;

class PlatController extends Controller
{
    public function index()
    {
        $plats = Plat::with('category')->get();
        return view('admin.plats.index', compact('plats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.plats.create', compact('categories'));
    }




public function store(Request $request)
{
    // 1. Validation de base
    $data = $request->validate([
        'nom' => 'required',
        'prix' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable',
        'image' => 'nullable|image' // On autorise l'image ici
    ]);

    // 2. Gestion de la disponibilité
    $data['disponible'] = $request->has('disponible') ? 1 : 0;

    // 3. RETIRER l'image du tableau $data pour éviter qu'il ne contienne l'objet fichier
    unset($data['image']);

    // 4. Gestion manuelle de l'upload
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $filename);
        
        // On ajoute le nom propre au tableau $data
        $data['image'] = $filename;
    } else {
        $data['image'] = null;
    }

    Plat::create($data);

    return redirect()->route('admin.plats.index')->with('success', 'Plat ajouté avec succès.');
}

public function update(Request $request, $id)
{
    $plat = Plat::findOrFail($id);

    $data = $request->validate([
        'nom' => 'required',
        'prix' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable',
        'image' => 'nullable|image'
    ]);

    $data['disponible'] = $request->has('disponible') ? 1 : 0;

    if ($request->hasFile('image')) {
        // Supprimer l'ancienne image si elle existe
        if ($plat->image && file_exists(public_path('images/'.$plat->image))) {
            unlink(public_path('images/'.$plat->image));
        }

        $file = $request->file('image');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $filename);
        $data['image'] = $filename;
    } else {
        // Si aucune nouvelle image, on garde l'ancienne
        $data['image'] = $plat->image; 
    }

    $plat->update($data);

    return back()->with('success', 'Plat mis à jour.');
}

  public function destroy($id)
{
    $plat = Plat::findOrFail($id);

    if ($plat->image && file_exists(public_path('images/'.$plat->image))) {
        unlink(public_path('images/'.$plat->image));
    }

    $plat->delete();

    return back()->with('success', 'Plat supprimé.');
}
}