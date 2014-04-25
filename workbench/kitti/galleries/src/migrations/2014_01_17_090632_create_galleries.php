<?php

use Illuminate\Database\Migrations\Migration;

class CreateGalleries extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galleries', function($table) {
            $table->increments('id');
            $table->integer('app_id', false)->unsigned();
            $table->foreign('app_id')->references('id')->on('applications');
            $table->integer('content_id', false);
            $table->string('content_type');
            $table->string('name',255);
            $table->string('description',255)->nullable();
            $table->string('picture',100)->nullable();
            $table->integer('like', false)->unsigned()->default(0);
            $table->integer('created_at', false);
            $table->integer('updated_at', false);
            $table->integer('published_at', false);
            $table->integer('deleted_at', false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('galleries');
    }

}