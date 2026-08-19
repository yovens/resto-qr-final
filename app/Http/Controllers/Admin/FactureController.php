<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Barryvdh\DomPDF\Facade\Pdf; // Si ou vle PDF

class FactureController extends Controller
{
    public function show(Commande $commande)
    {
        // Retire 'user' si relasyon sa pa egziste nan model la
        $commande->load(['items.plat', 'table']);
        
        return view('admin.facture.show', compact('commande'));
    }

    // Si ou vle telechaje PDF
    public function download(Commande $commande)
    {
        $commande->load(['items.plat', 'table', 'user']);
        
        $pdf = Pdf::loadView('admin.facture.pdf', compact('commande'));
        
        return $pdf->download('facture-'.$commande->id.'.pdf');
    }
}