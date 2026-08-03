<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        'restaurant_table_id',
        'total',
        'statut',
         'archived',
         'note',
    ];

    public function table()
    {
        return $this->belongsTo(
            RestaurantTable::class,
            'restaurant_table_id'
        );
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }
}




