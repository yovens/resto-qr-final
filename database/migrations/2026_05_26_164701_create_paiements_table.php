<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {

        Schema::create('paiements', function (Blueprint $table) {


            $table->id();


            /*
            Relation commande
            */

            $table->foreignId('commande_id')
                ->constrained()
                ->cascadeOnDelete();



            /*
            Montant payé
            */

            $table->decimal(
                'montant',
                10,
                2
            );



            /*
            Mode paiement
            */

            $table->enum(
                'mode_paiement',
                [

                    'Espèces',

                    'Carte',

                    'MonCash',

                    'NatCash',

                    'Virement'

                ]
            );



            /*
            Caissier connecté
            */

            $table->string(
                'caissier'
            )
            ->nullable();



            /*
            Numéro facture
            */

            $table->string(
                'numero_facture'
            )
            ->nullable();



            $table->timestamps();


        });

    }




    public function down(): void
    {

        Schema::dropIfExists('paiements');

    }

};