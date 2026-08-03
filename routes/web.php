<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Plat\PlatController;
use App\Http\Controllers\Table\TableController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FactureController;
use App\Models\Commande;



use App\Http\Controllers\OrderController;


use App\Http\Controllers\Client\WaitingController;


Route::get(
'/waiting/{tableId}',
[WaitingController::class,'index']
)
->name('client.waiting');
Route::post('/commander', [OrderController::class, 'store']);
use App\Http\Controllers\Admin\VentesController;

Route::get('/admin/ventes', [VentesController::class, 'index']);
/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/admin/dashboard');
});
Route::get('/', function () {
    return redirect('/menu/1'); // Sa ap voye kliyan an sou Table 1 otomatikman
});
/*
|--------------------------------------------------------------------------
| CLIENT
|--------------------------------------------------------------------------
*/
Route::get('/menu/{table}', [ClientController::class, 'menu']);
Route::post('/cart/add', [ClientController::class, 'addToCart']);
Route::post('/checkout', [ClientController::class, 'checkout']);
Route::get(
'/menu/{tableId}/plat/{id}',
[ClientController::class,'showPlat']
)
->name('client.plat.show');
Route::get('/waiting/{tableId}/{commandeId?}', [WaitingController::class, 'index'])->name('client.waiting');
/*
|--------------------------------------------------------------------------
| CUISINE
|--------------------------------------------------------------------------
*/
Route::get('/cuisine', [KitchenController::class, 'index']);
Route::post('/cuisine/update/{id}', [KitchenController::class, 'updateStatus']);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

  Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

// Fè wout la POST pou l matche ak sa fòm nan ap voye
    Route::post('/commandes/archive/{id}', [App\Http\Controllers\Admin\VentesController::class, 'archive']);
Route::post('/commandes/cloturer-journee', [App\Http\Controllers\Admin\VentesController::class, 'cloturerJournee']);
    Route::resource('plats', PlatController::class);
    Route::resource('tables', TableController::class);
    Route::resource('categories', CategoryController::class);

    Route::delete('/commandes/{id}', function ($id) {
        \App\Models\Commande::destroy($id);
        return back();
    });

});
   
 

/*
|--------------------------------------------------------------------------
| FACTURE
|--------------------------------------------------------------------------
*/
Route::get('/facture/{id}', [FactureController::class, 'generate']);

/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/
Route::post('/payment', [PaymentController::class, 'pay']);


