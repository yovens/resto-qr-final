<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Commande;
use App\Models\Paiement;
use App\Models\RestaurantTable;
use App\Services\CommandeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CaisseController extends Controller
{
    /**
     * Commandes à encaisser :
     *  - « prete »  : la cuisine a fini ;
     *  - « servie » : déjà apportée à table, mais pas encore payée.
     */
    public const A_ENCAISSER = ['prete', 'servie'];

    /** Commandes encore en cuisine. */
    public const EN_CUISINE = ['nouvelle', 'acceptee', 'en_preparation', 'preparation'];

    public const MODES = ['Espèces', 'Carte', 'MonCash', 'NatCash', 'Virement'];

    /* ======================================================================
       TABLEAU DE BORD
       ====================================================================== */
    public function index()
    {
        $today = Carbon::today();

        $commandesPretes = $this->aEncaisser()->get();

        $paiementsJour      = Paiement::whereDate('created_at', $today);
        $chiffreAffairesJour = (float) (clone $paiementsJour)->sum('montant');
        $countPayeesJour     = (clone $paiementsJour)->count();

        $modes = $this->repartitionModes($today);

        // Encaissements des 7 derniers jours
        $parJour = Paiement::where('created_at', '>=', $today->copy()->subDays(6))
            ->get(['montant', 'created_at'])
            ->groupBy(fn ($p) => $p->created_at->format('Y-m-d'))
            ->map(fn ($g) => (float) $g->sum('montant'));
        $salesLabels = [];
        $salesData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i);
            $salesLabels[] = ucfirst($d->locale('fr')->isoFormat('ddd'));
            $salesData[]   = $parJour[$d->format('Y-m-d')] ?? 0;
        }

        return view('caisse.dashboard', [
            'commandesPretes'     => $commandesPretes,
            'countPretes'         => $commandesPretes->count(),
            'countEnAttente'      => $this->enCuisine()->count(),
            'countPayeesJour'     => $countPayeesJour,
            'chiffreAffairesJour' => $chiffreAffairesJour,
            'derniersPaiements'   => Paiement::whereDate('created_at', $today)->latest()->limit(10)->get(),
            'salesLabels'         => $salesLabels,
            'salesData'           => $salesData,
        ] + $modes);
    }

    /**
     * Données JSON pour l'actualisation automatique du tableau de bord (toutes les 10 s).
     */
    public function dashboardData()
    {
        $today = Carbon::today();

        return response()->json([
            'stats' => [
                'chiffre' => (float) Paiement::whereDate('created_at', $today)->sum('montant'),
                'pretes'  => $this->aEncaisser()->count(),
                'attente' => $this->enCuisine()->count(),
                'payees'  => Paiement::whereDate('created_at', $today)->count(),
            ],
            'commandesPretes' => $this->aEncaisser()->limit(30)->get()->map(fn ($c) => [
                'id'                  => $c->id,
                'restaurant_table_id' => $c->restaurant_table_id,
                'table'               => ['numero' => $c->table->numero ?? $c->restaurant_table_id],
                'statut'              => $c->statut,
                'total'               => (float) $c->total,
                'created_at'          => $c->created_at->toIso8601String(),
            ]),
            'derniersPaiements' => Paiement::whereDate('created_at', $today)->latest()->limit(10)->get()->map(fn ($p) => [
                'id'            => $p->id,
                'commande_id'   => $p->commande_id,
                'montant'       => (float) $p->montant,
                'mode_paiement' => $p->mode_paiement,
                'created_at'    => $p->created_at->toIso8601String(),
            ]),
            'repatisyon' => $this->repartitionModes($today),
            'timestamp'  => now()->format('H:i:s'),
        ])->header('Cache-Control', 'no-store');
    }

    /** Nombre de commandes à encaisser (badge du menu). */
    public function countPretes()
    {
        return response()->json(['count' => $this->aEncaisser()->count()])
            ->header('Cache-Control', 'no-store');
    }

    /** Statistiques légères (compatibilité avec l'ancienne interface). */
    public function stats()
    {
        $today = Carbon::today();

        return response()->json([
            'pretes'       => $this->aEncaisser()->count(),
            'attente'      => $this->enCuisine()->count(),
            'payees_jour'  => Paiement::whereDate('created_at', $today)->count(),
            'chiffre_jour' => (float) Paiement::whereDate('created_at', $today)->sum('montant'),
            'timestamp'    => now()->format('H:i:s'),
        ]);
    }

    /* ======================================================================
       LISTES
       ====================================================================== */
    public function commandes()
    {
        $commandes = Commande::with('table')
            ->where('archived', false)
            ->whereIn('statut', array_merge(self::EN_CUISINE, self::A_ENCAISSER))
            ->latest()
            ->get();

        return view('caisse.commandes', compact('commandes'));
    }

    public function historique()
    {
        $paiements = Paiement::with('commande.table')->latest()->paginate(20);

        return view('caisse.paiements', compact('paiements'));
    }

    /** Recherche rapide (n° de commande ou n° de table). */
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $commandes = Commande::with('table')
            ->where('archived', false)
            ->whereIn('statut', array_merge(self::EN_CUISINE, self::A_ENCAISSER))
            ->where(function ($w) use ($q) {
                $w->where('id', (int) $q)
                  ->orWhereHas('table', fn ($t) => $t->where('numero', (int) $q));
            })
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id'     => $c->id,
                'table'  => $c->table->numero ?? $c->restaurant_table_id,
                'statut' => $c->statut,
                'total'  => (float) $c->total,
                'heure'  => $c->created_at->format('H:i'),
                'url'    => route('caisse.encaisser', $c->id),
            ]);

        return response()->json($commandes);
    }

    /* ======================================================================
       ENCAISSEMENT
       ====================================================================== */
    public function encaisser($id)
    {
        $commande = Commande::with(['items.plat', 'table'])->findOrFail($id);

        if ($commande->statut === 'payee') {
            $paiement = $commande->paiements()->latest()->first();
            return $paiement
                ? redirect()->route('caisse.facture', $paiement->id)->with('error', 'Cette commande est déjà payée.')
                : redirect()->route('caisse.dashboard')->with('error', 'Cette commande est déjà payée.');
        }

        if (!in_array($commande->statut, self::A_ENCAISSER, true)) {
            return redirect()->route('caisse.commandes')
                ->with('error', 'La commande #' . $commande->id . ' est encore en cuisine.');
        }

        return view('caisse.encaisser', compact('commande'));
    }

    public function paiement(Request $request)
    {
        $data = $request->validate([
            'commande_id'   => 'required|integer|exists:commandes,id',
            'mode_paiement' => 'required|in:' . implode(',', self::MODES),
        ], [
            'mode_paiement.required' => 'Choisissez un mode de paiement.',
        ]);

        $resultat = DB::transaction(function () use ($data) {
            // Verrou : deux caisses ne peuvent pas encaisser la même commande en même temps
            $commande = Commande::lockForUpdate()->findOrFail($data['commande_id']);

            if ($commande->statut === 'payee' || $commande->paiements()->exists()) {
                return ['erreur' => 'La commande #' . $commande->id . ' est déjà payée.'];
            }
            if (!in_array($commande->statut, self::A_ENCAISSER, true)) {
                return ['erreur' => 'La commande #' . $commande->id . ' n\'est pas encore prête.'];
            }

            // Le montant vient TOUJOURS de la commande, jamais du formulaire
            $paiement = Paiement::create([
                'commande_id'   => $commande->id,
                'montant'       => $commande->total,
                'mode_paiement' => $data['mode_paiement'],
                'caissier'      => auth()->user()->name ?? null,
            ]);
            $paiement->update([
                'numero_facture' => 'FAC-' . now()->format('Ymd') . '-' . str_pad($paiement->id, 5, '0', STR_PAD_LEFT),
            ]);

            $commande->update(['statut' => 'payee']);

            return ['paiement' => $paiement];
        });

        if (isset($resultat['erreur'])) {
            return redirect()->route('caisse.dashboard')->with('error', $resultat['erreur']);
        }

        $p = $resultat['paiement'];

        return redirect()->route('caisse.facture', $p->id)
            ->with('success', 'Paiement de ' . number_format($p->montant, 2, ',', ' ') . ' HTG enregistré.');
    }

    public function facture($id)
    {
        $paiement = Paiement::with(['commande.items.plat', 'commande.table'])->findOrFail($id);

        return view('caisse.facture', compact('paiement'));
    }

    /* ======================================================================
       PRISE DE COMMANDE (clients sans téléphone)
       ====================================================================== */
    public function nouvelleCommande(Request $request)
    {
        $tables = RestaurantTable::orderBy('numero')->get();

        $categories = Category::with(['plats' => fn ($q) => $q->where('disponible', true)->orderBy('nom')])
            ->orderBy('nom')
            ->get()
            ->filter(fn ($c) => $c->plats->isNotEmpty())
            ->values();

        // Tables qui ont déjà une commande en cours (affichées comme occupées)
        $occupees = Commande::where('archived', false)
            ->whereIn('statut', array_merge(self::EN_CUISINE, self::A_ENCAISSER))
            ->pluck('restaurant_table_id')
            ->unique()
            ->values();

        return view('caisse.nouvelle-commande', [
            'tables'       => $tables,
            'categories'   => $categories,
            'occupees'     => $occupees,
            'tableChoisie' => (int) $request->query('table'),
            'serviceRate'  => CommandeService::TAUX_SERVICE,
        ]);
    }

    public function storeCommande(Request $request, CommandeService $service)
    {
        $data = $request->validate([
            'table_id'         => 'required|integer|exists:restaurant_tables,id',
            'note'             => 'nullable|string|max:500',
            'items'            => 'required|array|min:1|max:50',
            'items.*.plat_id'  => 'required|integer',
            'items.*.quantite' => 'required|integer|min:1|max:99',
        ], [
            'table_id.required' => 'Choisissez la table.',
            'items.required'    => 'Ajoutez au moins un plat.',
            'items.min'         => 'Ajoutez au moins un plat.',
        ]);

        try {
            $commande = $service->creer((int) $data['table_id'], $data['items'], $data['note'] ?? null);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()->route('caisse.commandes')->with(
            'success',
            'Commande #' . $commande->id . ' envoyée en cuisine (table ' . ($commande->table->numero ?? $commande->restaurant_table_id) . ').'
        );
    }

    /* ======================================================================
       OUTILS
       ====================================================================== */
    private function aEncaisser()
    {
        return Commande::with('table')
            ->where('archived', false)
            ->whereIn('statut', self::A_ENCAISSER)
            ->oldest();
    }

    private function enCuisine()
    {
        return Commande::where('archived', false)->whereIn('statut', self::EN_CUISINE);
    }

    /** Nombre de paiements par mode, pour un jour donné. */
    private function repartitionModes(Carbon $jour): array
    {
        $parMode = Paiement::whereDate('created_at', $jour)
            ->select('mode_paiement', DB::raw('COUNT(*) as n'))
            ->groupBy('mode_paiement')
            ->pluck('n', 'mode_paiement');

        return [
            'cashCount'     => (int) ($parMode['Espèces'] ?? 0),
            'cardCount'     => (int) ($parMode['Carte'] ?? 0),
            'moncashCount'  => (int) ($parMode['MonCash'] ?? 0),
            'natcashCount'  => (int) ($parMode['NatCash'] ?? 0),
            'virementCount' => (int) ($parMode['Virement'] ?? 0),
        ];
    }
}
