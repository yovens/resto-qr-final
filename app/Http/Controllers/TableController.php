<?php
namespace App\Http\Controllers\Admin;
namespace App\Http\Controllers;

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

        RestaurantTable::create($request->all());

        return back()->with('success', 'Table créée');
    }
}



/*
|--------------------------------------------------------------------------
| CUISINE  Microsoft Windows [Version 10.0.19045.6466]
(c) Microsoft Corporation. All rights reserved.

C:\Windows\system32>cd /d "C:\Users\My PC\Desktop\resto-qr"

C:\Users\My PC\Desktop\resto-qr>php artisan reverb:start --port=9000

   INFO  Starting server on 0.0.0.0:9000 (127.0.0.1).

    php artisan reverb:start --port=8086
php artisan serve --host=0.0.0.0 --port=8000
    npm run dev

    php artisan queue:work
    php artisan queue:work --verbose
|--------------------------------------------------------------------------
*/