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