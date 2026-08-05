<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    /**
     * Liste des employés
     */
    public function index()
    {
        $employes = Employe::latest()->paginate(10);

        return view('admin.employes.index', compact('employes'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.employes.create');
    }

    /**
     * Enregistrer un employé
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            'email'      => 'required|email|unique:employes,email',
            'telephone'  => 'required|string|max:20',
            'role'       => 'required|in:caissiere,serveur,serveuse,cuisine,autre',
            'salaire'    => 'required|numeric|min:0',
            'photo'      => 'nullable|image|max:4096',
        ]);

        $data = $request->all();

        // Upload photo
        if ($request->hasFile('photo')) {

            $file = $request->file('photo');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(
                public_path('images/employes'),
                $filename
            );

            $data['photo'] = 'images/employes/'.$filename;
        }

        Employe::create($data);

        return redirect()
            ->route('employes.index')
            ->with('success', "L'employé a été ajouté avec succès.");
    }

    /**
     * Afficher un employé
     */
    public function show(Employe $employe)
    {
        return view('admin.employes.show', compact('employe'));
    }

    /**
     * Formulaire modification
     */
    public function edit(Employe $employe)
    {
        return view('admin.employes.edit', compact('employe'));
    }

    /**
     * Mettre à jour
     */
    public function update(Request $request, Employe $employe)
    {
        $request->validate([
            'nom'        => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            'email'      => 'required|email',
            'telephone'  => 'required|string|max:20',
            'role'       => 'required|string',
            'salaire'    => 'required|numeric|min:0',
            'photo'      => 'nullable|image|max:4096',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {

            // Effacer ancienne photo
            if (
                $employe->photo &&
                file_exists(public_path($employe->photo))
            ) {
                unlink(public_path($employe->photo));
            }

            $file = $request->file('photo');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(
                public_path('images/employes'),
                $filename
            );

            $data['photo'] = 'images/employes/'.$filename;
        }

        $employe->update($data);

        return redirect('/admin/employes')
            ->with(
                'success',
                "Les informations de l'employé ont été mises à jour avec succès."
            );
    }

    /**
     * Supprimer
     */
    public function destroy(Employe $employe)
    {
        if (
            $employe->photo &&
            file_exists(public_path($employe->photo))
        ) {
            unlink(public_path($employe->photo));
        }

        $employe->delete();

        return redirect('/admin/employes')
            ->with(
                'success',
                "L'employé a été supprimé avec succès."
            );
    }
}