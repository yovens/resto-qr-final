


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
       
  Schema::create('plats', function (Blueprint $table) {
    $table->id();

    // INFO PRINCIPALE
    $table->string('nom');
    $table->text('description')->nullable();

    // PRIX
    $table->decimal('prix', 10, 2);
    $table->decimal('prix_promo', 10, 2)->nullable(); // 🔥 promo

    // MEDIA
    $table->string('image')->nullable();

    // STATUS
    $table->boolean('disponible')->default(true);
    $table->boolean('is_populaire')->default(false); // 🔥 top plat

    // RELATIONS
    $table->foreignId('category_id')->constrained()->onDelete('cascade');

    // STATS VENTES
    $table->integer('total_vendu')->default(0); // 🔥 dashboard

    // TIMING CUISINE
    $table->integer('temps_preparation')->nullable(); // en minutes

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plats');
    }
};


