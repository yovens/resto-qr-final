<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commande_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('commande_id')
        ->constrained('commandes')
        ->onDelete('cascade');

    $table->foreignId('plat_id')
        ->constrained('plats')
        ->onDelete('cascade');

    $table->integer('quantite');

    $table->decimal('prix', 10, 2); // prix au moment de la commande

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_items');
    }
};
