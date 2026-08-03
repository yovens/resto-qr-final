<?php

namespace App\Services;

use App\Models\Plat;

class CartService
{
    /**
     * Ajouter un plat au panier
     */
    public static function add(array $cart, int $platId, int $quantite = 1): array
    {
        // sécurité quantité
        if ($quantite < 1) {
            $quantite = 1;
        }

        // si déjà dans panier
        if (isset($cart[$platId])) {
            $cart[$platId]['quantite'] += $quantite;
            return $cart;
        }

        // récupérer plat depuis DB (source de vérité)
        $plat = Plat::findOrFail($platId);

        $cart[$platId] = [
            'plat_id'   => $plat->id,
            'nom'       => $plat->nom,
            'prix'      => (float) $plat->prix,
            'quantite'  => $quantite,
            'subtotal'  => (float) $plat->prix * $quantite,
        ];

        return $cart;
    }

    /**
     * Calcul total du panier
     */
    public static function total(array $cart): float
    {
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['prix'] * $item['quantite']);
        }

        return round($total, 2);
    }

    /**
     * Mettre à jour les sous-totaux (optionnel mais pro)
     */
    public static function refresh(array $cart): array
    {
        foreach ($cart as $id => $item) {
            $cart[$id]['subtotal'] =
                $item['prix'] * $item['quantite'];
        }

        return $cart;
    }

    /**
     * Supprimer un item
     */
    public static function remove(array $cart, int $platId): array
    {
        unset($cart[$platId]);
        return $cart;
    }

    /**
     * Vider panier
     */
    public static function clear(): array
    {
        return [];
    }
}