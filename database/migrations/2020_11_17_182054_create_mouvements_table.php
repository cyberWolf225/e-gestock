<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMouvementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_mouvements_id')->index();
            $table->unsignedBigInteger('magasin_stocks_id')->index();
            $table->unsignedBigInteger('profils_id')->index();
            $table->integer('qte');
            $table->integer('prix_unit')->nullable();
            $table->integer('montant_ht')->nullable();
            $table->string('taxe')->nullable();
            $table->integer('montant_ttc')->nullable();
            $table->datetime('date_mouvement')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('type_mouvements_id')->references('id')->on('type_mouvements');
            $table->foreign('magasin_stocks_id')->references('id')->on('magasin_stocks');
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
        Schema::dropIfExists('mouvements');
    }
}
