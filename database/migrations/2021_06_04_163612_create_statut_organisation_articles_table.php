<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatutOrganisationArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_organisation_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_articles_id')->index();
            $table->unsignedBigInteger('type_statut_org_articles_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->foreign('organisation_articles_id')->references('id')->on('organisation_articles');
            $table->foreign('type_statut_org_articles_id')->references('id')->on('type_statut_organisation_articles');
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
        Schema::dropIfExists('statut_organisation_articles');
    }
}
