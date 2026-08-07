<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Commande;
use App\Models\Paiement;
use App\Models\Plat;
use Carbon\Carbon;


class CaisseController extends Controller
{

    public function index()
    {


        /*
        |--------------------------------------------------------------------------
        | Commandes prêtes à encaisser
        |--------------------------------------------------------------------------
        */


        $commandesPretes = Commande::where('statut', 'terminee')
            ->orderBy('created_at', 'desc')
            ->get();



        $countPretes = $commandesPretes->count();







        /*
        |--------------------------------------------------------------------------
        | Commandes en cuisine / attente
        |--------------------------------------------------------------------------
        */


        $countEnAttente = Commande::whereIn(
            'statut',
            [
                'nouvelle',
                'en_preparation',
                'preparation'
            ]
        )->count();







        /*
        |--------------------------------------------------------------------------
        | Commandes payées aujourd'hui
        |--------------------------------------------------------------------------
        */


        $countPayeesJour = Paiement::whereDate(
            'created_at',
            Carbon::today()
        )
        ->count();



        $paidOrders = $countPayeesJour;







        /*
        |--------------------------------------------------------------------------
        | Chiffre affaires jour
        |--------------------------------------------------------------------------
        */


        $chiffreAffairesJour = Paiement::whereDate(
            'created_at',
            Carbon::today()
        )
        ->sum('montant');



        $todaySales = $chiffreAffairesJour;







        /*
        |--------------------------------------------------------------------------
        | Ticket moyen
        |--------------------------------------------------------------------------
        */


        $averageTicket = $countPayeesJour > 0

            ? $chiffreAffairesJour / $countPayeesJour

            : 0;








        /*
        |--------------------------------------------------------------------------
        | Derniers paiements
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
        | Répartition paiements
        |--------------------------------------------------------------------------
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
        | Meilleures ventes
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
        | Graphique chiffre affaires
        |--------------------------------------------------------------------------
        */


        $sales = Paiement::select(

            DB::raw(
                'DATE(created_at) as date'
            ),

            DB::raw(
                'SUM(montant) as total'
            )

        )

        ->whereMonth(
            'created_at',
            Carbon::now()->month
        )

        ->groupBy('date')

        ->orderBy('date')

        ->get();





        $salesLabels = $sales->pluck('date');


        $salesData = $sales->pluck('total');









        /*
        |--------------------------------------------------------------------------
        | Activités récentes caisse
        |--------------------------------------------------------------------------
        */


        $activities = Paiement::orderBy(
            'created_at',
            'desc'
        )
        ->limit(5)
        ->get()
        ->map(function($paiement){


            return (object)[

                'message' =>

                    "Paiement "
                    .$paiement->mode_paiement
                    ." de "
                    .$paiement->montant
                    ." HTG enregistré",


                'created_at' =>

                    $paiement->created_at

            ];


        });









        /*
        |--------------------------------------------------------------------------
        | Pourcentage paiements
        |--------------------------------------------------------------------------
        */


        $totalPaiements = max(
            Paiement::count(),
            1
        );



        $cashPercent = round(
            ($cashCount / $totalPaiements) * 100
        );



        $cardPercent = round(
            ($cardCount / $totalPaiements) * 100
        );



        $moncashPercent = round(
            ($moncashCount / $totalPaiements) * 100
        );



        $natcashPercent = round(
            ($natcashCount / $totalPaiements) * 100
        );









        return view(
            'caisse.index',
            compact(

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


                'activities',


                'cashPercent',

                'cardPercent',

                'moncashPercent',

                'natcashPercent'

            )
        );


    }

}