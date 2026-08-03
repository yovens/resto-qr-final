<?php

namespace App\Http\Controllers\Plat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plat;
use App\Models\Category;

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
        $data = $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'image' => 'nullable|image'
        ]);

        // checkbox propre
        $data['disponible'] = $request->has('disponible') ? 1 : 0;

        // upload image
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $filename = uniqid().'_'.time().'.'.$file->getClientOriginalExtension();

            $file->move(public_path('images'), $filename);

            $data['image'] = $filename;
        }

        Plat::create($data);

        return redirect('/admin/plats')->with('success', 'Plat ajouté');
    }

    public function edit($id)
    {
        $plat = Plat::findOrFail($id);
        $categories = Category::all();

        return view('admin.plats.edit', compact('plat', 'categories'));
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

        // update image
        if ($request->hasFile('image')) {

            if ($plat->image && file_exists(public_path('images/'.$plat->image))) {
                unlink(public_path('images/'.$plat->image));
            }

            $file = $request->file('image');
            $filename = uniqid().'_'.time().'.'.$file->getClientOriginalExtension();

            $file->move(public_path('images'), $filename);

            $data['image'] = $filename;
        }

        $plat->update($data);

        return redirect('/admin/plats')->with('success', 'Plat modifié');
    }

    public function destroy($id)
    {
        $plat = Plat::findOrFail($id);

        if ($plat->image && file_exists(public_path('images/'.$plat->image))) {
            unlink(public_path('images/'.$plat->image));
        }

        $plat->delete();

        return redirect('/admin/plats')->with('success', 'Plat supprimé');
    }
}