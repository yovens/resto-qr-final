<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise'); // Non konpayi an
            $table->string('nom_contact');    // Non moun ou pale avè l la
            $table->string('email')->unique();
            $table->string('telephone');
            $table->text('adresse')->nullable();
            $table->string('produits_fournis')->nullable(); // Ki pwodwi li vann yo (eg: Viande, Boissons, Légumes)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};