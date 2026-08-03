<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Commande;



class NouvelleCommande implements ShouldBroadcast
{
    public $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande->load('items.plat');
    }

    public function broadcastOn()
    {
        return new Channel('kitchen');
    }

    public function broadcastAs()
    {
        return 'nouvelle-commande';
    }
}