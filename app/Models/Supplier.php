<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_entreprise',
        'nom_contact',
        'email',
        'telephone',
        'adresse',
        'produits_fournis'
    ];
}