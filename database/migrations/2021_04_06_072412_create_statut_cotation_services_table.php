<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutCotationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_cotation_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotation_services_id')->index();
            $table->unsignedBigInteger('type_statut_cot_services_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->unsignedBigInteger('profils_id')->index();
            $table->text('commentaire')->nullable();
            $table->foreign('cotation_services_id')->references('id')->on('cotation_services');
            $table->foreign('type_statut_cot_services_id')->references('id')->on('type_statut_cotation_services');
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
        Schema::dropIfExists('statut_cotation_services');
    }
}
