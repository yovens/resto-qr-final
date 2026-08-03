<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {

            $table->id();

            // Numéro table
            $table->integer('numero')->unique();

            // QR link (optionnel)
            $table->string('qr_code')->nullable();

            // disponible / occupée
            $table->enum(
                'statut',
                [
                    'disponible',
                    'occupee'
                ]
            )->default('disponible');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'restaurant_tables'
        );
    }
};