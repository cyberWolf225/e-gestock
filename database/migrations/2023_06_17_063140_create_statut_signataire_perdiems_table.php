<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutSignatairePerdiemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_signataire_perdiems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('signataire_perdiems_id')->index();
            $table->unsignedBigInteger('type_statut_sign_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('signataire_perdiems_id')->references('id')->on('signataire_perdiems');
            $table->foreign('type_statut_sign_id')->references('id')->on('type_statut_signataires');
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
        Schema::dropIfExists('statut_signataire_perdiems');
    }
}
