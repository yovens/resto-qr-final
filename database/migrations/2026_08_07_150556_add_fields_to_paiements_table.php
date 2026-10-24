<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Ces colonnes existent déjà dans la migration de création de « paiements ».
 * On ne les ajoute que si elles manquent : sinon une installation neuve
 * (php artisan migrate) plante sur « duplicate column ».
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements', 'mode_paiement')) {
                $table->enum('mode_paiement', ['Espèces', 'Carte', 'MonCash', 'NatCash', 'Virement'])->after('montant');
            }
            if (!Schema::hasColumn('paiements', 'caissier')) {
                $table->string('caissier')->nullable()->after('mode_paiement');
            }
            if (!Schema::hasColumn('paiements', 'numero_facture')) {
                $table->string('numero_facture')->nullable()->after('caissier');
            }
        });
    }

    public function down(): void
    {
        // Rien : ces colonnes appartiennent à la migration de création.
    }
};
