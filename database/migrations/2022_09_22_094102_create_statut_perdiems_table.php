<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutPerdiemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_perdiems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perdiems_id')->index();
            $table->unsignedBigInteger('type_statut_perdiems_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('type_statut_perdiems_id')->references('id')->on('type_statut_perdiems');
            $table->foreign('perdiems_id')->references('id')->on('perdiems');
            $table->foreign('profils_id')->references('id')->on('profils');
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
        Schema::dropIfExists('statut_perdiems');
    }
}
