<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantTable;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::all();

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|unique:restaurant_tables,numero'
        ]);

        RestaurantTable::create([
            'numero' => $request->numero
        ]);

        return back()->with('success', 'Table créée');
    }
}