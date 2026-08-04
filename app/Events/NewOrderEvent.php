<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande->load([
            'table',
            'items.plat'
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('kitchen');
    }

    public function broadcastAs()
    {
        return 'new-order';
    }

    public function broadcastWith()
    {
        return [
            'commande' => [
                'id' => $this->commande->id,
                'table' => [
                    'numero' => $this->commande->table->numero
                ],
                'items' => $this->commande->items->map(function ($item) {
                    return [
                        'quantite' => $item->quantite,
                        'plat' => [
                            'nom' => $item->plat->nom
                        ]
                    ];
                })->values()
            ]
        ];
    }
}