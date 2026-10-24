<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;

class BackupController extends Controller
{
    public function __construct(private BackupService $backups) {}

    public function index()
    {
        return view('admin.backups.index', [
            'sauvegardes' => $this->backups->liste(),
            'dossier'     => $this->backups->dossier(),
        ]);
    }

    public function store()
    {
        try {
            $nom = $this->backups->creer();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'La sauvegarde a échoué : ' . $e->getMessage());
        }

        return redirect()->route('admin.backups.index')->with('success', 'Sauvegarde créée : ' . $nom);
    }

    public function download(string $fichier)
    {
        return response()->download($this->backups->chemin($fichier), $fichier, [
            'Content-Type' => 'application/gzip',
        ]);
    }

    public function destroy(string $fichier)
    {
        $this->backups->supprimer($fichier);

        return back()->with('success', 'Sauvegarde supprimée.');
    }
}
