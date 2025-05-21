<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('ref_articles')->unique();
            $table->text('design_article');
            $table->boolean('flag_actif')->default(1);
            $table->unsignedBigInteger('ref_fam')->index()->nullable(); 
            $table->unsignedBigInteger('type_articles_id')->index()->nullable(); 
            $table->unsignedBigInteger('code_unite')->index()->nullable(); 
            $table->unsignedBigInteger('ref_taxe')->index()->nullable(); 
            $table->foreign('ref_fam')->references('ref_fam')->on('familles');
            $table->foreign('type_articles_id')->references('id')->on('type_articles');
            $table->foreign('code_unite')->references('code_unite')->on('unites');
            $table->foreign('ref_taxe')->references('ref_taxe')->on('taxes');
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
        Schema::dropIfExists('articles');
    }
}
