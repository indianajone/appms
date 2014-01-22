<?php

use Illuminate\Database\Migrations\Migration;

class CreateMedias extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('medias', function($table) {
            $table->increments('media_id');
            $table->integer('app_id', false);
            $table->integer('member_id', false);
            $table->integer('gallery_id', false);
            $table->string('name', 255);
            $table->string('description', 255);
            $table->string('path', 100);
            $table->string('filename', 100);
            $table->string('type', 10);
            $table->string('latitude', 25);
            $table->string('longtitude', 25);
            $table->integer('like', false);
            $table->timestamps();
            $table->boolean('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('medias');
    }

}
