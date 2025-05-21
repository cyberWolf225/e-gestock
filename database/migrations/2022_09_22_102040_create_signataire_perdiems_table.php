<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignatairePerdiemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signataire_perdiems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profil_fonctions_id')->index();
            $table->unsignedBigInteger('perdiems_id')->index();
            $table->boolean('flag_actif')->default(1);
            $table->foreign('perdiems_id')->references('id')->on('perdiems');
            $table->foreign('profil_fonctions_id')->references('id')->on('profil_fonctions');
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
        Schema::dropIfExists('signataire_perdiems');
    }
}
