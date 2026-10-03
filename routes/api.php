<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;


/*
|--------------------------------------------------------------------------
| Resto Kay-Y API
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {


Route::get('/images/{filename}', function ($filename) {

    $filename = basename($filename);

    $path = public_path('images/' . $filename);

    if (!is_file($path)) {
        return response()->json([
            'message' => 'Image introuvable.',
        ], 404);
    }

    $content = file_get_contents($path);

    return response($content, 200, [
        'Content-Type' => 'image/webp',
        'Content-Length' => strlen($content),
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control' => 'no-cache',
    ]);

})->where('filename', '.*');


    /*
    |--------------------------------------------------------------------------
    | AUTH PUBLIC
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/register',
        [AuthController::class, 'register']
    );

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );


    /*
    |--------------------------------------------------------------------------
    | MENU PUBLIC
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/menu',
        [MenuController::class, 'index']
    );

    Route::get(
        '/tables/{id}',
        [MenuController::class, 'table']
    );


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        | User
        */

        Route::get(
            '/me',
            [AuthController::class, 'me']
        );

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );


        /*
        | Commandes
        */

        Route::get(
            '/commandes',
            [OrderController::class, 'index']
        );

        Route::post(
            '/commandes',
            [OrderController::class, 'store']
        );

        Route::get(
            '/commandes/{id}',
            [OrderController::class, 'show']
        );

        Route::post(
            '/commandes/{id}/annuler',
            [OrderController::class, 'cancel']
        );
    });
});