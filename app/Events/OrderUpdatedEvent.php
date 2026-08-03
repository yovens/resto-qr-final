<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function broadcastOn(): Channel
    {
        // Nou itilize yon kanal piblik ki baze sou ID kòmand lan
        // Sa pèmèt kliyan an "koute" kòmand espesifik li a
        return new Channel('order.' . $this->commande->id);
    }
    
    // Opsyonèl: personnalize non evènman an
    public function broadcastAs(): string
    {
        return 'status.updated';
    }
}