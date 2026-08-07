<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {

        Schema::table('paiements', function (Blueprint $table) {


            $table->enum(
                'mode_paiement',
                [
                    'Espèces',
                    'Carte',
                    'MonCash',
                    'NatCash',
                    'Virement'
                ]
            )
            ->after('montant');


            $table->string('caissier')
                ->nullable()
                ->after('mode_paiement');


            $table->string('numero_facture')
                ->nullable()
                ->after('caissier');


        });

    }




    public function down(): void
    {

        Schema::table('paiements', function (Blueprint $table) {


            $table->dropColumn([

                'mode_paiement',

                'caissier',

                'numero_facture'

            ]);


        });

    }


};