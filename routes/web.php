<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeController;
use App\Http\Controllers\Admin\FactureController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VentesController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\WaitingController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Plat\PlatController;
use App\Http\Controllers\Table\TableController;
use App\Http\Middleware\CheckRole;
use App\Models\Commande;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rôles
|--------------------------------------------------------------------------
| admin                        → tout
| caissier / caissiere         → caisse (+ écran cuisine)
| serveur / serveuse           → caisse : prise de commande, encaissement (+ écran cuisine)
| cuisinier / cuisine          → écran cuisine
*/
$rolesCaisse  = 'role:caissier,caissiere,serveur,serveuse';
$rolesCuisine = 'role:cuisinier,cuisine,cuisiniere,caissier,caissiere,serveur,serveuse';

/*
|--------------------------------------------------------------------------
| Accueil & redirection après connexion
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/login'));

Route::get('/dashboard', fn () => redirect(CheckRole::accueil(auth()->user())))
    ->middleware('auth')
    ->name('dashboard');

// Compte sans rôle : on le déconnecte et on revient à la page de connexion
// (sinon la personne reste bloquée sur une page 403 sans pouvoir changer de compte).
Route::get('/acces-refuse', function (\Illuminate\Http\Request $request) {
    $email = $request->user()?->email;
    auth()->guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->withInput(['email' => $email])
        ->withErrors(['email' => "Le compte $email n'a pas de rôle (admin, caissier, serveur ou cuisinier). Connectez-vous avec un autre compte ou demandez à l'administrateur."]);
})->middleware('auth')->name('acces.refuse');

/*
|--------------------------------------------------------------------------
| CLIENT (public, via le QR code de la table)
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:300,1')->group(function () {
    Route::get('/menu/{table}', [ClientController::class, 'menu'])->name('client.menu');
    Route::get('/menu/{tableId}/plat/{id}', [ClientController::class, 'showPlat'])->name('client.plat.show');
    Route::get('/menu/{tableId}/commande/{commandeId}/statut', [ClientController::class, 'statut'])
        ->whereNumber(['tableId', 'commandeId'])
        ->name('client.commande.statut');
    Route::get('/waiting/{tableId}/{commandeId?}', [WaitingController::class, 'index'])->name('client.waiting');

    Route::post('/cart/add', [ClientController::class, 'addToCart']);
});

// Envoi de commande : limité pour éviter les doubles clics / abus
Route::post('/checkout', [ClientController::class, 'checkout'])
    ->middleware('throttle:20,1')
    ->name('client.checkout');

/*
|--------------------------------------------------------------------------
| ADMIN / MANAGER
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/facture/{commande}', [FactureController::class, 'show'])->name('facture.show');

    // Ventes & clôture
    Route::get('/ventes', [VentesController::class, 'index'])->name('admin.ventes');
    Route::post('/commandes/archive/{id}', [VentesController::class, 'archive'])->name('admin.commandes.archive');
    Route::post('/commandes/cloturer-journee', [VentesController::class, 'cloturerJournee'])->name('admin.cloturer');
    Route::delete('/commandes/{id}', function ($id) {
        $commande = Commande::findOrFail($id);
        if ($commande->statut === 'payee') {
            return back()->with('error', 'Une commande payée ne peut pas être supprimée (elle est dans la comptabilité).');
        }
        $commande->delete();
        return back()->with('success', 'Commande supprimée.');
    })->name('admin.commandes.destroy');

    // Restaurant
    Route::resource('plats', PlatController::class)->except(['show']);
    Route::resource('tables', TableController::class)->only(['index', 'store']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
    Route::resource('stock', StockController::class)->except(['show']);
    Route::get('/stock-mouvement', [StockController::class, 'mouvementForm']);
    Route::post('/stock-mouvement', [StockController::class, 'mouvementStore']);
    Route::resource('suppliers', SupplierController::class);

    // Équipe
    Route::resource('employes', EmployeController::class);
    Route::resource('users', UserController::class);

    // Divers
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/profile', [AdminProfileController::class, 'edit']);
    Route::put('/profile', [AdminProfileController::class, 'update']);

    // Sauvegardes de la base de données
    Route::get('/backups', [BackupController::class, 'index'])->name('admin.backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('admin.backups.store');
    Route::get('/backups/{fichier}', [BackupController::class, 'download'])
        ->where('fichier', '[A-Za-z0-9_.\-]+')
        ->name('admin.backups.download');
    Route::delete('/backups/{fichier}', [BackupController::class, 'destroy'])
        ->where('fichier', '[A-Za-z0-9_.\-]+')
        ->name('admin.backups.destroy');
});

/*
|--------------------------------------------------------------------------
| CAISSE
|--------------------------------------------------------------------------
*/
Route::prefix('caisse')->middleware(['auth', $rolesCaisse])->group(function () {

    Route::get('/dashboard', [CaisseController::class, 'index'])->name('caisse.dashboard');

    // Commandes — « nouvelle » doit être déclarée AVANT les routes avec paramètre
    Route::get('/commandes', [CaisseController::class, 'commandes'])->name('caisse.commandes');
    Route::get('/commandes/nouvelle', [CaisseController::class, 'nouvelleCommande'])->name('caisse.commandes.create');
    Route::post('/commandes', [CaisseController::class, 'storeCommande'])
        ->middleware('throttle:30,1')
        ->name('caisse.commandes.store');
    Route::get('/commandes/count', [CaisseController::class, 'countPretes'])->name('caisse.commandes.count');

    // Encaissement
    Route::get('/encaisser/{id}', [CaisseController::class, 'encaisser'])->whereNumber('id')->name('caisse.encaisser');
    Route::post('/paiement', [CaisseController::class, 'paiement'])->name('caisse.paiement');
    Route::get('/facture/{id}', [CaisseController::class, 'facture'])->whereNumber('id')->name('caisse.facture');

    // Historique
    Route::get('/paiements', [CaisseController::class, 'historique'])->name('caisse.paiements');
    Route::get('/historique', [CaisseController::class, 'historique'])->name('caisse.historique');

    // API (actualisation automatique)
    Route::get('/api/dashboard-data', [CaisseController::class, 'dashboardData'])->name('caisse.api.dashboard');
    Route::get('/api/stats', [CaisseController::class, 'stats'])->name('caisse.api.stats');
    Route::get('/api/search', [CaisseController::class, 'search'])->name('caisse.api.search');
    Route::get('/api/count-pretes', [CaisseController::class, 'countPretes'])->name('caisse.count-pretes');
});

/*
|--------------------------------------------------------------------------
| CUISINE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', $rolesCuisine])->group(function () {
    Route::get('/cuisine', [KitchenController::class, 'index'])->name('cuisine.index');
    Route::get('/cuisine/data', [KitchenController::class, 'data'])->name('cuisine.data');
    Route::post('/cuisine/update/{id}', [KitchenController::class, 'updateStatus'])->whereNumber('id')->name('cuisine.update');
});

require __DIR__.'/auth.php';