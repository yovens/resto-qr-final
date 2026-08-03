<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin; // <-- Doit correspondre au dossier

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Plat;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Dashboard admin
     */
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalCommandes' => Commande::count(),
            'commandesEnCours' => Commande::where('statut', '!=', 'servie')->count(),
            'plats' => Plat::count(),
            'revenuTotal' => Commande::sum('total')
        ]);
    }
    public function ventes(Request $request)
{
    $query = Commande::with('table','items.plat');

    // FILTRE MOIS
    if ($request->month) {
        $query->whereMonth('created_at', $request->month);
    }

    // FILTRE ANNÉE
    if ($request->year) {
        $query->whereYear('created_at', $request->year);
    }

    $commandes = $query->latest()->get();

    // TOTAL VENTES
    $total = $commandes->sum('total');

    // GROUP BY MOIS (statistique)
    $parMois = Commande::selectRaw('MONTH(created_at) as mois, SUM(total) as total')
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();

    return view('admin.ventes', compact(
        'commandes',
        'total',
        'parMois'
    ));
}
}