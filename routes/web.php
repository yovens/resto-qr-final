<?php

use Illuminate\Support\Facades\Route;

// KONTWOLÈ YO
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Plat\PlatController;
use App\Http\Controllers\Table\TableController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Client\WaitingController;
use App\Http\Controllers\Admin\VentesController;
use App\Models\Commande;


/*
|--------------------------------------------------------------------------
| HOME & DASHBOARD (REDIREKSYON APRE LOGIN)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/login'); 
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Tcheke si se Admin
    if ($user->role === 'admin' || $user->role_id == 1 || $user->email === 'admin@resto.com') { 
        return redirect('/admin/dashboard');
    }
    
    // Tcheke si se Caissière (Nou tcheke tou pa imèl pou l pa janm echwe)
    if ($user->role === 'caissiere' || $user->role_id == 2 || $user->email === 'caisse@resto.com') {
        return redirect('/caisse/dashboard');
    }

    return redirect('/menu/1');
})->middleware(['auth'])->name('dashboard');
/*
|--------------------------------------------------------------------------
| CLIENT (PIBLIK)
|--------------------------------------------------------------------------
*/
Route::get('/menu/{table}', [ClientController::class, 'menu']);
Route::post('/cart/add', [ClientController::class, 'addToCart']);
Route::post('/checkout', [ClientController::class, 'checkout']);
Route::get('/menu/{tableId}/plat/{id}', [ClientController::class, 'showPlat'])->name('client.plat.show');
Route::get('/waiting/{tableId}/{commandeId?}', [WaitingController::class, 'index'])->name('client.waiting');
Route::post('/commander', [OrderController::class, 'store']);
Route::post('/payment', [PaymentController::class, 'pay']);


/*
|--------------------------------------------------------------------------
| ADMIN (SÈLMAN ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/ventes', [VentesController::class, 'index']);
    Route::post('/commandes/archive/{id}', [VentesController::class, 'archive']);
    Route::post('/commandes/cloturer-journee', [VentesController::class, 'cloturerJournee']);
    
    Route::resource('plats', PlatController::class);
    Route::resource('tables', TableController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('employes', \App\Http\Controllers\Admin\EmployeController::class);
    Route::delete('/commandes/{id}', function ($id) {
        Commande::destroy($id);
        return back();
    });
});


/*
|--------------------------------------------------------------------------
| CAISSE & CUISINE (ADMIN AK CAISSIERE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/caisse/dashboard', function () {
        return view('caisse.dashboard');
    });

    Route::get('/cuisine', [KitchenController::class, 'index']);
    Route::post('/cuisine/update/{id}', [KitchenController::class, 'updateStatus']);
    
    Route::get('/facture/{id}', [FactureController::class, 'generate']);
});

require __DIR__.'/auth.php';