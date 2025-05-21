<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_consolides_id')->index();
            $table->unsignedBigInteger('livraisons_id')->index();
            $table->foreign('livraisons_id')->references('id')->on('livraisons');
            $table->foreign('demande_consolides_id')->references('id')->on('demande_consolides');
            $table->integer('qte');
            $table->unsignedBigInteger('prixu');
            $table->unsignedBigInteger('montant');
            $table->unsignedBigInteger('profils_id')->index();   
            $table->foreign('profils_id')->references('id')->on('profils');
            $table->integer('qte_recue')->default(0);
            $table->boolean('flag_reception')->default(0);
            $table->datetime('date_reception')->nullable();
            $table->text('commentaire')->nullable();
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
        Schema::dropIfExists('distributions');
    }
}
