<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'unite', 'quantite_actuelle', 'seuil_alerte'];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}