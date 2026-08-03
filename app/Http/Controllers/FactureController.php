<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;

class FactureController extends Controller
{
    public function generate($id)
    {
        $commande = Commande::with('items.plat', 'table')
            ->findOrFail($id);

        return view('facture.show', compact('commande'));
    }
}