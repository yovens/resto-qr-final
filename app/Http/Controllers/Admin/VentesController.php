<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Services\BackupService;
use Illuminate\Http\Request;

class VentesController extends Controller
{
    /**
     * Historique des ventes : commandes payées (ou archivées par une clôture).
     */
    public function index(Request $request)
    {
        $vendues = fn ($q) => $q->where(fn ($w) => $w->where('statut', 'payee')->orWhere('archived', true))
                                 ->whereNotIn('statut', ['annulee']);

        $query = Commande::with('table', 'items.plat')->tap($vendues);

        if ($request->filled('month')) {
            $query->whereMonth('created_at', (int) $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', (int) $request->year);
        }

        $commandes = $query->latest()->paginate(30)->withQueryString();
        $total     = (float) (clone $query)->reorder()->sum('total');

        // Ventes par mois de l'année choisie (calcul en PHP : fonctionne sur MySQL et SQLite)
        $annee   = (int) ($request->year ?: date('Y'));
        $parMois = Commande::query()->tap($vendues)
            ->whereYear('created_at', $annee)
            ->get(['total', 'created_at'])
            ->groupBy(fn ($c) => (int) $c->created_at->format('n'))
            ->map(fn ($g, $mois) => (object) ['mois' => $mois, 'total' => (float) $g->sum('total')])
            ->sortKeys()
            ->values();

        return view('admin.ventes.index', compact('commandes', 'total', 'parMois'));
    }

    public function archive($id)
    {
        Commande::findOrFail($id)->update(['archived' => true]);

        return back()->with('success', 'Commande archivée.');
    }

    /**
     * Clôture de la journée :
     *  - archive seulement les commandes PAYÉES (une commande non payée reste visible en caisse) ;
     *  - fait une sauvegarde automatique de la base.
     */
    public function cloturerJournee(BackupService $backups)
    {
        $archivees = Commande::where('archived', false)->where('statut', 'payee')->update(['archived' => true]);

        $restantes = Commande::where('archived', false)->whereNotIn('statut', ['payee', 'annulee'])->count();

        $sauvegarde = null;
        try {
            $sauvegarde = $backups->creer(true);
        } catch (\Throwable $e) {
            report($e);
        }

        $message = "Journée clôturée : $archivees commande(s) archivée(s).";
        if ($restantes) {
            $message .= " Attention : $restantes commande(s) non payée(s) restent ouvertes.";
        }
        $message .= $sauvegarde ? ' Sauvegarde créée.' : ' La sauvegarde automatique a échoué, faites-en une manuellement.';

        return back()->with($restantes || !$sauvegarde ? 'error' : 'success', $message);
    }
}
