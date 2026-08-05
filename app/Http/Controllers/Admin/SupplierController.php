<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'nom_contact' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'telephone' => 'required|string|max:30',
            'adresse' => 'nullable|string',
            'produits_fournis' => 'nullable|string|max:255',
        ]);

        Supplier::create($request->all());

        return redirect('/admin/suppliers')->with('success', 'Fournisseur enregistré avec succès !');
    }

    public function show(Supplier $supplier)
    {
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'nom_contact' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'telephone' => 'required|string|max:30',
            'adresse' => 'nullable|string',
            'produits_fournis' => 'nullable|string|max:255',
        ]);

        $supplier->update($request->all());

        return redirect('/admin/suppliers')->with('success', 'Informations du fournisseur mises à jour !');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect('/admin/suppliers')->with('success', 'Fournisseur supprimé avec succès !');
    }
}