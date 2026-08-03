<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Commande;
use App\Models\CommandeItem;

class DashboardController extends Controller
{
    public function index()
    {

        /*
        |--------------------------------------------------------------------------
        | 💰 VENTES DU JOUR
        |--------------------------------------------------------------------------
        */

        $todaySales = Commande::where('archived', false)
            ->whereDate('created_at', today())
            ->sum('total');



        /*
        |--------------------------------------------------------------------------
        | 📦 TOTAL COMMANDES ACTIVES
        |--------------------------------------------------------------------------
        */

        $totalOrders = Commande::where('archived', false)
            ->count();




        /*
        |--------------------------------------------------------------------------
        | 📈 GRAPH VENTES 7 DERNIERS JOURS
        |--------------------------------------------------------------------------
        */

        $salesChart = collect();


        for($i = 6; $i >= 0; $i--){

            $date = Carbon::today()->subDays($i);


            $total = Commande::where('archived', false)
                ->whereDate('created_at', $date)
                ->sum('total');



            $salesChart->push([

                'date' => $date->format('D'),

                'total' => $total

            ]);

        }




        /*
        |--------------------------------------------------------------------------
        | 📊 HISTORIQUE VENTES COMPLET
        |--------------------------------------------------------------------------
        */

        $monthlySales = Commande::select(

                DB::raw("DATE(created_at) as date"),

                DB::raw("SUM(total) as total")

            )

            ->where('archived', false)

            ->groupBy('date')

            ->orderBy('date','asc')

            ->get();





        /*
        |--------------------------------------------------------------------------
        | 🍔 TOP PLATS
        |--------------------------------------------------------------------------
        */

        $topPlats = CommandeItem::whereHas(
            'commande',
            function($q){

                $q->where('archived',false);

            }

        )

        ->select(

            'plat_id',

            DB::raw(
                'SUM(quantite) as total'
            )

        )

        ->groupBy('plat_id')

        ->with('plat')

        ->orderByDesc('total')

        ->limit(5)

        ->get();






        /*
        |--------------------------------------------------------------------------
        | 🍽️ STATUT COMMANDES
        |--------------------------------------------------------------------------
        */

        $completedOrders = Commande::where(
                'statut',
                'prete'
            )

            ->where('archived',false)

            ->count();



        $preparingOrders = Commande::where(
                'statut',
                'en_preparation'
            )

            ->where('archived',false)

            ->count();




        $newOrders = Commande::where(
                'statut',
                'nouvelle'
            )

            ->where('archived',false)

            ->count();







        /*
        |--------------------------------------------------------------------------
        | 📜 COMMANDES RECENTES
        |--------------------------------------------------------------------------
        */

        $recentOrders = Commande::with('table')

            ->where('archived',false)

            ->orderBy(
                'created_at',
                'desc'
            )

            ->limit(20)

            ->get();







        /*
        |--------------------------------------------------------------------------
        | 🚀 ENVOI VERS DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.dashboard',
            compact(

                'todaySales',

                'totalOrders',

                'salesChart',

                'monthlySales',

                'topPlats',

                'recentOrders',

                'completedOrders',

                'preparingOrders',

                'newOrders'

            )
        );

    }
}