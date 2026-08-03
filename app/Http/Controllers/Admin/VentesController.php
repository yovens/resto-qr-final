<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commande;

class VentesController extends Controller
{
   public function index(Request $request)
{
    // Nou filtre sèlman sou kòmand ki achive (si se sa ou vle wè nan istwa a)
    $query = Commande::with('table', 'items.plat')->where('archived', true);

    // Itilize 'month' jan li ye nan HTML la
    if ($request->filled('month')) {
        $query->whereMonth('created_at', $request->month);
    }

    // Itilize 'year' jan li ye nan HTML la
    if ($request->filled('year')) {
        $query->whereYear('created_at', $request->year);
    }

    $commandes = $query->latest()->get();
    $total = $commandes->sum('total');

    // Statistik par mwa
    $parMois = Commande::selectRaw('MONTH(created_at) as mois, SUM(total) as total')
        ->where('archived', true) // Asire nou pran sa ki achive sèlman
        ->whereYear('created_at', $request->year ?? date('Y'))
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();

    return view('admin.ventes', compact('commandes', 'total', 'parMois'));
}
    public function archive($id)
{
    $commande = Commande::findOrFail($id);
    $commande->update(['archived' => true]);

    return back()->with('success', 'Commande archivée avec succès !');
}
public function cloturerJournee()
{
    // Nou chanje estati tout kòmand ki "aktif" (archived = 0) pou yo tounen "archived" (1)
    \App\Models\Commande::where('archived', false)
                        ->update(['archived' => true]);

    return back()->with('success', 'Jounen an fèmen avèk siksè!');
}
}