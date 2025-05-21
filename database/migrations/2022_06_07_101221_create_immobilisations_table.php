<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmobilisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immobilisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_structure');
            $table->string('num_bc')->unique();
            $table->year('exercice')->index();
            $table->text('intitule')->nullable();
            $table->string('code_gestion')->nullable()->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->boolean('flag_valide')->nullable()->default(0);
            $table->boolean('flag_valide_stock')->nullable()->default(0);
            $table->boolean('flag_valide_r_cmp')->nullable()->default(0);
            $table->boolean('flag_valide_r_l')->nullable()->default(0);
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('exercice')->references('exercice')->on('exercices');
            $table->foreign('code_structure')->references('code_structure')->on('structures');
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
        Schema::dropIfExists('immobilisations');
    }
}
