<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComptabilisationEcrituresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comptabilisation_ecritures', function (Blueprint $table) {
            $table->id();
            $table->string('type_piece');
            $table->string('reference_piece');
            $table->string('compte');
            $table->string('code_gestion');
            $table->year('exercice');
            $table->string('montant');
            $table->date('date_transaction');
            $table->string('mle');
            $table->string('code_structure');
            $table->string('code_section');
            $table->integer('ref_depot');
            $table->boolean('acompte')->default(0);
            $table->boolean('flag_comptabilisation')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comptabilisation_ecritures');
    }
}
