<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIprofileInformationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iprofile__information_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            // Your translatable fields
            $table->string('title');
            $table->text('description');

            $table->integer('information_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['information_id', 'locale']);
            $table->foreign('information_id')->references('id')->on('iprofile__information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iprofile__information_translations', function (Blueprint $table) {
            $table->dropForeign(['information_id']);
        });
        Schema::dropIfExists('iprofile__information_translations');
    }
}
