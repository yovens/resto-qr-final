<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('admin.layouts.app', function ($view) {
            $stockAlertsCount = Product::whereColumn('quantite_actuelle', '<=', 'seuil_alerte')->count();
            
            $employeeAlertsCount = 0; 
            if (class_exists(\App\Models\Employe::class)) {
                $employeeAlertsCount = \App\Models\Employe::count(); 
            }

            $view->with([
                'stockAlertsCount' => $stockAlertsCount,
                'totalNotifications' => $stockAlertsCount
            ]);
        });
    }
}