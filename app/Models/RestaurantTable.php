<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = [
        'numero',
        'qr_code'
    ];

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}