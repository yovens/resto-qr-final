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
      Schema::create('commandes', function (Blueprint $table) {
    $table->id();

    $table->foreignId('restaurant_table_id')
        ->constrained('restaurant_tables')
        ->onDelete('cascade');

    $table->decimal('total', 10, 2)->default(0);

    $table->enum('statut', [
        'nouvelle',
        'en_preparation',
        'prete',
        'servie',
        'payee'
    ])->default('nouvelle');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
