<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisation_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisations_id')->index();
            $table->unsignedBigInteger('ref_fam')->index();
            $table->boolean('flag_actif')->nullable();
            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
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
        Schema::dropIfExists('organisation_articles');
    }
}
