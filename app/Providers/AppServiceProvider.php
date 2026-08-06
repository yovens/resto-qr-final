<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Commande;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        View::composer('admin.layouts.*', function ($view) {


            /*
            |--------------------------------------------------------------------------
            | ALERTES STOCK
            |--------------------------------------------------------------------------
            */

            $stockAlertsCount = 0;

            if (class_exists(Product::class)) {

                $stockAlertsCount = Product::whereColumn(
                    'quantite_actuelle',
                    '<=',
                    'seuil_alerte'
                )->count();

            }



            /*
            |--------------------------------------------------------------------------
            | COMMANDES CUISINE
            |--------------------------------------------------------------------------
            */

            $commandesCuisine = 0;

            if (class_exists(Commande::class)) {

                $commandesCuisine = Commande::whereIn(
                    'statut',
                    [
                        'nouvelle',
                        'en_preparation'
                    ]
                )->count();

            }



            /*
            |--------------------------------------------------------------------------
            | TOTAL NOTIFICATIONS
            |--------------------------------------------------------------------------
            */

            $notificationCount =
                $stockAlertsCount +
                $commandesCuisine;



            /*
            |--------------------------------------------------------------------------
            | SEND TO ALL ADMIN LAYOUTS
            |--------------------------------------------------------------------------
            */

            $view->with([

                // Stock badge
                'stockAlertsCount' => $stockAlertsCount,


                // Cuisine badge
                'commandesCuisine' => $commandesCuisine,


                // Bell notification
                'notificationCount' => $notificationCount,


                // Total général
                'totalNotifications' => $notificationCount,

            ]);


        });

    }
}