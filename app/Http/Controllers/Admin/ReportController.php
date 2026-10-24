<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Rapports de ventes.
     * Une vente = une commande au statut « payee » (c'est ce que la caisse enregistre).
     * Les regroupements sont faits en PHP : fonctionne sur MySQL comme sur SQLite.
     */
    public function index(Request $request)
    {
        $selectedYear  = (int) $request->input('year', date('Y'));
        $selectedMonth = (int) $request->input('month', date('m'));

        $payees = fn () => Commande::query()->where('statut', 'payee');

        $totalVentesGlobal    = (float) $payees()->sum('total');
        $totalCommandesGlobal = Commande::count();

        $ventesAnnee         = (float) $payees()->whereYear('created_at', $selectedYear)->sum('total');
        $commandesAnneeCount = Commande::whereYear('created_at', $selectedYear)->count();

        $ventesMois = (float) $payees()
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->sum('total');

        $commandesMoisCount = Commande::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->count();

        $monthlyStats = $payees()
            ->whereYear('created_at', $selectedYear)
            ->get(['total', 'created_at'])
            ->groupBy(fn ($c) => (int) $c->created_at->format('n'))
            ->map(fn ($g, $mois) => (object) [
                'mois'            => $mois,
                'total_ventes'    => (float) $g->sum('total'),
                'total_commandes' => $g->count(),
            ])
            ->sortKeys()
            ->values();

        $premiere = Commande::min('created_at');
        $debut    = $premiere ? (int) date('Y', strtotime($premiere)) : (int) date('Y');
        $years    = collect(range((int) date('Y'), min($debut, (int) date('Y'))));

        return view('admin.reports.index', compact(
            'totalVentesGlobal',
            'totalCommandesGlobal',
            'ventesAnnee',
            'commandesAnneeCount',
            'ventesMois',
            'commandesMoisCount',
            'monthlyStats',
            'selectedYear',
            'selectedMonth',
            'years'
        ));
    }
}
