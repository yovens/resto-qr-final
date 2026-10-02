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






PS C:\Users\Youvens\Desktop\resto-qr> php artisan tinker
Psy Shell v0.12.23 (PHP 8.5.6 — cli) by Justin Hileman
New PHP manual is available (latest: 3.0.7). Update with `doc --update-manual`
> $user = App\Models\User::where('email', 'client@test.com')->first();                                     

= App\Models\User {#8767
    id: 3,
    name: "Test Client",
    email: "client@test.com",
    email_verified_at: null,
    #password: "\$2y\$12\$pORUp8bYis4KXqjIALtm5O0dV9RSsHcxZ2cPVw5Q3ThYZ/zYgfjfq",
    #remember_token: null,
    created_at: "2026-10-02 19:23:14",
    updated_at: "2026-10-02 19:23:14",
    role: "client",
    telephone: "50900000000",
  }

> $token = $user->createToken('flutter-test')->plainTextToken;                                             

= "3|IcXKDwmswjtxQB8Rh73htluDS4utSboh5tYsnL2Td377fdb1"

> $token;                                                                                                  

= "3|IcXKDwmswjtxQB8Rh73htluDS4utSboh5tYsnL2Td377fdb1"

>   





class AppConfig {
  AppConfig._();

  /// Android Emulator
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1';

  /// Si w itilize yon physical phone, pita n ap mete
  /// IP PC ou isit la, egzanp:
  /// http://192.168.1.10:8000/api/v1

  static const String appName = 'Resto Kay-Y';

  static const Duration requestTimeout = Duration(seconds: 15);

  static const Duration connectionTimeout = Duration(seconds: 10);
}
*/