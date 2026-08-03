<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderReadyEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function broadcastOn()
    {
        return new Channel(
            'commande.'.$this->commande->id
        );
    }

    public function broadcastAs()
    {
        return 'order-ready';
    }

    public function broadcastWith()
    {
        return [

            'id'=>$this->commande->id,

            'statut'=>$this->commande->statut

        ];
    }
}