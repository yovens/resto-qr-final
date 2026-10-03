<?php

namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\RestaurantTable;

class MenuController extends Controller
{
    /**
     * MENU
     * GET /api/v1/menu
     */
    public function index()
    {
        $categories = Category::with([
            'plats' => function ($query) {
                $query
                    ->where('disponible', true)
                    ->orderByDesc('is_populaire')
                    ->orderBy('nom');
            }
        ])
        ->orderBy('id')
        ->get();

        $categories = $categories->map(function ($category) {

            $category->plats->transform(function ($plat) {

                // Prix effectif
                $plat->prix_effectif =
                    $plat->prix_promo ?? $plat->prix;

                // Images situées dans public/images
                if ($plat->image) {
                  $plat->image_url = url(
    'api/v1/images/' . ltrim($plat->image, '/')
);
                } else {
                    $plat->image_url = null;
                }

                return $plat;
            });

            return $category;
        });

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * TABLE
     * GET /api/v1/tables/{id}
     */
    public function table($id)
    {
        $table = RestaurantTable::find($id);

        if (!$table) {
            return response()->json([
                'message' => 'Table introuvable.',
            ], 404);
        }

        return response()->json([
            'table' => $table,
        ]);
    }
}
