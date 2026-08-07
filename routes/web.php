<?php

use Illuminate\Support\Facades\Route;


// CONTROLLERS
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Plat\PlatController;
use App\Http\Controllers\Table\TableController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Client\WaitingController;
use App\Http\Controllers\Admin\VentesController;
use App\Http\Controllers\PaiementController;

use App\Models\Commande;



/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return redirect('/login');

});





/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECTION
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {


    $user = auth()->user();



    if (
        $user->role === 'admin' ||
        $user->role_id == 1 ||
        $user->email === 'admin@resto.com'
    ) {


        return redirect('/admin/dashboard');


    }



    if (
        $user->role === 'caissiere' ||
        $user->role_id == 2 ||
        $user->email === 'caisse@resto.com'
    ) {


        return redirect('/caisse/dashboard');


    }



    return redirect('/menu/1');



})->middleware('auth')->name('dashboard');







/*
|--------------------------------------------------------------------------
| CLIENT PUBLIC
|--------------------------------------------------------------------------
*/


Route::get(
    '/menu/{table}',
    [ClientController::class,'menu']
);



Route::get(
    '/menu/{tableId}/plat/{id}',
    [ClientController::class,'showPlat']
)->name('client.plat.show');



Route::post(
    '/cart/add',
    [ClientController::class,'addToCart']
);



Route::post(
    '/checkout',
    [ClientController::class,'checkout']
);



Route::get(
    '/waiting/{tableId}/{commandeId?}',
    [WaitingController::class,'index']
)->name('client.waiting');



Route::post(
    '/commander',
    [OrderController::class,'store']
);



Route::post(
    '/payment',
    [PaymentController::class,'pay']
);







/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/


Route::prefix('admin')
->middleware(['auth'])
->group(function(){



    Route::get(
        '/dashboard',
        [DashboardController::class,'index']
    );



    Route::get(
        '/ventes',
        [VentesController::class,'index']
    );



    Route::post(
        '/commandes/archive/{id}',
        [VentesController::class,'archive']
    );



    Route::post(
        '/commandes/cloturer-journee',
        [VentesController::class,'cloturerJournee']
    );



    Route::resource(
        'plats',
        PlatController::class
    );



    Route::resource(
        'tables',
        TableController::class
    );



    Route::resource(
        'categories',
        CategoryController::class
    );



    Route::resource(
        'stock',
        \App\Http\Controllers\Admin\StockController::class
    );



    Route::get(
        '/stock-mouvement',
        [
            \App\Http\Controllers\Admin\StockController::class,
            'mouvementForm'
        ]
    );



    Route::post(
        '/stock-mouvement',
        [
            \App\Http\Controllers\Admin\StockController::class,
            'mouvementStore'
        ]
    );



    Route::resource(
        'employes',
        \App\Http\Controllers\Admin\EmployeController::class
    );



    Route::resource(
        'suppliers',
        \App\Http\Controllers\Admin\SupplierController::class
    );



    Route::resource(
        'users',
        \App\Http\Controllers\Admin\UserController::class
    );



    Route::get(
        '/notifications',
        [
            \App\Http\Controllers\Admin\NotificationController::class,
            'index'
        ]
    );



    Route::get(
        '/reports',
        [
            \App\Http\Controllers\Admin\ReportController::class,
            'index'
        ]
    );



    Route::get(
        '/profile',
        [
            \App\Http\Controllers\Admin\ProfileController::class,
            'edit'
        ]
    );



    Route::put(
        '/profile',
        [
            \App\Http\Controllers\Admin\ProfileController::class,
            'update'
        ]
    );





    Route::delete(
        '/commandes/{id}',
        function($id){

            Commande::destroy($id);

            return back();

        }
    );



});









/*
|--------------------------------------------------------------------------
| CAISSE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
->prefix('caisse')
->group(function(){



    /*
    Dashboard caisse
    */

    Route::get(
        '/dashboard',
        [
            \App\Http\Controllers\CaisseController::class,
            'index'
        ]
    )->name('caisse.dashboard');





    /*
    Page encaissement
    */

    Route::get(
        '/encaisser/{id}',
        [
            PaiementController::class,
            'create'
        ]
    )->name('caisse.encaisser');





    /*
    Validation paiement
    */

    Route::post(
        '/paiement',
        [
            PaiementController::class,
            'store'
        ]
    )->name('caisse.paiement');





    /*
    Liste paiements
    */

    Route::get(
        '/paiements',
        [
            PaiementController::class,
            'index'
        ]
    )->name('caisse.paiements');





    /*
    Facture
    */

    Route::get(
        '/facture/{id}',
        [
            PaiementController::class,
            'facture'
        ]
    )->name('caisse.facture');



});

Route::middleware(['auth'])
->prefix('caisse')
->group(function(){


    Route::get(
        '/encaisser/{id}',
        [
            PaiementController::class,
            'create'
        ]
    )->name('caisse.encaisser');



});

/*
|--------------------------------------------------------------------------
| CUISINE
|--------------------------------------------------------------------------
*/


Route::middleware(['auth'])
->group(function(){



    Route::get(
        '/cuisine',
        [
            KitchenController::class,
            'index'
        ]
    )->name('cuisine.index');



    Route::post(
        '/cuisine/update/{id}',
        [
            KitchenController::class,
            'updateStatus'
        ]
    )->name('cuisine.update');



});







require __DIR__.'/auth.php';