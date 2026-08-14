<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Paiement;
use App\Models\Plat;
use Carbon\Carbon;

class CaisseController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * DASHBOARD (index)
     * --------------------------------------------------------------------------
     */
    public function index()
    {
        $commandesPretes = Commande::where('statut', 'prete')
            ->orderBy('created_at', 'desc')
            ->get();

        $countPretes = $commandesPretes->count();

        $countEnAttente = Commande::whereIn('statut', [
            'nouvelle',
            'en_preparation',
            'preparation'
        ])->count();

        $countPayeesJour = Paiement::whereDate('created_at', Carbon::today())->count();

        $chiffreAffairesJour = Paiement::whereDate('created_at', Carbon::today())->sum('montant');

        $paidOrders = $countPayeesJour;
        $todaySales = $chiffreAffairesJour;

        $averageTicket = $countPayeesJour > 0
            ? $chiffreAffairesJour / $countPayeesJour
            : 0;

        $derniersPaiements = Paiement::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $cashCount = Paiement::where('mode_paiement', 'Espèces')->count();
        $cardCount = Paiement::where('mode_paiement', 'Carte')->count();
        $moncashCount = Paiement::where('mode_paiement', 'MonCash')->count();
        $natcashCount = Paiement::where('mode_paiement', 'NatCash')->count();
        $virementCount = Paiement::where('mode_paiement', 'Virement')->count();

        $bestSelling = Plat::orderBy('total_vendu', 'desc')
            ->limit(5)
            ->get();

        $sales = Paiement::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(montant) as total')
        )
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $salesLabels = $sales->pluck('date')->values();
        $salesData = $sales->pluck('total')->values();

        $orders = Paiement::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $orderLabels = $orders->pluck('date')->values();
        $orderData = $orders->pluck('total')->values();

        $activities = Paiement::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($paiement) {
                return (object) [
                    'message' => 'Paiement ' . $paiement->mode_paiement . ' de ' . number_format($paiement->montant, 2) . ' HTG enregistré',
                    'created_at' => $paiement->created_at
                ];
            });

        $totalPaiements = Paiement::count();

        if ($totalPaiements > 0) {
            $cashPercent = round(($cashCount / $totalPaiements) * 100, 1);
            $cardPercent = round(($cardCount / $totalPaiements) * 100, 1);
            $moncashPercent = round(($moncashCount / $totalPaiements) * 100, 1);
            $natcashPercent = round(($natcashCount / $totalPaiements) * 100, 1);
            $virementPercent = round(($virementCount / $totalPaiements) * 100, 1);
        } else {
            $cashPercent = 0;
            $cardPercent = 0;
            $moncashPercent = 0;
            $natcashPercent = 0;
            $virementPercent = 0;
        }

        return view('caisse.index', compact(
            'commandesPretes',
            'countPretes',
            'countEnAttente',
            'countPayeesJour',
            'chiffreAffairesJour',
            'derniersPaiements',
            'cashCount',
            'cardCount',
            'moncashCount',
            'natcashCount',
            'virementCount',
            'bestSelling',
            'todaySales',
            'paidOrders',
            'averageTicket',
            'salesLabels',
            'salesData',
            'orderLabels',
            'orderData',
            'activities',
            'cashPercent',
            'cardPercent',
            'moncashPercent',
            'natcashPercent',
            'virementPercent'
        ));
    }

    /**
     * --------------------------------------------------------------------------
     * API — Données dashboard en JSON (polling temps réel)
     * --------------------------------------------------------------------------
     */
    public function dashboardData()
    {
        $today = Carbon::today();

        $chiffreAffairesJour = Paiement::whereDate('created_at', $today)->sum('montant');
        $countPretes = Commande::where('statut', 'prete')->count();
        $countEnAttente = Commande::whereIn('statut', ['nouvelle', 'en_preparation', 'preparation'])->count();
        $countPayeesJour = Paiement::whereDate('created_at', $today)->count();

        $commandesPretes = Commande::with('items')
            ->where('statut', 'prete')
            ->orderBy('updated_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($cmd) {
                return [
                    'id' => $cmd->id,
                    'restaurant_table_id' => $cmd->restaurant_table_id,
                    'total' => number_format($cmd->total, 2),
                    'created_at' => $cmd->created_at->format('H:i'),
                    'items_count' => $cmd->items ? $cmd->items->sum('quantite') : 0
                ];
            });

        $derniersPaiements = Paiement::with('commande')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'commande_id' => $p->commande_id,
                    'montant' => number_format($p->montant, 2),
                    'mode_paiement' => $p->mode_paiement,
                    'created_at' => $p->created_at->format('d/m/Y H:i')
                ];
            });

        $cashCount = Paiement::where('mode_paiement', 'Espèces')->count();
        $cardCount = Paiement::where('mode_paiement', 'Carte')->count();
        $moncashCount = Paiement::where('mode_paiement', 'MonCash')->count();
        $natcashCount = Paiement::where('mode_paiement', 'NatCash')->count();
        $virementCount = Paiement::where('mode_paiement', 'Virement')->count();

        return response()->json([
            'stats' => [
                'chiffre' => number_format($chiffreAffairesJour, 2),
                'pretes' => $countPretes,
                'attente' => $countEnAttente,
                'payees' => $countPayeesJour
            ],
            'commandesPretes' => $commandesPretes,
            'derniersPaiements' => $derniersPaiements,
            'repatisyon' => [
                'cashCount' => $cashCount,
                'cardCount' => $cardCount,
                'moncashCount' => $moncashCount,
                'natcashCount' => $natcashCount,
                'virementCount' => $virementCount
            ],
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * LISTE COMMANDES
     * --------------------------------------------------------------------------
     */
    public function commandes()
    {
        $commandes = Commande::whereIn('statut', [
            'nouvelle',
            'en_preparation',
            'preparation',
            'prete',
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('caisse.commandes', compact('commandes'));
    }

    /**
     * --------------------------------------------------------------------------
     * HISTORIQUE
     * --------------------------------------------------------------------------
     */
    public function historique()
    {
        $paiements = Paiement::with('commande')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('caisse.historique', compact('paiements'));
    }

    /**
     * --------------------------------------------------------------------------
     * FACTURE
     * --------------------------------------------------------------------------
     */
    public function facture($id)
    {
        $paiement = Paiement::with([
            'commande.items.plat'
        ])->findOrFail($id);

        return view('caisse.facture', compact('paiement'));
    }

    /**
     * --------------------------------------------------------------------------
     * ENCAISSEMENT — Affiche la page
     * --------------------------------------------------------------------------
     */
    public function encaisser($id)
    {
        $commande = Commande::with('items.plat')->findOrFail($id);

        if ($commande->statut !== 'prete') {
            return redirect()->route('caisse.dashboard')
                ->with('error', 'Cette commande n\'est pas encore prête.');
        }

        return view('caisse.encaisser', compact('commande'));
    }

    /**
     * --------------------------------------------------------------------------
     * PAIEMENT — Traite le paiement
     * --------------------------------------------------------------------------
     */
    public function paiement(Request $request)
    {
        $request->validate([
            'commande_id' => 'required|exists:commandes,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:Espèces,Carte,MonCash,NatCash,Virement'
        ]);

        $commande = Commande::findOrFail($request->commande_id);

        if ($commande->statut === 'payee') {
            return redirect()->route('caisse.dashboard')
                ->with('error', 'Cette commande est déjà payée.');
        }

        $paiement = Paiement::create([
            'commande_id' => $commande->id,
            'montant' => $request->montant,
            'mode_paiement' => $request->mode_paiement,
            'caissier_id' => auth()->id()
        ]);

        $commande->update([
            'statut' => 'payee',
            'payee_at' => now()
        ]);

        return redirect()->route('caisse.dashboard')
            ->with('success', 'Paiement de ' . number_format($request->montant, 2) . ' HTG enregistré.');
    }

    /**
     * --------------------------------------------------------------------------
     * COUNT PRETES (API)
     * --------------------------------------------------------------------------
     */
    public function countPretes()
    {
        $count = Commande::where('statut', 'prete')
            ->where('archived', 0)
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }
}