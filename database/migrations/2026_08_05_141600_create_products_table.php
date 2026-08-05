<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('unite'); // eg: kg, unité, litres, btes
            $table->decimal('quantite_actuelle', 10, 2)->default(0);
            $table->decimal('seuil_alerte', 10, 2)->default(5); // Nivo pou rapèl/alèt
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};