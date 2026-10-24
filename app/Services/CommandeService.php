<?php

namespace App\Services;

use App\Events\NewOrderEvent;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Plat;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Création des commandes — une seule logique pour :
 *  - le client qui commande avec le QR code de sa table ;
 *  - la caisse qui prend la commande d'un client sans téléphone.
 *
 * Les prix sont toujours relus en base (jamais ceux envoyés par le navigateur).
 */
class CommandeService
{
    /** Taux de service ajouté au sous-total (10 %). */
    public const TAUX_SERVICE = 0.10;

    /**
     * @param  int    $tableId    restaurant_tables.id
     * @param  array  $lignes     [['plat_id' => 3, 'quantite' => 2], ...]
     * @param  string|null $note  note pour la cuisine
     * @throws ValidationException si un plat n'existe pas ou n'est plus disponible
     */
    public function creer(int $tableId, array $lignes, ?string $note = null): Commande
    {
        // Regroupe les doublons (même plat envoyé deux fois)
        $quantites = [];
        foreach ($lignes as $l) {
            $id = (int) ($l['plat_id'] ?? 0);
            $q  = (int) ($l['quantite'] ?? 0);
            if ($id > 0 && $q > 0) {
                $quantites[$id] = min(($quantites[$id] ?? 0) + $q, 99);
            }
        }

        if (!$quantites) {
            throw ValidationException::withMessages(['items' => 'Panye a vid. / La commande est vide.']);
        }

        $plats = Plat::whereIn('id', array_keys($quantites))->where('disponible', true)->get()->keyBy('id');

        $manquants = array_diff(array_keys($quantites), $plats->keys()->all());
        if ($manquants) {
            $noms = Plat::whereIn('id', $manquants)->pluck('nom')->implode(', ') ?: 'Un plat';
            throw ValidationException::withMessages([
                'items' => $noms . ' pa disponib ankò. / n\'est plus disponible.',
            ]);
        }

        $commande = DB::transaction(function () use ($tableId, $quantites, $plats, $note) {
            $sousTotal = 0;
            foreach ($quantites as $platId => $q) {
                $sousTotal += self::prix($plats[$platId]) * $q;
            }

            $commande = Commande::create([
                'restaurant_table_id' => $tableId,
                'total'               => $sousTotal + self::service($sousTotal),
                'statut'              => 'nouvelle',
                'note'                => $note !== null && trim($note) !== '' ? trim($note) : null,
                'archived'            => false,
            ]);

            foreach ($quantites as $platId => $q) {
                CommandeItem::create([
                    'commande_id' => $commande->id,
                    'plat_id'     => $platId,
                    'quantite'    => $q,
                    'prix'        => self::prix($plats[$platId]),
                ]);
            }

            return $commande;
        });

        $commande->load(['table', 'items.plat']);

        // Si le serveur temps réel (Reverb) est arrêté, la commande reste enregistrée :
        // l'écran cuisine la verra au prochain rechargement.
        try {
            broadcast(new NewOrderEvent($commande));
        } catch (\Throwable $e) {
            report($e);
        }

        return $commande;
    }

    /** Prix effectif d'un plat (promotion si présente). */
    public static function prix(Plat $plat): float
    {
        return (float) ($plat->prix_promo ?: $plat->prix);
    }

    /** Montant du service pour un sous-total. */
    public static function service(float $sousTotal): float
    {
        return round($sousTotal * self::TAUX_SERVICE);
    }
}
