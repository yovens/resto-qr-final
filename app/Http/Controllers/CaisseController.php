<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Commande;
use App\Models\Paiement;
use App\Models\Plat;
use Carbon\Carbon;

class CaisseController extends Controller
{
    /**
     * Dashboard de la caisse
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | COMMANDES PRÊTES À ENCAISSER
        |--------------------------------------------------------------------------
        |
        | KitchenController met :
        |
        | statut = "prete"
        |
        | Donc la caisse doit chercher exactement "prete".
        |
        */

        $commandesPretes = Commande::where('statut', 'prete')
            ->orderBy('created_at', 'desc')
            ->get();

        $countPretes = $commandesPretes->count();


        /*
        |--------------------------------------------------------------------------
        | COMMANDES EN ATTENTE / CUISINE
        |--------------------------------------------------------------------------
        */

        $countEnAttente = Commande::whereIn('statut', [
            'nouvelle',
            'en_preparation',
            'preparation'
        ])->count();


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DU JOUR
        |--------------------------------------------------------------------------
        */

        $countPayeesJour = Paiement::whereDate(
            'created_at',
            Carbon::today()
        )->count();


        /*
        |--------------------------------------------------------------------------
        | CHIFFRE D'AFFAIRES DU JOUR
        |--------------------------------------------------------------------------
        */

        $chiffreAffairesJour = Paiement::whereDate(
            'created_at',
            Carbon::today()
        )->sum('montant');


        $paidOrders = $countPayeesJour;

        $todaySales = $chiffreAffairesJour;


        /*
        |--------------------------------------------------------------------------
        | TICKET MOYEN
        |--------------------------------------------------------------------------
        */

        $averageTicket = $countPayeesJour > 0
            ? $chiffreAffairesJour / $countPayeesJour
            : 0;


        /*
        |--------------------------------------------------------------------------
        | DERNIERS PAIEMENTS
        |--------------------------------------------------------------------------
        */

        $derniersPaiements = Paiement::orderBy(
            'created_at',
            'desc'
        )
        ->limit(10)
        ->get();


        /*
        |--------------------------------------------------------------------------
        | RÉPARTITION DES PAIEMENTS
        |--------------------------------------------------------------------------
        |
        | Ces données viennent directement de la table "paiements".
        |
        */

        $cashCount = Paiement::where(
            'mode_paiement',
            'Espèces'
        )->count();


        $cardCount = Paiement::where(
            'mode_paiement',
            'Carte'
        )->count();


        $moncashCount = Paiement::where(
            'mode_paiement',
            'MonCash'
        )->count();


        $natcashCount = Paiement::where(
            'mode_paiement',
            'NatCash'
        )->count();


        $virementCount = Paiement::where(
            'mode_paiement',
            'Virement'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | MEILLEURES VENTES
        |--------------------------------------------------------------------------
        */

        $bestSelling = Plat::orderBy(
            'total_vendu',
            'desc'
        )
        ->limit(5)
        ->get();


        /*
        |--------------------------------------------------------------------------
        | GRAPHIQUE CHIFFRE D'AFFAIRES
        |--------------------------------------------------------------------------
        |
        | CA par jour pour le mois actuel.
        |
        */

        $sales = Paiement::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(montant) as total')
        )
        ->whereMonth(
            'created_at',
            Carbon::now()->month
        )
        ->whereYear(
            'created_at',
            Carbon::now()->year
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();


        $salesLabels = $sales
            ->pluck('date')
            ->values();


        $salesData = $sales
            ->pluck('total')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | GRAPHIQUE COMMANDES ENCAISSÉES
        |--------------------------------------------------------------------------
        |
        | Nombre de paiements par jour.
        |
        */

        $orders = Paiement::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->whereMonth(
            'created_at',
            Carbon::now()->month
        )
        ->whereYear(
            'created_at',
            Carbon::now()->year
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();


        $orderLabels = $orders
            ->pluck('date')
            ->values();


        $orderData = $orders
            ->pluck('total')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | ACTIVITÉS RÉCENTES
        |--------------------------------------------------------------------------
        */

        $activities = Paiement::orderBy(
            'created_at',
            'desc'
        )
        ->limit(5)
        ->get()
        ->map(function ($paiement) {

            return (object) [

                'message' =>
                    'Paiement '
                    . $paiement->mode_paiement
                    . ' de '
                    . number_format(
                        $paiement->montant,
                        2
                    )
                    . ' HTG enregistré',

                'created_at' =>
                    $paiement->created_at
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | POURCENTAGE DES PAIEMENTS
        |--------------------------------------------------------------------------
        |
        | Les pourcentages sont calculés automatiquement
        | selon les paiements réellement enregistrés.
        |
        */

        $totalPaiements = Paiement::count();


        if ($totalPaiements > 0) {

            $cashPercent = round(
                ($cashCount / $totalPaiements) * 100,
                1
            );

            $cardPercent = round(
                ($cardCount / $totalPaiements) * 100,
                1
            );

            $moncashPercent = round(
                ($moncashCount / $totalPaiements) * 100,
                1
            );

            $natcashPercent = round(
                ($natcashCount / $totalPaiements) * 100,
                1
            );

            $virementPercent = round(
                ($virementCount / $totalPaiements) * 100,
                1
            );

        } else {

            $cashPercent = 0;

            $cardPercent = 0;

            $moncashPercent = 0;

            $natcashPercent = 0;

            $virementPercent = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | RETOUR DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'caisse.index',
            compact(

                /*
                | Commandes
                */

                'commandesPretes',

                'countPretes',

                'countEnAttente',


                /*
                | Paiements
                */

                'countPayeesJour',

                'chiffreAffairesJour',

                'derniersPaiements',


                /*
                | Moyens de paiement
                */

                'cashCount',

                'cardCount',

                'moncashCount',

                'natcashCount',

                'virementCount',


                /*
                | Statistiques
                */

                'bestSelling',

                'todaySales',

                'paidOrders',

                'averageTicket',


                /*
                | Graphique CA
                */

                'salesLabels',

                'salesData',


                /*
                | Graphique commandes
                */

                'orderLabels',

                'orderData',


                /*
                | Activités
                */

                'activities',


                /*
                | Pourcentages
                */

                'cashPercent',

                'cardPercent',

                'moncashPercent',

                'natcashPercent',

                'virementPercent'
            )
        );
    }

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

public function historique()
{
    $paiements = Paiement::with('commande')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('caisse.historique', compact('paiements'));
}
public function facture($id)
{
    $paiement = Paiement::with([
        'commande.items.plat'
    ])->findOrFail($id);

    return view('caisse.facture', compact('paiement'));
}
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

