<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plat extends Model
{
    protected $fillable = [
        'nom',
        'prix',
        'description',
        'image',
        'disponible',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function commandeItems()
    {
        return $this->hasMany(CommandeItem::class);
    }
}
