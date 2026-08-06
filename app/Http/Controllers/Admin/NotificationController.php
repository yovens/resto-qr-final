<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $stockAlerts = Product::whereColumn('quantite_actuelle', '<=', 'seuil_alerte')->get();

        $employes = [];
        if (\Schema::hasTable('employes')) {
            $employes = DB::table('employes')->get();
        }

        return view('admin.notifications.index', compact('stockAlerts', 'employes'));
    }
}